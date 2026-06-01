<?php

namespace App\Http\Controllers;

use App\Models\ApartmentMember;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApartmentMemberController extends Controller
{
    public function index()
    {
        $ownerMember = ApartmentMember::where('user_id', Auth::id())
            ->where('relationship', 'owner')
            ->first();

        if (! $ownerMember) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        $members = ApartmentMember::with(['user', 'apartment.floor.block'])
            ->where('apartment_id', $ownerMember->apartment_id)
            ->orderByRaw("FIELD(relationship, 'owner', 'spouse', 'child', 'other')")
            ->orderBy('name')
            ->get();

        $memberInvites = Invitation::with('apartmentMember')
            ->where('type', 'member_invite')
            ->where('apartment_id', $ownerMember->apartment_id)
            ->get();

        return view('resident.members.index', compact('members', 'memberInvites', 'ownerMember'));
    }

    public function invite(Request $request, ApartmentMember $member): RedirectResponse
    {
        $ownerMember = ApartmentMember::where('user_id', Auth::id())
            ->where('relationship', 'owner')
            ->first();

        if (! $ownerMember || $member->apartment_id !== $ownerMember->apartment_id) {
            abort(403, 'Không thể tạo mã mời cho thành viên này.');
        }


        if ($member->user_id) {
            return back()->with('error', 'Thành viên này đã có tài khoản.');
        }

        // Only allow showing the member invitation if the member has been verified by admin
        if ($member->status !== 'verified') {
            return back()->with('error', 'Thành viên phải được admin duyệt trước khi có mã mời.');
        }

        // Find existing invitation for this specific member
        $existing = Invitation::where('apartment_member_id', $member->id)
            ->where('type', 'member_invite')
            ->first();

        if ($existing) {
            return back()->with('success', 'Mã mời thành viên: ' . $existing->code);
        }

        return back()->with('error', 'Hiện chưa có mã mời cho thành viên này. Mã sẽ được tạo khi admin duyệt.');
    }

    public function store(Request $request): RedirectResponse
    {
        $ownerMember = ApartmentMember::where('user_id', Auth::id())
            ->where('relationship', 'owner')
            ->first();

        if (! $ownerMember) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'date_of_birth' => ['nullable', 'date', 'before:tomorrow'],
            'relationship' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
        ]);

        ApartmentMember::create([
            'apartment_id' => $ownerMember->apartment_id,
            'name' => $validated['name'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'relationship' => $validated['relationship'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Đã thêm nhân khẩu mới. Vui lòng chờ duyệt (nếu quy trình yêu cầu).');
    }

    public function destroy(ApartmentMember $member): RedirectResponse
    {
        $ownerMember = ApartmentMember::where('user_id', Auth::id())
            ->where('relationship', 'owner')
            ->first();

        if (! $ownerMember || $member->apartment_id !== $ownerMember->apartment_id) {
            abort(403, 'Không thể xóa nhân khẩu này.');
        }

        if ($member->user_id) {
            return back()->with('error', 'Không thể xóa nhân khẩu đã có tài khoản.');
        }

        $member->delete();

        return back()->with('success', 'Đã xóa nhân khẩu.');
    }
}
