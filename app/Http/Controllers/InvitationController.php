<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = Invitation::latest()->paginate(10);
        return view('admin.invitations.index', compact('invitations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
        ], [
            'max_uses.required' => 'Vui lòng nhập số lượt sử dụng tối đa.',
            'max_uses.integer' => 'Số lượt sử dụng phải là số nguyên.',
            'max_uses.min' => 'Số lượt sử dụng tối đa phải từ 1 trở lên.',
            'expires_at.date' => 'Ngày hết hạn không đúng định dạng.',
            'expires_at.after' => 'Ngày hết hạn phải sau thời điểm hiện tại.',
        ]);

        // Tạo mã ngẫu nhiên dạng: RES-XXXXXXXX (Resident)
        $code = 'RES-' . strtoupper(Str::random(8));

        Invitation::create([
            'code' => $code,
            'role' => 'resident',         // Cố định là cư dân
            'permissions' => [],          // Không có quyền đặc cách
            'max_uses' => $validated['max_uses'],
            'expires_at' => $validated['expires_at'],
            'created_by' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Đã tạo mã mời cư dân thành công: ' . $code);
    }

    public function destroy($id)
    {
        Invitation::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Đã xóa mã mời thành công.');
    }
}
