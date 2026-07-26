<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SystemLogger;
use App\Http\Controllers\Controller;
use App\Models\BangLuong;
use App\Models\ChiTietKhauTru;
use App\Models\ChiTietPhuCap;
use App\Models\ChiTietThuong;
use App\Models\DanhMucKhauTru;
use App\Models\DanhMucPhuCap;
use App\Models\DanhMucThuong;
use App\Models\ThanhToanLuong;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * PayrollController — Quản lý Bảng lương & Thanh toán lương nhân viên.
 */
class PayrollController extends Controller
{
    private function checkAdmin()
    {
        if (! auth()->user() || auth()->user()->role !== 'admin') {
            abort(403, 'Chỉ Quản trị viên (Admin) mới có quyền thực hiện thao tác này.');
        }
    }

    // ── 1. Danh sách bảng lương ────────────────────────────────────

    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $query = BangLuong::with(['user', 'approver', 'thanhToan'])
            ->where('nam', (int) $year)
            ->where('thang', (int) $mon);

        if ($request->filled('role')) {
            $query->whereHas('user', fn($q) => $q->where('role', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('trang_thai_duyet', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q
                ->where('name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search . '%')
            );
        }

        $payrolls = $query->orderBy('user_id')->paginate(20);

        // Stats summary
        $statsBase = BangLuong::where('nam', (int) $year)->where('thang', (int) $mon);
        $stats = [
            'total_count'   => (clone $statsBase)->count(),
            'approved_count'=> (clone $statsBase)->where('trang_thai_duyet', 'da_duyet')->count(),
            'total_payroll' => (clone $statsBase)->sum('thuc_linh'),
            'paid_count'    => (clone $statsBase)->whereHas('thanhToan', fn($q) => $q->where('trang_thai', 'da_thanh_toan'))->count(),
        ];

        $roles = [
            'admin'      => 'Quản trị viên',
            'manager'    => 'Quản lý',
            'staff'      => 'Kế toán',
            'technician' => 'Kỹ thuật',
            'security'   => 'An ninh',
            'cleaning'   => 'Vệ sinh',
        ];

        return view('admin.payroll.index', compact('payrolls', 'month', 'year', 'mon', 'stats', 'roles'));
    }

    // ── 2. Sinh bảng lương tự động ─────────────────────────────────

    public function generate(Request $request)
    {
        $this->checkAdmin();

        $month = $request->input('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $year = (int) $year;
        $mon  = (int) $mon;

        // Lấy tất cả nhân sự nội bộ (dùng scopeStaff)
        $staffUsers = User::staff()->where('status', 'active')->get();

        $generatedCount = 0;
        $skippedCount   = 0;

        foreach ($staffUsers as $user) {
            $bangLuong = BangLuong::firstOrNew([
                'user_id' => $user->id,
                'thang'   => $mon,
                'nam'     => $year,
            ]);

            // Ràng buộc: Chặn ghi đè nếu đã duyệt hoặc đã thanh toán
            if ($bangLuong->exists && ! $bangLuong->canRegenerate()) {
                $skippedCount++;
                continue;
            }

            $bangLuong->fill([
                'luong_co_ban'        => $user->base_salary ?? 0,
                'so_ngay_cong_chuan' => config('payroll.so_ngay_cong_chuan', 26),
                'recorded_by'         => auth()->id(),
            ]);

            $bangLuong->save();

            // Gọi 4 bước tính toán chính
            $bangLuong->computeCongThucTe();
            $bangLuong->computeTienLuong();
            $bangLuong->syncKhauTruTuDong();

            // Tự động gán phụ cấp mặc định nếu có (chỉ gán khi tạo mới)
            if ($bangLuong->chiTietPhuCaps()->count() === 0) {
                $activePhuCaps = DanhMucPhuCap::where('is_active', true)->where('muc_mac_dinh', '>', 0)->get();
                foreach ($activePhuCaps as $pc) {
                    ChiTietPhuCap::create([
                        'bang_luong_id'        => $bangLuong->id,
                        'danh_muc_phu_cap_id' => $pc->id,
                        'so_tien'              => $pc->muc_mac_dinh,
                    ]);
                }
            }

            $bangLuong->recalculateTotals();
            $bangLuong->save();
            $generatedCount++;
        }

        SystemLogger::log('Sinh bảng lương', "Tháng {$mon}/{$year} — Sinh thành công {$generatedCount} bản ghi, Bỏ qua {$skippedCount} bản ghi đã duyệt/thanh toán");

        return redirect()->to(portal_route('payroll.index') . "?month={$month}")
            ->with('success', "Đã sinh bảng lương tháng {$mon}/{$year} cho {$generatedCount} nhân sự. (Bỏ qua {$skippedCount} bản ghi đã khóa)");
    }

    // ── 3. Xem phiếu lương chi tiết ───────────────────────────────

    public function show($id)
    {
        $bangLuong = BangLuong::with([
            'user',
            'approver',
            'recorder',
            'chiTietPhuCaps.danhMuc',
            'chiTietThuongs.danhMuc',
            'chiTietKhauTrus.danhMuc',
            'thanhToan.processor',
        ])->findOrFail($id);

        $danhMucPhuCaps  = DanhMucPhuCap::where('is_active', true)->get();
        $danhMucThuongs  = DanhMucThuong::where('is_active', true)->get();
        $danhMucKhauTrus = DanhMucKhauTru::where('is_active', true)->get();

        return view('admin.payroll.show', compact('bangLuong', 'danhMucPhuCaps', 'danhMucThuongs', 'danhMucKhauTrus'));
    }

    // ── 4. Duyệt bảng lương ────────────────────────────────────────

    public function approve($id)
    {
        $this->checkAdmin();

        $bangLuong = BangLuong::with('user')->findOrFail($id);

        if ($bangLuong->trang_thai_duyet === 'da_duyet') {
            return back()->with('info', 'Bảng lương này đã được duyệt từ trước.');
        }

        $bangLuong->update([
            'trang_thai_duyet' => 'da_duyet',
            'duyet_boi'        => auth()->id(),
            'ngay_duyet'       => now(),
        ]);

        SystemLogger::log('Duyệt bảng lương', "Nhân viên: {$bangLuong->user->name} — Tháng {$bangLuong->thang}/{$bangLuong->nam}");

        return back()->with('success', "Đã duyệt bảng lương tháng {$bangLuong->thang}/{$bangLuong->nam} của {$bangLuong->user->name}.");
    }

    // ── 5. Thanh toán lương ────────────────────────────────────────

    public function pay(Request $request, $id)
    {
        $this->checkAdmin();

        $bangLuong = BangLuong::with('user')->findOrFail($id);

        if ($bangLuong->trang_thai_duyet !== 'da_duyet') {
            return back()->withErrors(['error' => 'Bảng lương phải được Duyệt trước khi thực hiện thanh toán.']);
        }

        $validated = $request->validate([
            'hinh_thuc' => 'required|in:tien_mat,chuyen_khoan',
            'ghi_chu'   => 'nullable|string|max:500',
        ]);

        $thanhToan = ThanhToanLuong::firstOrNew(['bang_luong_id' => $bangLuong->id]);
        $thanhToan->fill([
            'hinh_thuc'       => $validated['hinh_thuc'],
            'ngay_thanh_toan' => now(),
            'trang_thai'      => 'da_thanh_toan',
            'xu_ly_boi'       => auth()->id(),
            'ghi_chu'         => $validated['ghi_chu'] ?? null,
        ]);
        $thanhToan->save();

        SystemLogger::log('Thanh toán lương', "Nhân viên: {$bangLuong->user->name} — Số tiền: " . number_format($bangLuong->thuc_linh) . ' VNĐ');

        return back()->with('success', "Đã xác nhận thanh toán lương cho {$bangLuong->user->name}.");
    }

    // ── 6. Lịch sử nhận lương ──────────────────────────────────────

    public function history($userId)
    {
        $user = User::findOrFail($userId);
        $payrolls = BangLuong::with(['thanhToan', 'approver'])
            ->where('user_id', $user->id)
            ->orderByDesc('nam')
            ->orderByDesc('thang')
            ->paginate(15);

        return view('admin.payroll.history', compact('user', 'payrolls'));
    }

    // ── 7. Báo cáo thống kê lương ──────────────────────────────────

    public function report(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        // Chi phí lương theo 12 tháng
        $monthlyStats = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyStats[$m] = BangLuong::where('nam', $year)
                ->where('thang', $m)
                ->sum('thuc_linh');
        }

        // Chi phí lương theo Role
        $roleStats = BangLuong::where('nam', $year)
            ->join('users', 'bang_luong.user_id', '=', 'users.id')
            ->selectRaw('users.role, SUM(bang_luong.thuc_linh) as total')
            ->groupBy('users.role')
            ->pluck('total', 'users.role')
            ->toArray();

        $roles = [
            'admin'      => 'Quản trị viên',
            'manager'    => 'Quản lý',
            'staff'      => 'Kế toán',
            'technician' => 'Kỹ thuật',
            'security'   => 'An ninh',
            'cleaning'   => 'Vệ sinh',
        ];

        return view('admin.payroll.report', compact('year', 'monthlyStats', 'roleStats', 'roles'));
    }

    // ── 8. In / Xuất phiếu lương ───────────────────────────────────

    public function exportPayslip($id)
    {
        $bangLuong = BangLuong::with([
            'user',
            'approver',
            'chiTietPhuCaps.danhMuc',
            'chiTietThuongs.danhMuc',
            'chiTietKhauTrus.danhMuc',
            'thanhToan',
        ])->findOrFail($id);

        return view('admin.payroll.export', compact('bangLuong'));
    }

    // ── 9. Thêm phụ cấp / thưởng / khấu trừ thủ công vào phiếu lương ──

    public function addPhuCap(Request $request, $id)
    {
        $this->checkAdmin();
        $bangLuong = BangLuong::findOrFail($id);
        if (! $bangLuong->canRegenerate()) return back()->withErrors(['error' => 'Bảng lương đã khóa.']);

        $request->validate([
            'danh_muc_phu_cap_id' => 'required|exists:danh_muc_phu_cap,id',
            'so_tien'             => 'required|numeric|min:0',
        ]);

        ChiTietPhuCap::create([
            'bang_luong_id'        => $bangLuong->id,
            'danh_muc_phu_cap_id' => $request->danh_muc_phu_cap_id,
            'so_tien'              => $request->so_tien,
        ]);

        $bangLuong->recalculateTotals();
        $bangLuong->save();

        return back()->with('success', 'Đã thêm phụ cấp.');
    }

    public function addThuong(Request $request, $id)
    {
        $this->checkAdmin();
        $bangLuong = BangLuong::findOrFail($id);
        if (! $bangLuong->canRegenerate()) return back()->withErrors(['error' => 'Bảng lương đã khóa.']);

        $request->validate([
            'danh_muc_thuong_id' => 'required|exists:danh_muc_thuong,id',
            'so_tien'            => 'required|numeric|min:0',
            'ly_do'              => 'nullable|string|max:500',
        ]);

        ChiTietThuong::create([
            'bang_luong_id'      => $bangLuong->id,
            'danh_muc_thuong_id' => $request->danh_muc_thuong_id,
            'so_tien'            => $request->so_tien,
            'ly_do'              => $request->ly_do,
        ]);

        $bangLuong->recalculateTotals();
        $bangLuong->save();

        return back()->with('success', 'Đã thêm khoản thưởng.');
    }

    public function addKhauTru(Request $request, $id)
    {
        $this->checkAdmin();
        $bangLuong = BangLuong::findOrFail($id);
        if (! $bangLuong->canRegenerate()) return back()->withErrors(['error' => 'Bảng lương đã khóa.']);

        $request->validate([
            'danh_muc_khau_tru_id' => 'required|exists:danh_muc_khau_tru,id',
            'so_tien'              => 'required|numeric|min:0',
            'ly_do'                => 'nullable|string|max:500',
        ]);

        ChiTietKhauTru::create([
            'bang_luong_id'        => $bangLuong->id,
            'danh_muc_khau_tru_id' => $request->danh_muc_khau_tru_id,
            'so_tien'              => $request->so_tien,
            'ly_do'                => $request->ly_do,
        ]);

        $bangLuong->recalculateTotals();
        $bangLuong->save();

        return back()->with('success', 'Đã thêm khoản khấu trừ thủ công.');
    }
}
