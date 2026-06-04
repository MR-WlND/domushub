<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private const DEFAULT_PASSWORD = 'Chungcu@2026';

    // 1. Hàm hiển thị danh sách, xử lý tìm kiếm và phân trang
    public function index(Request $request)
    {
        // Khởi tạo query từ Model User
        $query = User::query();

        // Tìm kiếm theo Tên hoặc Email (nếu có nhập)
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Lọc theo Vai trò (nếu có chọn)
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        // Lọc theo Trạng thái (nếu có chọn)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Lấy dữ liệu và phân trang (10 user mỗi trang)
        $users = $query->latest()->paginate(10);

        // Trả về view giao diện admin
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', [
            'roleLabels' => [
                'admin' => 'Quản trị viên',
                'manager' => 'Quản lý',
                'staff' => 'Nhân viên',
                'technician' => 'Kỹ thuật',
                'security' => 'An ninh',
                'resident' => 'Cư dân',
            ],
            'statusLabels' => [
                'pending' => 'Chờ kích hoạt',
                'active' => 'Đang hoạt động',
                'banned' => 'Đã khóa',
            ],
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.edit', [
            'user' => $user,
            'roleLabels' => [
                'admin' => 'Quản trị viên',
                'manager' => 'Quản lý',
                'staff' => 'Nhân viên',
                'technician' => 'Kỹ thuật',
                'security' => 'An ninh',
                'resident' => 'Cư dân',
            ],
            'statusLabels' => [
                'pending' => 'Chờ kích hoạt',
                'active' => 'Đang hoạt động',
                'banned' => 'Đã khóa',
            ],
        ]);
    }
    // 2. Hàm cập nhật Vai trò và Trạng thái khi Admin bấm Lưu trên Modal
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'required|string|max:20|regex:/^[0-9+]+$/|unique:users,phone',
            'role' => 'required|in:admin,manager,staff,technician,security,resident',
            'status' => 'required|in:pending,active,banned',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại chỉ được chứa số và dấu +.',
            'phone.unique' => 'Số điện thoại đã tồn tại trong hệ thống.',
            'role.required' => 'Vui lòng chọn vai trò.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'role.in' => 'Vai trò không hợp lệ.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'],
            'password' => self::DEFAULT_PASSWORD,
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Tạo tài khoản ' . $user->name . ' thành công. Mật khẩu mặc định: ' . self::DEFAULT_PASSWORD);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $id,
            'phone' => 'required|string|max:20|regex:/^[0-9+]+$/|unique:users,phone,' . $id,
            'role' => 'required|in:admin,manager,staff,technician,security,resident',
            'status' => 'required|in:pending,active,banned',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại chỉ được chứa số và dấu +.',
            'phone.unique' => 'Số điện thoại đã tồn tại trong hệ thống.',
            'role.required' => 'Vui lòng chọn vai trò.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'role.in' => 'Vai trò không hợp lệ.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật thông tin người dùng thành công!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,manager,staff,technician,security,resident',
            'status' => 'required|in:pending,active,banned',
        ], [
            'role.required' => 'Vui lòng chọn vai trò.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'role.in' => 'Vai trò không hợp lệ.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'role' => $request->role,
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Cập nhật tài khoản thành công!');
    }
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'password' => self::DEFAULT_PASSWORD,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Đã đặt lại mật khẩu cho ' . $user->name . '. Mật khẩu mặc định: ' . self::DEFAULT_PASSWORD);
    }
}
