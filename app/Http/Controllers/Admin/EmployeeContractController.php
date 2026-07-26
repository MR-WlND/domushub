<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SystemLogger;
use App\Http\Controllers\Controller;
use App\Models\EmployeeContract;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Quản lý Hợp đồng lao động nhân sự chung cư
 * Áp dụng cho: Admin & Manager
 */
class EmployeeContractController extends Controller
{
    private const STAFF_ROLES = ['admin', 'manager', 'staff', 'technician', 'security', 'cleaning'];

    public function index(Request $request)
    {
        $query = EmployeeContract::with(['user', 'creator']);

        // ── Bộ lọc ───────────────────────────────────────────
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ma_hop_dong', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('trang_thai', $status);
        }

        if ($type = $request->get('type')) {
            $query->where('loai_hop_dong', $type);
        }

        if ($role = $request->get('role')) {
            $query->whereHas('user', fn($q) => $q->where('role', $role));
        }

        $contracts = $query->orderByDesc('created_at')->paginate(15);

        // Cập nhật tự động trạng thái hợp đồng trước khi hiển thị
        foreach ($contracts as $contract) {
            $contract->updateCalculatedStatus();
            $contract->save();
        }

        // ── Thống kê tổng quan ──────────────────────────────
        $stats = [
            'total'         => EmployeeContract::count(),
            'active'        => EmployeeContract::where('trang_thai', 'hieu_luc')->count(),
            'expiring_soon' => EmployeeContract::expiringSoon(30)->count(),
            'expired'       => EmployeeContract::where('trang_thai', 'het_han')->count(),
        ];

        // Danh sách nhân viên chưa có hợp đồng hoặc chọn nhân viên
        $staffList = User::whereIn('role', self::STAFF_ROLES)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'role', 'base_salary']);

        return view('admin.contracts.index', compact('contracts', 'stats', 'staffList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => ['required', 'exists:users,id'],
            'ma_hop_dong'   => ['required', 'string', 'max:50', 'unique:employee_contracts,ma_hop_dong'],
            'loai_hop_dong' => ['required', Rule::in(['thu_viec', 'xac_dinh_thoi_han', 'khong_thoi_han', 'vendor_thue_ngoai', 'thoi_vu'])],
            'ngay_bat_dau'  => ['required', 'date'],
            'ngay_ket_thuc' => ['nullable', 'date', 'after_or_equal:ngay_bat_dau'],
            'luong_co_ban'  => ['required', 'numeric', 'min:0'],
            'ghi_chu'       => ['nullable', 'string', 'max:500'],
        ]);

        $validated['created_by'] = auth()->id();

        $contract = EmployeeContract::create($validated);
        $contract->updateCalculatedStatus();
        $contract->save();

        // Đồng bộ lương cơ bản sang hồ sơ User
        $user = User::find($validated['user_id']);
        if ($user && $validated['luong_co_ban'] > 0) {
            $user->update(['base_salary' => $validated['luong_co_ban']]);
        }

        SystemLogger::log(
            'Hợp đồng nhân sự',
            "Tạo mới hợp đồng {$contract->ma_hop_dong} cho nhân viên {$user->name}"
        );

        return redirect()->route('admin.contracts.index')
            ->with('success', "Đã tạo thành công hợp đồng {$contract->ma_hop_dong} cho nhân viên {$user->name}.");
    }

    public function update(Request $request, $id)
    {
        $contract = EmployeeContract::findOrFail($id);

        $validated = $request->validate([
            'loai_hop_dong' => ['required', Rule::in(['thu_viec', 'xac_dinh_thoi_han', 'khong_thoi_han', 'vendor_thue_ngoai', 'thoi_vu'])],
            'ngay_bat_dau'  => ['required', 'date'],
            'ngay_ket_thuc' => ['nullable', 'date', 'after_or_equal:ngay_bat_dau'],
            'luong_co_ban'  => ['required', 'numeric', 'min:0'],
            'trang_thai'    => ['required', Rule::in(['hieu_luc', 'sap_het_han', 'het_han', 'thanh_ly'])],
            'ghi_chu'       => ['nullable', 'string', 'max:500'],
        ]);

        $contract->update($validated);
        $contract->updateCalculatedStatus();
        $contract->save();

        // Cập nhật lương cơ bản User
        if ($contract->user && $validated['luong_co_ban'] > 0) {
            $contract->user->update(['base_salary' => $validated['luong_co_ban']]);
        }

        SystemLogger::log(
            'Hợp đồng nhân sự',
            "Cập nhật hợp đồng {$contract->ma_hop_dong} của {$contract->user->name}"
        );

        return redirect()->route('admin.contracts.index')
            ->with('success', "Đã cập nhật hợp đồng {$contract->ma_hop_dong}.");
    }

    public function destroy($id)
    {
        $contract = EmployeeContract::findOrFail($id);
        $contract->update(['trang_thai' => 'thanh_ly']);

        SystemLogger::log(
            'Hợp đồng nhân sự',
            "Thanh lý hợp đồng {$contract->ma_hop_dong} của {$contract->user->name}"
        );

        return redirect()->route('admin.contracts.index')
            ->with('success', "Đã chuyển hợp đồng {$contract->ma_hop_dong} sang trạng thái Thanh lý.");
    }
}
