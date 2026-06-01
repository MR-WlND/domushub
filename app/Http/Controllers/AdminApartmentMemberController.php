<?php

namespace App\Http\Controllers;

use App\Models\ApartmentMember;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AdminApartmentMemberController extends Controller
{
    public function index()
    {
        $members = ApartmentMember::with(['user', 'apartment.floor.block'])
            ->orderBy('status')
            ->orderBy('apartment_id')
            ->orderBy('name')
            ->get();

        return view('admin.apartment-members.index', compact('members'));
    }

    public function verify(ApartmentMember $member): RedirectResponse
    {
        $member->update(['status' => 'verified']);

        // After verifying a member, create a single-use MEM invitation tied to that member
        $apartmentId = $member->apartment_id;

        $message = 'Đã xác nhận nhân khẩu.';

        // Find owner to assign created_by so owner can see the code
        $owner = ApartmentMember::where('apartment_id', $apartmentId)->where('relationship', 'owner')->first();
        $ownerUserId = $owner && $owner->user_id ? $owner->user_id : Auth::id();

        // If an invitation for this specific member already exists, don't create another
        $existing = Invitation::where('apartment_member_id', $member->id)
            ->where('type', 'member_invite')
            ->first();

        if ($existing) {
            // Ensure it's active and single-use
            $existing->max_uses = 1;
            $existing->status = $existing->uses_count >= 1 ? 'used' : 'active';
            $existing->created_by = $ownerUserId;
            $existing->save();

            $message = 'Đã xác nhận nhân khẩu. Mã MEM đã tồn tại: ' . $existing->code;
        } else {
            $code = 'MEM-' . strtoupper(Str::random(8));

            $inv = Invitation::create([
                'code' => $code,
                'type' => 'member_invite',
                'apartment_member_id' => $member->id,
                'apartment_id' => $apartmentId,
                'status' => 'active',
                'max_uses' => 1,
                'uses_count' => 0,
                'created_by' => $ownerUserId,
                'permissions' => [],
            ]);

            $message = 'Đã xác nhận nhân khẩu. Đã tạo mã MEM: ' . $inv->code;
        }

        return back()->with('success', $message);
    }

    public function reject(ApartmentMember $member): RedirectResponse
    {
        $member->update(['status' => 'rejected']);

        // Cancel any existing member invitation
        $inv = Invitation::where('apartment_member_id', $member->id)
            ->where('type', 'member_invite')
            ->first();

        if ($inv) {
            $inv->status = 'cancelled';
            $inv->save();
        }

        return back()->with('success', 'Đã từ chối nhân khẩu. Mã mời liên quan (nếu có) đã bị huỷ.');
    }
}
