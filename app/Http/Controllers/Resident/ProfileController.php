<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
            // Silence migration exceptions
        }

        $user = Auth::user();
        $ownerApartmentIds = $this->getOwnerApartmentIds($user);
        $allApartmentIds = $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        // Fallback: nếu không có record trong bảng residents, dùng apartment_id trên user
        if (empty($allApartmentIds) && $user->apartment_id) {
            $allApartmentIds = [$user->apartment_id];
        }

        // Lấy căn hộ: ưu tiên owner, fallback apartment_id trực tiếp
        if (!empty($ownerApartmentIds)) {
            $apartments = Apartment::with(['floor.block'])
                ->whereIn('id', $ownerApartmentIds)
                ->orderBy('apartment_number')
                ->get();
        } elseif ($user->apartment_id) {
            $apartments = Apartment::with(['floor.block'])
                ->where('id', $user->apartment_id)
                ->get();
        } else {
            $apartments = collect();
        }

        // Thành viên trong căn hộ (cả registered + declared)
        $registeredMembers = \App\Models\Resident::with(['user'])
            ->whereIn('apartment_id', $allApartmentIds)
            ->where('user_id', '!=', $user->id)
            ->whereNull('deleted_at')
            ->where('status', 'active')
            ->orderBy('created_at')
            ->get();

        $declaredMembers = ApartmentMember::whereIn('apartment_id', $allApartmentIds)
            ->orderBy('created_at')
            ->get();

        // Phương tiện
        $vehicles = \App\Models\Vehicle::where('apartment_id', $user->apartment_id)
            ->whereIn('status', ['active', 'pending', 'pending_renewal'])
            ->withoutTrashed()
            ->latest()
            ->get();

        // Số dư nợ hiện tại
        $outstandingBalance = \App\Models\Invoice::where('apartment_id', $user->apartment_id)
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->sum(\Illuminate\Support\Facades\DB::raw('total_amount - paid_amount'));

        // Resident record của user hiện tại
        $selfResident = \App\Models\Resident::where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        return view('resident.profile.index', compact(
            'user', 'apartments', 'registeredMembers', 'declaredMembers',
            'vehicles', 'outstandingBalance', 'selfResident'
        ));
    }

    public function update(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|regex:/^[0-9+]+$/|unique:users,phone,' . $user->id,
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'cccd' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại chỉ được chứa số và dấu +.',
            'phone.unique' => 'Số điện thoại đã tồn tại trong hệ thống.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'cccd.regex' => 'Số CCCD chỉ được chứa số.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB.',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin cá nhân thành công.',
            'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'name' => $user->name,
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => ['current_password' => ['Mật khẩu hiện tại không đúng.']],
            ], 422);
        }

        $user->update(['password' => $request->password]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công.',
        ]);
    }

    /**
     * Thành viên rời khỏi căn hộ tự nguyện
     */
    public function leaveApartment(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->apartment_id) {
            return back()->with('error', 'Bạn không thuộc căn hộ nào.');
        }

        $selfResident = \App\Models\Resident::where('user_id', $user->id)
            ->where('apartment_id', $user->apartment_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$selfResident) {
            return back()->with('error', 'Không tìm thấy thông tin cư trú.');
        }

        $isOwner = $selfResident->relationship === 'owner';
        $otherResidentsExist = \App\Models\Resident::where('apartment_id', $user->apartment_id)
            ->where('user_id', '!=', $user->id)
            ->whereNull('deleted_at')
            ->where('status', 'active')
            ->exists();

        if ($isOwner && $otherResidentsExist) {
            return back()->with('error', 'Chủ hộ phải chuyển quyền cho người khác trước khi rời đi.');
        }

        DB::transaction(function () use ($selfResident) {
            $selfResident->delete();
        });

        return redirect()->route('resident.login')->with('status', 'Bạn đã rời khỏi căn hộ thành công.');
    }

    /**
     * Gửi mã OTP chuyển giao quyền chủ hộ
     */
    public function sendTransferOtp(Request $request)
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,id',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $isOwner = \App\Models\Resident::where('user_id', $user->id)
            ->where('apartment_id', $user->apartment_id)
            ->where('relationship', 'owner')
            ->whereNull('deleted_at')
            ->exists();

        if (!$isOwner) {
            return response()->json(['success' => false, 'message' => 'Bạn không phải chủ hộ của căn hộ này.'], 403);
        }

        $targetResident = \App\Models\Resident::where('user_id', $request->target_user_id)
            ->where('apartment_id', $user->apartment_id)
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->first();

        if (!$targetResident) {
            return response()->json(['success' => false, 'message' => 'Người nhận không phải là cư dân hoạt động trong căn hộ này.'], 422);
        }

        $otp = (string) random_int(100000, 999999);
        session([
            'transfer_owner_otp' => $otp,
            'transfer_owner_target_id' => $request->target_user_id,
            'transfer_owner_otp_expires' => now()->addMinutes(10)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mã OTP đã được tạo thành công.',
            'otp_demo' => $otp
        ]);
    }

    /**
     * Xác nhận và chuyển giao quyền chủ hộ
     */
    public function verifyTransferOwner(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.size' => 'Mã OTP phải gồm 6 số.'
        ]);

        $storedOtp = session('transfer_owner_otp');
        $targetId = session('transfer_owner_target_id');
        $expires = session('transfer_owner_otp_expires');

        if (!$storedOtp || !$targetId || !$expires || now()->gt($expires)) {
            return response()->json(['success' => false, 'message' => 'Yêu cầu chuyển quyền không hợp lệ hoặc đã hết hạn.'], 422);
        }

        if ($request->otp !== $storedOtp) {
            return response()->json(['success' => false, 'errors' => ['otp' => ['Mã OTP không chính xác.']]], 422);
        }

        /** @var User $user */
        $user = Auth::user();

        DB::transaction(function () use ($user, $targetId) {
            $currentOwnerResident = \App\Models\Resident::where('user_id', $user->id)
                ->where('apartment_id', $user->apartment_id)
                ->where('relationship', 'owner')
                ->whereNull('deleted_at')
                ->first();

            $targetResident = \App\Models\Resident::where('user_id', $targetId)
                ->where('apartment_id', $user->apartment_id)
                ->whereNull('deleted_at')
                ->first();

            if ($currentOwnerResident && $targetResident) {
                $currentOwnerResident->update(['relationship' => 'family_member']);
                $targetResident->update(['relationship' => 'owner']);
            }
        });

        session()->forget(['transfer_owner_otp', 'transfer_owner_target_id', 'transfer_owner_otp_expires']);

        return response()->json([
            'success' => true,
            'message' => 'Đã chuyển quyền chủ hộ thành công.'
        ]);
    }

    private function getOwnerApartmentIds(User $user): array
    {
        return $user->residents()
            ->where('relationship', 'owner')
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();
    }
}
