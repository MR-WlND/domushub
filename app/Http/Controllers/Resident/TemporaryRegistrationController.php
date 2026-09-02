<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\TemporaryRegistration;
use App\Models\User;
use App\Notifications\NewTemporaryRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TemporaryRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Show registrations that belong to the user OR the user's apartment
        $query = TemporaryRegistration::with(['user', 'apartment', 'approver'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })
            ->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $registrations = $query->paginate(20)->withQueryString();

        return view('resident.temporary-registrations.index', compact('registrations'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user->apartment_id) {
            return redirect()->back()->with('error', 'Bạn chưa được liên kết với căn hộ nào.');
        }

        $extendRegistration = null;
        if ($request->has('extend_id')) {
            $extendRegistration = TemporaryRegistration::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })->find($request->extend_id);
        }

        return view('resident.temporary-registrations.create', compact('user', 'extendRegistration'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'guest_name' => 'nullable|required_if:type,residence|string|max:255',
            'guest_phone' => 'nullable|required_if:type,residence|regex:/^(0)[0-9]{9}$/',
            'guest_cccd' => 'nullable|required_if:type,residence|regex:/^[0-9]{9,12}$/',
            'guest_email' => 'nullable|email|max:255',
            'guest_dob' => 'nullable|date',
            'guest_gender' => 'nullable|in:male,female,other',
            'guest_hometown' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'type' => 'required|in:residence,absence',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'guest_name.required_if' => 'Vui lòng nhập họ và tên khách.',
            'guest_phone.required_if' => 'Vui lòng nhập số điện thoại.',
            'guest_phone.regex' => 'Số điện thoại không hợp lệ.',
            'guest_cccd.required_if' => 'Vui lòng nhập CCCD/CMND.',
            'guest_cccd.regex' => 'CCCD/CMND phải từ 9 đến 12 số.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'attachments.*.max' => 'Mỗi file đính kèm không được vượt quá 10MB.',
            'attachments.*.mimes' => 'File đính kèm phải là hình ảnh (jpg, jpeg, png) hoặc PDF.',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['attachments']);
            
            $data['apartment_id'] = $user->apartment_id;
            
            $data['user_id'] = $user->id;
            
            $data['status'] = 'pending';
            
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $attachmentPaths[] = $file->store('temporary_registrations', 'public');
                }
            }
            $data['attachments'] = $attachmentPaths;

            $registration = TemporaryRegistration::create($data);

            // Notify admins
            $admins = User::whereIn('role', ['admin', 'manager', 'receptionist'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewTemporaryRegistrationNotification($registration));
            }

            DB::commit();
            return redirect()->route('resident.temporary-registrations.index')
                ->with('success', 'Tạo đơn đăng ký thành công. Đang chờ Ban quản lý phê duyệt.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $temporaryRegistration = TemporaryRegistration::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })
            ->findOrFail($id);

        if (!in_array($temporaryRegistration->status, ['rejected', 'pending'])) {
            return redirect()->back()->with('error', 'Chỉ có thể chỉnh sửa đơn bị từ chối hoặc đang chờ duyệt.');
        }

        return view('resident.temporary-registrations.edit', compact('user', 'temporaryRegistration'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $registration = TemporaryRegistration::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })
            ->findOrFail($id);

        if (!in_array($registration->status, ['rejected', 'pending'])) {
            return redirect()->back()->with('error', 'Chỉ có thể chỉnh sửa đơn bị từ chối hoặc đang chờ duyệt.');
        }

        $request->validate([
            'guest_name' => 'nullable|required_if:type,residence|string|max:255',
            'guest_phone' => 'nullable|required_if:type,residence|regex:/^(0)[0-9]{9}$/',
            'guest_cccd' => 'nullable|required_if:type,residence|regex:/^[0-9]{9,12}$/',
            'guest_email' => 'nullable|email|max:255',
            'guest_dob' => 'nullable|date',
            'guest_gender' => 'nullable|in:male,female,other',
            'guest_hometown' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'type' => 'required|in:residence,absence',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'remove_attachments' => 'nullable|array',
        ], [
            'guest_name.required_if' => 'Vui lòng nhập họ và tên khách.',
            'guest_phone.required_if' => 'Vui lòng nhập số điện thoại.',
            'guest_phone.regex' => 'Số điện thoại không hợp lệ.',
            'guest_cccd.required_if' => 'Vui lòng nhập CCCD/CMND.',
            'guest_cccd.regex' => 'CCCD/CMND phải từ 9 đến 12 số.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'attachments.*.max' => 'Mỗi file đính kèm không được vượt quá 10MB.',
            'attachments.*.mimes' => 'File đính kèm phải là hình ảnh (jpg, jpeg, png) hoặc PDF.',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['attachments', 'remove_attachments']);
            
            $data['status'] = 'pending'; // Reset status
            $data['rejection_reason'] = null; // Clear rejection reason
            
            $currentAttachments = $registration->attachments ?? [];
            
            // Remove attachments if requested
            if ($request->has('remove_attachments')) {
                foreach ($request->remove_attachments as $pathToRemove) {
                    if (in_array($pathToRemove, $currentAttachments)) {
                        Storage::disk('public')->delete($pathToRemove);
                        $currentAttachments = array_diff($currentAttachments, [$pathToRemove]);
                    }
                }
            }
            
            // Upload new attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $currentAttachments[] = $file->store('temporary_registrations', 'public');
                }
            }
            
            // Re-index array
            $data['attachments'] = array_values($currentAttachments);

            $registration->update($data);

            // Notify admins
            $admins = User::whereIn('role', ['admin', 'manager', 'receptionist'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewTemporaryRegistrationNotification($registration));
            }

            DB::commit();
            return redirect()->route('resident.temporary-registrations.index')
                ->with('success', 'Cập nhật đơn đăng ký thành công. Đang chờ Ban quản lý phê duyệt lại.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $temporaryRegistration = TemporaryRegistration::with(['user', 'apartment', 'approver'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })
            ->findOrFail($id);

        return view('resident.temporary-registrations.show', compact('temporaryRegistration'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $temporaryRegistration = TemporaryRegistration::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })
            ->findOrFail($id);

        if ($temporaryRegistration->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể xóa đơn đang ở trạng thái chờ duyệt.');
        }

        if ($temporaryRegistration->attachment_path) {
            Storage::disk('public')->delete($temporaryRegistration->attachment_path);
        }
        
        if ($temporaryRegistration->attachments) {
            foreach ($temporaryRegistration->attachments as $path) {
                Storage::disk('public')->delete($path);
            }
        }
        
        $temporaryRegistration->delete();

        return redirect()->route('resident.temporary-registrations.index')
            ->with('success', 'Đã xóa đơn đăng ký.');
    }

    public function endEarly($id)
    {
        $user = Auth::user();
        $temporaryRegistration = TemporaryRegistration::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })
            ->findOrFail($id);

        if ($temporaryRegistration->status !== 'approved') {
            return redirect()->back()->with('error', 'Chỉ có thể kết thúc sớm các đăng ký đã được duyệt.');
        }

        DB::beginTransaction();
        try {
            $temporaryRegistration->update([
                'end_date' => now()->subDay(),
            ]);

            $targetUserId = $temporaryRegistration->user_id;
            if ($temporaryRegistration->type === 'residence' && $temporaryRegistration->guest_name) {
                $sponsor = \App\Models\User::find($temporaryRegistration->user_id);
                if ($sponsor && trim(mb_strtolower($sponsor->name)) !== trim(mb_strtolower($temporaryRegistration->guest_name))) {
                    $guestUser = \App\Models\User::where(function($q) use ($temporaryRegistration) {
                        if ($temporaryRegistration->guest_phone) $q->where('phone', $temporaryRegistration->guest_phone);
                        if ($temporaryRegistration->guest_cccd) $q->orWhere('cccd', $temporaryRegistration->guest_cccd);
                    })->first();
                    if ($guestUser) {
                        $targetUserId = $guestUser->id;
                    }
                }
            }

            $resident = \App\Models\Resident::where('user_id', $targetUserId)
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

            // Notify admins and receptionists
            $admins = \App\Models\User::whereIn('role', ['admin', 'manager', 'receptionist'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\TemporaryRegistrationEndedEarlyNotification($temporaryRegistration));
            }

            DB::commit();
            return redirect()->back()->with('success', 'Đã báo cáo kết thúc sớm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
