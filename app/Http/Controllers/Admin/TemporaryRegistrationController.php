<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Resident;
use App\Models\TemporaryRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TemporaryRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = TemporaryRegistration::with(['user', 'apartment', 'approver'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $registrations = $query->paginate(20)->withQueryString();

        $now = now();
        $thirtyDaysLater = now()->addDays(30);

        $stats = [
            'total_new' => TemporaryRegistration::whereDate('created_at', today())->count(), // or just total pending if they prefer, but image says "Tổng yêu cầu mới"
            'pending' => TemporaryRegistration::where('status', 'pending')->count(),
            'expiring_soon' => TemporaryRegistration::whereNotNull('end_date')
                                ->whereBetween('end_date', [$now, $thirtyDaysLater])
                                ->count(),
            'approved' => TemporaryRegistration::where('status', 'approved')->count(),
        ];

        return view('admin.temporary-registrations.index', compact('registrations', 'stats'));
    }

    public function create()
    {
        $users = User::where('role', 'resident')->get();
        $blocks = \App\Models\Block::with('floors.apartments')->get();
        return view('admin.temporary-registrations.create', compact('users', 'blocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'guest_name' => 'nullable|required_without:user_id|string|max:255',
            'guest_phone' => 'nullable|required_without:user_id|string|max:20|unique:users,phone',
            'guest_cccd' => 'nullable|required_without:user_id|string|max:20',
            'apartment_id' => 'required|exists:apartments,id',
            'type' => 'required|in:residence,absence',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['attachment', 'guest_name', 'guest_phone', 'guest_cccd']);
            
            // Nếu là Tạm trú và không chọn user có sẵn, tạo User mới
            if ($request->type === 'residence' && !$request->user_id) {
                $user = User::create([
                    'name' => $request->guest_name,
                    'phone' => $request->guest_phone,
                    'cccd' => $request->guest_cccd,
                    'email' => 'guest_' . time() . '@domushub.local',
                    'password' => bcrypt('password123'), // Mật khẩu mặc định
                    'role' => 'resident',
                    'apartment_id' => $request->apartment_id,
                ]);
                $data['user_id'] = $user->id;
            } elseif (!$request->user_id) {
                throw new \Exception("Vui lòng chọn Cư dân cho đơn Tạm vắng.");
            }
            
            // Nếu admin tạo, trạng thái mặc định là approved
            $data['status'] = 'approved';
            $data['approved_by'] = Auth::id();

            if ($request->hasFile('attachment')) {
                $data['attachment_path'] = $request->file('attachment')->store('temporary_registrations', 'public');
            }

            $registration = TemporaryRegistration::create($data);

            // Đồng bộ trạng thái vào Resident
            $this->syncResidentStatus($registration);

            DB::commit();
            return redirect()->route('admin.temporary-registrations.index')
                ->with('success', 'Tạo đăng ký thành công và đã tự động duyệt.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function edit(TemporaryRegistration $temporaryRegistration)
    {
        $users = User::where('role', 'resident')->get();
        $blocks = \App\Models\Block::with('floors.apartments')->get();
        return view('admin.temporary-registrations.edit', compact('temporaryRegistration', 'users', 'blocks'));
    }

    public function update(Request $request, TemporaryRegistration $temporaryRegistration)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'guest_name' => 'nullable|required_without:user_id|string|max:255',
            'guest_phone' => 'nullable|required_without:user_id|string|max:20|unique:users,phone',
            'guest_cccd' => 'nullable|required_without:user_id|string|max:20',
            'apartment_id' => 'required|exists:apartments,id',
            'type' => 'required|in:residence,absence',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['attachment', 'guest_name', 'guest_phone', 'guest_cccd']);

            if ($request->type === 'residence' && !$request->user_id) {
                $user = User::create([
                    'name' => $request->guest_name,
                    'phone' => $request->guest_phone,
                    'cccd' => $request->guest_cccd,
                    'email' => 'guest_' . time() . '@domushub.local',
                    'password' => bcrypt('password123'),
                    'role' => 'resident',
                    'apartment_id' => $request->apartment_id,
                ]);
                $data['user_id'] = $user->id;
            } elseif (!$request->user_id) {
                throw new \Exception("Vui lòng chọn Cư dân cho đơn Tạm vắng.");
            }

            if ($request->hasFile('attachment')) {
                if ($temporaryRegistration->attachment_path) {
                    Storage::disk('public')->delete($temporaryRegistration->attachment_path);
                }
                $data['attachment_path'] = $request->file('attachment')->store('temporary_registrations', 'public');
            }

            $temporaryRegistration->update($data);

            if ($temporaryRegistration->status === 'approved') {
                $this->syncResidentStatus($temporaryRegistration);
            }

            DB::commit();
            return redirect()->route('admin.temporary-registrations.index')
                ->with('success', 'Cập nhật đăng ký thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function approve(TemporaryRegistration $temporaryRegistration)
    {
        if ($temporaryRegistration->status !== 'pending') {
            return redirect()->back()->with('error', 'Đơn đăng ký không ở trạng thái chờ duyệt.');
        }

        DB::beginTransaction();
        try {
            $temporaryRegistration->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'rejection_reason' => null,
            ]);

            $this->syncResidentStatus($temporaryRegistration);

            DB::commit();
            return redirect()->back()->with('success', 'Đã duyệt đơn đăng ký.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, TemporaryRegistration $temporaryRegistration)
    {
        if ($temporaryRegistration->status !== 'pending') {
            return redirect()->back()->with('error', 'Đơn đăng ký không ở trạng thái chờ duyệt.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $temporaryRegistration->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Đã từ chối đơn đăng ký.');
    }

    public function destroy(TemporaryRegistration $temporaryRegistration)
    {
        if ($temporaryRegistration->attachment_path) {
            Storage::disk('public')->delete($temporaryRegistration->attachment_path);
        }
        $temporaryRegistration->delete();

        return redirect()->route('admin.temporary-registrations.index')
            ->with('success', 'Đã xóa đơn đăng ký.');
    }

    private function syncResidentStatus(TemporaryRegistration $registration)
    {
        // Tạo hoặc cập nhật resident
        $resident = Resident::firstOrCreate(
            [
                'user_id' => $registration->user_id,
                'apartment_id' => $registration->apartment_id,
            ],
            [
                'relationship' => 'tenant',
                'start_date' => $registration->start_date,
            ]
        );

        if ($registration->type === 'residence') {
            $resident->temporary_status = 'temporary';
            $resident->start_date = $registration->start_date;
            $resident->end_date = $registration->end_date;
        } elseif ($registration->type === 'absence') {
            $resident->temporary_status = 'absent';
            // Vẫn giữ apartment hiện tại
        }

        $resident->save();

        // Đồng bộ apartment_id vào users table
        $user = User::find($registration->user_id);
        if ($user && $user->apartment_id !== $registration->apartment_id) {
            $user->update(['apartment_id' => $registration->apartment_id]);
        }
    }
}
