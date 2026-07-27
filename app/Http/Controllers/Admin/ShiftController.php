<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::withCount('rosters')->orderBy('id', 'asc')->get();
        return view('admin.shifts.index', compact('shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'grace_period_minutes' => 'required|integer|min:0',
            'shift_rate' => 'required|numeric|min:0.5|max:3.0',
            'description' => 'nullable|string',
        ]);

        $validated['status'] = 'active';

        Shift::create($validated);

        return redirect()->back()->with('success', 'Thêm ca làm việc mới thành công!');
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'grace_period_minutes' => 'required|integer|min:0',
            'shift_rate' => 'required|numeric|min:0.5|max:3.0',
            'description' => 'nullable|string',
        ]);

        $shift->update($validated);

        return redirect()->back()->with('success', 'Cập nhật ca làm việc thành công!');
    }

    public function toggleStatus(Shift $shift)
    {
        $shift->status = $shift->status === 'active' ? 'inactive' : 'active';
        $shift->save();

        return redirect()->back()->with('success', 'Đổi trạng thái ca làm việc thành công!');
    }

    public function destroy(Shift $shift)
    {
        if ($shift->rosters()->exists()) {
            return redirect()->back()->with('error', 'Ca làm việc này đã được phân lịch cho nhân viên! Vui lòng chuyển trạng thái sang "Ngưng hoạt động" thay vì xóa.');
        }

        $shift->delete();
        return redirect()->back()->with('success', 'Xóa ca làm việc thành công!');
    }
}
