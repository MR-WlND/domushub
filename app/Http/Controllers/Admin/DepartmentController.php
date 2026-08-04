<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Helpers\SystemLogger;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('staffs')->orderBy('id', 'desc')->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:departments,code',
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'is_shift' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Handle checkbox
        $validated['is_shift'] = $request->has('is_shift');

        $dept = Department::create($validated);
        SystemLogger::log('Thêm phòng ban mới', 'Phòng ban: ' . $dept->name);

        return redirect()->route('admin.departments.index')->with('success', 'Thêm phòng ban thành công!');
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'is_shift' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);
        
        $validated['is_shift'] = $request->has('is_shift');

        $department->update($validated);
        SystemLogger::log('Cập nhật phòng ban', 'Phòng ban: ' . $department->name);

        return redirect()->route('admin.departments.index')->with('success', 'Cập nhật phòng ban thành công!');
    }

    public function destroy(Department $department)
    {
        if ($department->staffs()->count() > 0) {
            return redirect()->route('admin.departments.index')->withErrors(['error' => 'Không thể xóa phòng ban đang có nhân viên!']);
        }

        $name = $department->name;
        $department->delete();
        SystemLogger::log('Xóa phòng ban', 'Phòng ban: ' . $name);

        return redirect()->route('admin.departments.index')->with('success', 'Đã xóa phòng ban thành công!');
    }
}
