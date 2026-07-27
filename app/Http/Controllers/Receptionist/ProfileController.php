<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Thống kê nhanh cho profile
        $totalParcelsHandled = \App\Models\Parcel::where('created_by', $user->id)->count();
        $parcelsToday        = \App\Models\Parcel::where('created_by', $user->id)
            ->whereDate('arrived_at', today())->count();

        return view('receptionist.profile.index', compact(
            'user', 'totalParcelsHandled', 'parcelsToday'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'required|string|max:20|regex:/^[0-9+]+$/|unique:users,phone,' . $user->id,
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
        ], [
            'name.required'  => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex'    => 'Số điện thoại chỉ được chứa số và dấu +.',
            'phone.unique'   => 'Số điện thoại đã tồn tại trong hệ thống.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không đúng định dạng.',
            'email.unique'   => 'Email đã tồn tại trong hệ thống.',
        ]);

        // Upload avatar nếu có
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'avatar.image'  => 'File phải là hình ảnh.',
                'avatar.mimes'  => 'Chỉ chấp nhận định dạng jpg, png, webp.',
                'avatar.max'    => 'Ảnh không được vượt quá 2MB.',
            ]);

            // Xóa ảnh cũ nếu có
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return redirect()->route('receptionist.profile')
            ->with('success', 'Cập nhật thông tin thành công.');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required'         => 'Vui lòng nhập mật khẩu mới.',
            'password.min'              => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed'        => 'Xác nhận mật khẩu không khớp.',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Mật khẩu hiện tại không đúng.',
            ]);
        }

        $user->update(['password' => $request->password]);

        return redirect()->route('receptionist.profile')
            ->with('success', 'Đổi mật khẩu thành công.');
    }
}
