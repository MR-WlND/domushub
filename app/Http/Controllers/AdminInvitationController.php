<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentInvite;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminInvitationController extends Controller
{
    public function index()
    {
        $blocks = Block::orderBy('name')->get();
        $apartments = Apartment::with(['floor.block'])
            ->orderBy('apartment_number')
            ->get();

        $invitations = ApartmentInvite::with(['apartment.floor.block', 'block', 'creator'])
            ->latest()
            ->paginate(10);

        return view('admin.invitations.index', compact('blocks', 'apartments', 'invitations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'apartment_id' => 'nullable|exists:apartments,id',
            'intended_relationship' => 'required|in:owner,tenant,family_member',
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date_format:Y-m-d\TH:i|after:now',
        ], [
            'block_id.required' => 'Vui lòng chọn tòa nhà.',
            'block_id.exists' => 'Tòa nhà chọn không hợp lệ.',
            'apartment_id.exists' => 'Căn hộ chọn không hợp lệ.',
            'intended_relationship.required' => 'Vui lòng chọn quan hệ cư dân.',
            'intended_relationship.in' => 'Quan hệ cư dân không hợp lệ.',
            'max_uses.required' => 'Vui lòng nhập số lượt sử dụng tối đa.',
            'max_uses.integer' => 'Số lượt sử dụng phải là số nguyên.',
            'max_uses.min' => 'Số lượt sử dụng tối đa phải từ 1 trở lên.',
            'expires_at.date_format' => 'Ngày hết hạn phải đúng định dạng.',
            'expires_at.after' => 'Ngày hết hạn phải sau thời điểm hiện tại.',
        ]);

        if ($validated['apartment_id']) {
            $apartment = Apartment::with('floor')->findOrFail($validated['apartment_id']);

            if (! $apartment->floor) {
                return back()->withErrors(['apartment_id' => 'Căn hộ không có thông tin tầng hợp lệ.'])->withInput();
            }

            if ($apartment->floor->block_id != $validated['block_id']) {
                return back()->withErrors(['apartment_id' => 'Căn hộ phải thuộc tòa nhà đã chọn.'])->withInput();
            }
        }

        do {
            $code = 'RES-' . strtoupper(Str::random(8));
        } while (ApartmentInvite::where('invite_code', $code)->exists());

        ApartmentInvite::create([
            'block_id' => $validated['block_id'],
            'apartment_id' => $validated['apartment_id'] ?? null,
            'created_by' => Auth::id(),
            'invite_code' => $code,
            'intended_relationship' => $validated['intended_relationship'],
            'status' => 'active',
            'expired_at' => $validated['expires_at'] ?? null,
            'max_uses' => $validated['max_uses'],
            'uses_count' => 0,
        ]);

        return redirect()->back()->with('success', 'Đã tạo mã mời cư dân thành công: ' . $code);
    }

    public function destroy($id)
    {
        ApartmentInvite::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Đã xóa mã mời thành công.');
    }
}
