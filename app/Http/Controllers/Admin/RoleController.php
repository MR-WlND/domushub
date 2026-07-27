<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SystemLogger;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    private const ROLE_DESCRIPTIONS = [
        'admin'      => 'Toàn quyền kiểm soát hệ thống, bao gồm cấu hình và phân quyền.',
        'manager'    => 'Quản lý vận hành chung, nhân sự và báo cáo doanh thu.',
        'staff'      => 'Xử lý các nghiệp vụ kế toán, thu phí và hợp đồng.',
        'technician' => 'Tiếp nhận và xử lý các yêu cầu sửa chữa, bảo trì kỹ thuật.',
        'security'   => 'Kiểm soát phương tiện, khách ra vào và an ninh tòa nhà.',
        'cleaning'   => 'Quản lý lịch trực và báo cáo công việc vệ sinh.',
    ];

    public function index(Request $request)
    {
        // Kiểm tra quyền Admin (Chỉ Admin mới có quyền truy cập trang Phân quyền)
        if (! auth()->user() || auth()->user()->role !== 'admin') {
            abort(403, 'Chỉ Quản trị viên (Admin) mới có quyền truy cập trang phân quyền.');
        }

        $roleDescriptions = self::ROLE_DESCRIPTIONS;

        // Đếm số lượng tài khoản theo từng vai trò (dùng scopeInternalStaff từ User model)
        $roleCounts = User::internalStaff()
            ->selectRaw('role, count(*) as total')
            ->where('status', 'active')
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        // Danh sách nhân sự cho trang phân quyền
        $query = User::internalStaff();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        $users = $query->with('staff')->orderBy('role')->latest()->paginate(20);

        return view('admin.roles.index', compact('roleDescriptions', 'roleCounts', 'users'));
    }

    public function updateStatus(Request $request, $id)
    {
        // Giới hạn nghiêm ngặt: Chỉ Admin tuyệt đối mới được thực hiện thay đổi phân quyền
        if (! auth()->user() || auth()->user()->role !== 'admin') {
            abort(403, 'Chỉ Quản trị viên (Admin) mới có quyền thay đổi phân quyền tài khoản.');
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', Rule::in(['admin', 'manager', 'staff', 'technician', 'security', 'cleaning', 'receptionist'])],
            'status'   => ['required', Rule::in(['pending', 'active', 'banned'])],
            'reset_password' => ['nullable', 'boolean'],
        ], [
            'name.required'   => 'Vui lòng nhập họ và tên.',
            'role.required'   => 'Vui lòng chọn vai trò.',
            'role.in'         => 'Vai trò không hợp lệ.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in'       => 'Trạng thái không hợp lệ.',
        ]);

        // Chặn người dùng tự khóa hoặc hạ quyền admin của chính mình
        if ((int) $user->id === (int) auth()->id() && ($validated['role'] !== 'admin' || $validated['status'] !== 'active')) {
            return back()->withErrors(['role' => 'Bạn không thể tự thay đổi vai trò hoặc khóa tài khoản của chính mình.']);
        }

        if (!empty($request->reset_password)) {
            $validated['password'] = 'Chungcu@2026';
        }
        unset($validated['reset_password']);

        $oldRole   = $user->role;
        $oldStatus = $user->status;

        $user->update($validated);

        SystemLogger::log(
            'Cập nhật hồ sơ & phân quyền',
            "Tài khoản: {$user->name} ({$user->email}) — Role: {$oldRole} → {$validated['role']}, Status: {$oldStatus} → {$validated['status']}"
        );

        return back()->with('success', "Đã cập nhật hồ sơ & phân quyền cho tài khoản {$user->name}.");
    }
}
