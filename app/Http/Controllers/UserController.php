<?php

namespace App\Http\Controllers;

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
    // 2. Hàm cập nhật Vai trò và Trạng thái khi Admin bấm Lưu trên Modal
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'role' => 'required|in:admin,manager,staff,technician,security,resident',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => 'AUTO' . now()->format('ymdHis'),
            'password' => self::DEFAULT_PASSWORD,
            'role' => $validated['role'],
            'status' => 'active',
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Tạo tài khoản ' . $user->name . ' thành công. Mật khẩu mặc định: ' . self::DEFAULT_PASSWORD);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,manager,staff,technician,security,resident',
            'status' => 'required|in:active,pending,inactive',
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
