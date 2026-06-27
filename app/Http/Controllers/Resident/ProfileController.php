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

    private function getOwnerApartmentIds(User $user): array
    {
        return $user->residents()
            ->where('relationship', 'owner')
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();
    }
}
