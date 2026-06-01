<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = Invitation::with('building')->latest()->paginate(10);
        $blocks = DB::table('blocks')->orderBy('name')->get();

        return view('admin.invitations.index', compact('invitations', 'blocks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:blocks,id',
            'expires_at' => 'nullable|date|after:now',
        ], [
            'building_id.required' => 'Vui lòng chọn tòa nhà.',
            'building_id.exists' => 'Tòa nhà không hợp lệ.',
            'expires_at.date' => 'Ngày hết hạn không đúng định dạng.',
            'expires_at.after' => 'Ngày hết hạn phải sau thời điểm hiện tại.',
        ]);

        $code = 'RES-' . strtoupper(Str::random(8));

        Invitation::create([
            'code' => $code,
            'type' => 'resident_master',
            'building_id' => $validated['building_id'],
            'status' => 'active',
            'max_uses' => 1,
            'uses_count' => 0,
            'expires_at' => $validated['expires_at'],
            'created_by' => Auth::id(),
            'permissions' => [],
        ]);

        return redirect()->back()->with('success', 'Đã tạo mã mời RES thành công: ' . $code);
    }

    public function destroy($id)
    {
        Invitation::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Đã xóa mã mời thành công.');
    }
}
