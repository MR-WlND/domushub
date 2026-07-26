<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SystemLogger;
use App\Http\Controllers\Controller;
use App\Models\DanhMucKhauTru;
use App\Models\DanhMucPhuCap;
use App\Models\DanhMucThuong;
use Illuminate\Http\Request;

/**
 * PayrollConfigController — Quản lý danh mục Phụ cấp, Thưởng, Khấu trừ.
 * Phân quyền: Chỉ Quản trị viên (Admin).
 */
class PayrollConfigController extends Controller
{
    private function checkAdmin()
    {
        if (! auth()->user() || auth()->user()->role !== 'admin') {
            abort(403, 'Chỉ Quản trị viên (Admin) mới có quyền quản lý danh mục lương.');
        }
    }

    public function index()
    {
        $this->checkAdmin();

        $phuCaps  = DanhMucPhuCap::orderBy('id')->get();
        $thuongs  = DanhMucThuong::orderBy('id')->get();
        $khauTrus = DanhMucKhauTru::orderBy('id')->get();

        return view('admin.payroll.config.index', compact('phuCaps', 'thuongs', 'khauTrus'));
    }

    // ── Phụ cấp ───────────────────────────────────────────────────

    public function storePhuCap(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'ten_phu_cap'  => 'required|string|max:150',
            'muc_mac_dinh' => 'required|numeric|min:0',
        ], [
            'ten_phu_cap.required' => 'Vui lòng nhập tên phụ cấp.',
        ]);

        $item = DanhMucPhuCap::create($validated);
        SystemLogger::log('Thêm phụ cấp', "Tên: {$item->ten_phu_cap}");

        return redirect()->to(portal_route('payroll.config.index'))
            ->with('success', "Đã thêm phụ cấp '{$item->ten_phu_cap}'.");
    }

    public function updatePhuCap(Request $request, $id)
    {
        $this->checkAdmin();

        $item = DanhMucPhuCap::findOrFail($id);

        $validated = $request->validate([
            'ten_phu_cap'  => 'required|string|max:150',
            'muc_mac_dinh' => 'required|numeric|min:0',
            'is_active'    => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $item->update($validated);

        SystemLogger::log('Sửa phụ cấp', "Tên: {$item->ten_phu_cap}");

        return redirect()->to(portal_route('payroll.config.index'))
            ->with('success', "Đã cập nhật phụ cấp '{$item->ten_phu_cap}'.");
    }

    // ── Thưởng ────────────────────────────────────────────────────

    public function storeThuong(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'ten_thuong' => 'required|string|max:150',
            'mo_ta'      => 'nullable|string|max:500',
        ], [
            'ten_thuong.required' => 'Vui lòng nhập tên khoản thưởng.',
        ]);

        $item = DanhMucThuong::create($validated);
        SystemLogger::log('Thêm danh mục thưởng', "Tên: {$item->ten_thuong}");

        return redirect()->to(portal_route('payroll.config.index'))
            ->with('success', "Đã thêm danh mục thưởng '{$item->ten_thuong}'.");
    }

    public function updateThuong(Request $request, $id)
    {
        $this->checkAdmin();

        $item = DanhMucThuong::findOrFail($id);

        $validated = $request->validate([
            'ten_thuong' => 'required|string|max:150',
            'mo_ta'      => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $item->update($validated);

        SystemLogger::log('Sửa danh mục thưởng', "Tên: {$item->ten_thuong}");

        return redirect()->to(portal_route('payroll.config.index'))
            ->with('success', "Đã cập nhật danh mục thưởng '{$item->ten_thuong}'.");
    }

    // ── Khấu trừ ──────────────────────────────────────────────────

    public function storeKhauTru(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'ten_khau_tru' => 'required|string|max:150',
            'loai'         => 'required|in:tu_dong,thu_cong',
        ], [
            'ten_khau_tru.required' => 'Vui lòng nhập tên khoản khấu trừ.',
        ]);

        $item = DanhMucKhauTru::create($validated);
        SystemLogger::log('Thêm khoản khấu trừ', "Tên: {$item->ten_khau_tru}");

        return redirect()->to(portal_route('payroll.config.index'))
            ->with('success', "Đã thêm khoản khấu trừ '{$item->ten_khau_tru}'.");
    }

    public function updateKhauTru(Request $request, $id)
    {
        $this->checkAdmin();

        $item = DanhMucKhauTru::findOrFail($id);

        $validated = $request->validate([
            'ten_khau_tru' => 'required|string|max:150',
            'loai'         => 'required|in:tu_dong,thu_cong',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $item->update($validated);

        SystemLogger::log('Sửa khoản khấu trừ', "Tên: {$item->ten_khau_tru}");

        return redirect()->to(portal_route('payroll.config.index'))
            ->with('success', "Đã cập nhật khoản khấu trừ '{$item->ten_khau_tru}'.");
    }
}
