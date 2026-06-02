<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $ownerApartmentIds = $this->getOwnerApartmentIds($user);

        $apartments = Apartment::with(['floor.block'])
            ->whereIn('id', $ownerApartmentIds)
            ->orderBy('apartment_number')
            ->get();

        return view('resident.profile.index', compact('user', 'apartments'));
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|regex:/^[0-9+]+$/|unique:users,phone,' . $user->id,
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'cccd' => 'nullable|string|max:20|regex:/^[0-9]+$/',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại chỉ được chứa số và dấu +.',
            'phone.unique' => 'Số điện thoại đã tồn tại trong hệ thống.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'cccd.regex' => 'Số CCCD chỉ được chứa số.',
        ]);

        $user->update($validated);

        return redirect()->route('resident.profile.index')
            ->with('success', 'Cập nhật thông tin cá nhân thành công.');
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
