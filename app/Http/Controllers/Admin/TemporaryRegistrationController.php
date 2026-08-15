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

    public function create(Request $request)
    {
        $users = User::where('role', 'resident')->get();
        $blocks = \App\Models\Block::with('floors.apartments')->get();
        
        $extendRegistration = null;
        if ($request->has('extend_id')) {
            $extendRegistration = TemporaryRegistration::find($request->extend_id);
        }
        
        return view('admin.temporary-registrations.create', compact('users', 'blocks', 'extendRegistration'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'guest_name' => 'nullable|required_without:user_id|string|max:255',
            'guest_phone' => 'nullable|required_without:user_id|regex:/^(0)[0-9]{9}$/|unique:users,phone',
            'guest_cccd' => 'nullable|required_without:user_id|digits:12',
            'guest_email' => 'nullable|email|max:255',
            'guest_dob' => 'nullable|date',
            'guest_gender' => 'nullable|in:male,female,other',
            'guest_hometown' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'apartment_id' => 'required|exists:apartments,id',
            'type' => 'required|in:residence,absence',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachments' => 'nullable|required_if:type,residence|array',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['attachments']);
            
            // Nếu là Tạm trú và không chọn user có sẵn, tạo User mới
            if ($request->type === 'residence' && !$request->user_id) {
                $user = User::create([
                    'name' => $request->guest_name,
                    'phone' => $request->guest_phone,
                    'cccd' => $request->guest_cccd,
                    'email' => $request->guest_email ?? ('guest_' . time() . '@domushub.local'),
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

            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $attachmentPaths[] = $file->store('temporary_registrations', 'public');
                }
            }
            $data['attachments'] = $attachmentPaths;

            $registration = TemporaryRegistration::create($data);

            // Đồng bộ trạng thái vào Resident
            $this->syncResidentStatus($registration);

            DB::commit();
            return redirect(portal_route('temporary-registrations.index'))
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
            'guest_phone' => 'nullable|required_without:user_id|regex:/^(0)[0-9]{9}$/|unique:users,phone,' . ($request->user_id ?? 'NULL'),
            'guest_cccd' => 'nullable|required_without:user_id|digits:12',
            'guest_email' => 'nullable|email|max:255',
            'guest_dob' => 'nullable|date',
            'guest_gender' => 'nullable|in:male,female,other',
            'guest_hometown' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'apartment_id' => 'required|exists:apartments,id',
            'type' => 'required|in:residence,absence',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['attachments']);

            if ($request->type === 'residence' && !$request->user_id) {
                $user = User::create([
                    'name' => $request->guest_name,
                    'phone' => $request->guest_phone,
                    'cccd' => $request->guest_cccd,
                    'email' => $request->guest_email ?? ('guest_' . time() . '@domushub.local'),
                    'password' => bcrypt('password123'),
                    'role' => 'resident',
                    'apartment_id' => $request->apartment_id,
                ]);
                $data['user_id'] = $user->id;
            } elseif (!$request->user_id) {
                throw new \Exception("Vui lòng chọn Cư dân cho đơn Tạm vắng.");
            }

            $attachmentPaths = $temporaryRegistration->attachments ?? [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $attachmentPaths[] = $file->store('temporary_registrations', 'public');
                }
                $data['attachments'] = $attachmentPaths;
            }

            $temporaryRegistration->update($data);

            if ($temporaryRegistration->status === 'approved') {
                $this->syncResidentStatus($temporaryRegistration);
            }

            DB::commit();
            return redirect(portal_route('temporary-registrations.index'))
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

            if ($temporaryRegistration->user) {
                $temporaryRegistration->user->notify(new \App\Notifications\TemporaryRegistrationStatusNotification($temporaryRegistration));
            }

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

        if ($temporaryRegistration->user) {
            $temporaryRegistration->user->notify(new \App\Notifications\TemporaryRegistrationStatusNotification($temporaryRegistration));
        }

        return redirect()->back()->with('success', 'Đã từ chối đơn đăng ký.');
    }

    public function destroy(TemporaryRegistration $temporaryRegistration)
    {
        if ($temporaryRegistration->attachment_path) {
            Storage::disk('public')->delete($temporaryRegistration->attachment_path);
        }
        if ($temporaryRegistration->attachments) {
            foreach ($temporaryRegistration->attachments as $path) {
                Storage::disk('public')->delete($path);
            }
        }
        $temporaryRegistration->delete();

        return redirect(portal_route('temporary-registrations.index'))
            ->with('success', 'Đã xóa đơn đăng ký.');
    }

    public function endEarly(TemporaryRegistration $temporaryRegistration)
    {
        if ($temporaryRegistration->status !== 'approved') {
            return redirect()->back()->with('error', 'Chỉ có thể kết thúc sớm các đăng ký đã được duyệt.');
        }

        DB::beginTransaction();
        try {
            $temporaryRegistration->update([
                'end_date' => now(),
            ]);

            $resident = \App\Models\Resident::where('user_id', $temporaryRegistration->user_id)
                ->where('apartment_id', $temporaryRegistration->apartment_id)
                ->first();

            if ($resident) {
                if ($temporaryRegistration->type === 'residence' && $resident->temporary_status === 'temporary') {
                    $resident->temporary_status = null;
                    $resident->save();
                } elseif ($temporaryRegistration->type === 'absence' && $resident->temporary_status === 'absent') {
                    $resident->temporary_status = null;
                    $resident->save();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Đã báo cáo kết thúc sớm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    private function syncResidentStatus(TemporaryRegistration $registration)
    {
        $targetUserId = $registration->user_id;

        // Nếu là đơn tạm trú và có tên khách
        if ($registration->type === 'residence' && $registration->guest_name) {
            $sponsor = \App\Models\User::find($registration->user_id);
            // Nếu người tạo đơn (sponsor) khác với tên khách, nghĩa là chủ hộ tạo dùm khách
            if ($sponsor && $sponsor->name !== $registration->guest_name) {
                // Tìm hoặc tạo User mới cho khách
                $guestUser = \App\Models\User::where(function($q) use ($registration) {
                    if ($registration->guest_phone) $q->where('phone', $registration->guest_phone);
                    if ($registration->guest_cccd) $q->orWhere('cccd', $registration->guest_cccd);
                })->first();

                if (!$guestUser) {
                    $guestUser = \App\Models\User::create([
                        'name' => $registration->guest_name,
                        'phone' => $registration->guest_phone,
                        'cccd' => $registration->guest_cccd,
                        'email' => $registration->guest_email ?? ('guest_' . time() . '@domushub.local'),
                        'password' => bcrypt('password123'),
                        'role' => 'resident',
                        'apartment_id' => $registration->apartment_id,
                    ]);
                }
                $targetUserId = $guestUser->id;
            }
        }

        // Tạo hoặc cập nhật resident
        $resident = \App\Models\Resident::firstOrCreate(
            [
                'user_id' => $targetUserId,
                'apartment_id' => $registration->apartment_id,
            ],
            [
                'relationship' => $registration->relationship ?? 'tenant',
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
        $user = \App\Models\User::find($targetUserId);
        if ($user && $user->apartment_id !== $registration->apartment_id) {
            $user->update(['apartment_id' => $registration->apartment_id]);
        }
    }
}
