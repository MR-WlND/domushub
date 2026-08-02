<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Department;
use App\Models\User;
use App\Helpers\SystemLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StaffController extends Controller
{
    private const DEFAULT_PASSWORD = 'Chungcu@2026';

    public function index(Request $request)
    {
        $query = Staff::with(['department', 'user']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('cccd', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $staffs = $query->latest()->paginate(10);
        $departments = Department::all();

        return view('admin.staffs.index', compact('staffs', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        $roles = [
            'staff'        => 'Nhân viên kế toán',
            'technician'   => 'Kỹ thuật',
            'security'     => 'An ninh',
            'cleaning'     => 'Nhân viên vệ sinh',
            'receptionist' => 'Lễ tân',
        ];

        return view('admin.staffs.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Tab 1: Info
            'full_name' => 'required|string|max:255',
            'phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,12}$/',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->create_account == 1 && $value) {
                        $exists = \App\Models\User::where('phone', $value)->exists();
                        if ($exists) {
                            $fail('Số điện thoại này đã được đăng ký cho một tài khoản khác trong hệ thống.');
                        }
                    }
                }
            ],
            'cccd' => 'nullable|string|regex:/^[0-9]{9,12}$/|unique:staffs,cccd',
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date|before:today',
            // Tab 2: Job
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:active,inactive',
            // Tab 3: System Account
            'create_account' => 'nullable|boolean',
            'email' => 'required_if:create_account,1|nullable|email|unique:users,email',
            'role' => 'required_if:create_account,1|nullable|in:staff,technician,security,cleaning,receptionist',
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên nhân sự.',
            'full_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'phone.regex' => 'Số điện thoại không hợp lệ (chỉ chứa 10-12 chữ số).',
            'cccd.regex' => 'Số CCCD/CMND không hợp lệ (chứa 9-12 chữ số).',
            'cccd.unique' => 'Số CCCD/CMND này đã được sử dụng cho một nhân sự khác.',
            'dob.date' => 'Ngày sinh không đúng định dạng.',
            'dob.before' => 'Ngày sinh phải là một ngày trước hiện tại.',
            'department_id.exists' => 'Phòng ban được chọn không hợp lệ.',
            'status.required' => 'Vui lòng chọn trạng thái làm việc.',
            'email.required_if' => 'Vui lòng nhập email khi tạo tài khoản.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'role.required_if' => 'Vui lòng chọn vai trò hệ thống khi tạo tài khoản.',
            'role.in' => 'Vai trò được chọn không hợp lệ.',
        ]);

        $staff = Staff::create([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'cccd' => $request->cccd,
            'address' => $request->address,
            'dob' => $request->dob,
            'department_id' => $request->department_id,
            'status' => $request->status,
        ]);

        if ($request->create_account == 1) {
            $user = User::create([
                'name' => $staff->full_name,
                'email' => strtolower(trim($request->email)),
                'phone' => $staff->phone ?? Str::random(10), // fallback if no phone
                'password' => self::DEFAULT_PASSWORD,
                'role' => $request->role,
                'status' => 'active',
                'staff_id' => $staff->id,
            ]);
        }

        SystemLogger::log('Thêm nhân sự mới', 'Nhân sự: ' . $staff->full_name);

        return redirect()->route('admin.staffs.index')->with('success', 'Thêm nhân sự thành công!');
    }

    public function edit(Staff $staff)
    {
        $staff->load(['user']);
        $departments = Department::all();
        $roles = [
            'staff'        => 'Nhân viên kế toán',
            'technician'   => 'Kỹ thuật',
            'security'     => 'An ninh',
            'cleaning'     => 'Nhân viên vệ sinh',
            'receptionist' => 'Lễ tân',
        ];

        return view('admin.staffs.edit', compact('staff', 'departments', 'roles'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            // Tab 1: Info
            'full_name' => 'required|string|max:255',
            'phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,12}$/',
                function ($attribute, $value, $fail) use ($staff) {
                    if ($staff->user && $value && $value !== $staff->user->phone) {
                        $exists = \App\Models\User::where('phone', $value)->exists();
                        if ($exists) {
                            $fail('Số điện thoại này đã được đăng ký cho một tài khoản khác trong hệ thống.');
                        }
                    }
                }
            ],
            'cccd' => 'nullable|string|regex:/^[0-9]{9,12}$/|unique:staffs,cccd,' . $staff->id,
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date|before:today',
            // Tab 2: Job
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:active,inactive',
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên nhân sự.',
            'full_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'phone.regex' => 'Số điện thoại không hợp lệ (chỉ chứa 10-12 chữ số).',
            'cccd.regex' => 'Số CCCD/CMND không hợp lệ (chứa 9-12 chữ số).',
            'cccd.unique' => 'Số CCCD/CMND này đã được sử dụng cho một nhân sự khác.',
            'dob.date' => 'Ngày sinh không đúng định dạng.',
            'dob.before' => 'Ngày sinh phải là một ngày trước hiện tại.',
            'department_id.exists' => 'Phòng ban được chọn không hợp lệ.',
            'status.required' => 'Vui lòng chọn trạng thái làm việc.',
        ]);

        $staff->update([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'cccd' => $request->cccd,
            'address' => $request->address,
            'dob' => $request->dob,
            'department_id' => $request->department_id,
            'status' => $request->status,
        ]);

        // If user already has an account, sync name/phone
        if ($staff->user) {
            $staff->user->update([
                'name' => $staff->full_name,
                'phone' => $staff->phone ?? $staff->user->phone,
            ]);
        }

        SystemLogger::log('Cập nhật thông tin nhân sự', 'Nhân sự: ' . $staff->full_name);

        return redirect()->route('admin.staffs.index')->with('success', 'Cập nhật nhân sự thành công!');
    }

    public function destroy(Staff $staff)
    {
        $name = $staff->full_name;
        $staff->delete();
        SystemLogger::log('Xóa nhân sự', 'Nhân sự: ' . $name);
        return redirect()->route('admin.staffs.index')->with('success', 'Đã xóa nhân sự.');
    }
}
