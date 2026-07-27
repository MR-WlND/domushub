<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Contract;
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
        $query = Staff::with(['department', 'contracts', 'user']);

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
            'admin'        => 'Quản trị viên',
            'manager'      => 'Quản lý',
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
            'phone' => 'nullable|string|max:20',
            'cccd' => 'nullable|string|max:20|unique:staffs,cccd',
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            // Tab 2: Job
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:active,inactive',
            // Tab 3: Contract
            'contract_number' => 'nullable|string|max:255|unique:contracts,contract_number',
            'contract_type' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            // Tab 4: System Account
            'create_account' => 'nullable|boolean',
            'email' => 'required_if:create_account,1|nullable|email|unique:users,email',
            'role' => 'required_if:create_account,1|nullable|string',
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

        if ($request->filled('contract_number')) {
            $filePath = null;
            if ($request->hasFile('contract_file')) {
                $filePath = $request->file('contract_file')->store('contracts', 'public');
            }

            $staff->contracts()->create([
                'contract_number' => $request->contract_number,
                'type' => $request->contract_type,
                'base_salary' => $request->base_salary ?? 0,
                'start_date' => $request->start_date ?? Carbon::now(),
                'end_date' => $request->end_date,
                'file_path' => $filePath,
            ]);
        }

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
        $staff->load(['contracts', 'user']);
        $departments = Department::all();
        $roles = [
            'admin'        => 'Quản trị viên',
            'manager'      => 'Quản lý',
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
            'phone' => 'nullable|string|max:20',
            'cccd' => 'nullable|string|max:20|unique:staffs,cccd,' . $staff->id,
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            // Tab 2: Job
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:active,inactive',
            // Tab 3: Contract (Add new contract)
            'contract_number' => 'nullable|string|max:255|unique:contracts,contract_number',
            'contract_type' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
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

        if ($request->filled('contract_number')) {
            $filePath = null;
            if ($request->hasFile('contract_file')) {
                $filePath = $request->file('contract_file')->store('contracts', 'public');
            }

            $staff->contracts()->create([
                'contract_number' => $request->contract_number,
                'type' => $request->contract_type,
                'base_salary' => $request->base_salary ?? 0,
                'start_date' => $request->start_date ?? Carbon::now(),
                'end_date' => $request->end_date,
                'file_path' => $filePath,
            ]);
        }

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
