<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminInviteController extends Controller
{
    public function index(): View
    {
        $apartments = DB::table('apartments as a')
            ->join('floors as f', 'f.id', '=', 'a.floor_id')
            ->join('blocks as b', 'b.id', '=', 'f.block_id')
            ->select('a.id', 'a.apartment_number', 'a.area', 'a.status', 'f.floor_number', 'b.name as block_name')
            ->orderBy('b.name')
            ->orderBy('f.floor_number')
            ->orderBy('a.apartment_number')
            ->get();

        $invites = DB::table('apartment_invites as i')
            ->join('apartments as a', 'a.id', '=', 'i.apartment_id')
            ->join('floors as f', 'f.id', '=', 'a.floor_id')
            ->join('blocks as b', 'b.id', '=', 'f.block_id')
            ->select('i.*', 'a.apartment_number', 'f.floor_number', 'b.name as block_name')
            ->orderByDesc('i.created_at')
            ->get();

        return view('admin.invitations.index', compact('apartments', 'invites'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'apartment_id' => ['required', 'integer', 'exists:apartments,id'],
            'max_residents' => ['required', 'integer', 'min:1', 'max:20'],
            'relationship' => ['required', 'string', 'in:owner,tenant,family_member'],
            'expired_days' => ['required', 'integer', 'min:1', 'max:90'],
        ]);

        $maxResidents = (int) $validated['max_residents'];
        $expiredDays = (int) $validated['expired_days'];

        $inviteCode = 'INVITE-' . strtoupper(substr(md5(uniqid((string) time(), true)), 0, 8));

        DB::table('apartment_invites')->insert([
            'apartment_id' => $validated['apartment_id'],
            'created_by' => Auth::id(),
            'invite_code' => $inviteCode,
            'intended_relationship' => $validated['relationship'],
            'status' => 'active',
            'max_residents' => $maxResidents,
            'used_count' => 0,
            'expired_at' => now()->addDays($expiredDays),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('status', 'Tạo mã mời thành công. Mã mời mới: ' . $inviteCode);
    }
}
