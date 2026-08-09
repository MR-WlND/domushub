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
        $apartments = Apartment::all();
        return view('admin.temporary-registrations.create', compact('users', 'apartments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'apartment_id' => 'required|exists:apartments,id',
            'type' => 'required|in:residence,absence',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except('attachment');
            
            // Nếu admin tạo, trạng thái mặc định là approved (như user yêu cầu)
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
        $apartments = Apartment::all();
        return view('admin.temporary-registrations.edit', compact('temporaryRegistration', 'users', 'apartments'));
    }

    public function update(Request $request, TemporaryRegistration $temporaryRegistration)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'apartment_id' => 'required|exists:apartments,id',
            'type' => 'required|in:residence,absence',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except('attachment');

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
        $resident = Resident::firstOrCreate([
            'user_id' => $registration->user_id,
            'apartment_id' => $registration->apartment_id,
        ]);

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
