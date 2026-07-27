<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Department;
use App\Models\Shift;
use App\Models\ShiftRoster;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftRosterController extends Controller
{
    public function index(Request $request)
    {
        // Tuần đang xem (Default: tuần hiện tại)
        $selectedDate = $request->input('week') ? Carbon::parse($request->input('week')) : Carbon::now();
        $startOfWeek = $selectedDate->copy()->startOfWeek(); // Monday
        $endOfWeek = $selectedDate->copy()->endOfWeek(); // Sunday

        // 7 ngày trong tuần
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $weekDays[] = $startOfWeek->copy()->addDays($i);
        }

        // Filters
        $departmentId = $request->input('department_id');
        $blockId = $request->input('block_id');
        $search = $request->input('search');

        // Danh sách nhân viên
        $staffQuery = Staff::with('department')->where('status', 'active');

        if ($departmentId) {
            $staffQuery->where('department_id', $departmentId);
        }

        if ($search) {
            $staffQuery->where('full_name', 'LIKE', '%' . $search . '%');
        }

        $staffs = $staffQuery->orderBy('full_name')->get();

        // Danh sách phân ca trong tuần
        $rostersQuery = ShiftRoster::with(['shift', 'block'])
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()]);

        if ($blockId) {
            $rostersQuery->where('block_id', $blockId);
        }

        $rosters = $rostersQuery->get();

        // Key rosters by staff_id and date string for quick lookup
        $rosterMap = [];
        foreach ($rosters as $roster) {
            $dateStr = Carbon::parse($roster->date)->toDateString();
            $rosterMap[$roster->staff_id][$dateStr][] = $roster;
        }

        $shifts = Shift::where('status', 'active')->get();
        $blocks = Block::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.shift_rosters.index', compact(
            'staffs',
            'weekDays',
            'startOfWeek',
            'endOfWeek',
            'rosterMap',
            'shifts',
            'blocks',
            'departments',
            'departmentId',
            'blockId',
            'search'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staffs,id',
            'shift_id' => 'required|exists:shifts,id',
            'block_id' => 'nullable|exists:blocks,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        ShiftRoster::updateOrCreate(
            [
                'staff_id' => $validated['staff_id'],
                'date' => $validated['date'],
            ],
            [
                'shift_id' => $validated['shift_id'],
                'block_id' => $validated['block_id'] ?? null,
                'status' => 'scheduled',
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Phân ca làm việc thành công!');
    }

    public function update(Request $request, ShiftRoster $shiftRoster)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'block_id' => 'nullable|exists:blocks,id',
            'notes' => 'nullable|string',
        ]);

        $shiftRoster->update($validated);

        return redirect()->back()->with('success', 'Cập nhật lịch trực thành công!');
    }

    public function destroy(ShiftRoster $shiftRoster)
    {
        // Kiểm tra xem đã có attendance records chưa
        if ($shiftRoster->attendanceRecords()->exists()) {
            $shiftRoster->update(['status' => 'cancelled']);
            return redirect()->back()->with('success', 'Đã hủy lịch trực (giữ lại lịch sử điểm danh)!');
        }

        $shiftRoster->delete();
        return redirect()->back()->with('success', 'Đã gỡ lịch trực thành công!');
    }

    public function copyWeek(Request $request)
    {
        $targetWeek = Carbon::parse($request->input('target_week', Carbon::now()->toDateString()));
        $prevWeekStart = $targetWeek->copy()->subWeek()->startOfWeek();
        $prevWeekEnd = $prevWeekStart->copy()->endOfWeek();

        $prevRosters = ShiftRoster::whereBetween('date', [$prevWeekStart->toDateString(), $prevWeekEnd->toDateString()])->get();

        if ($prevRosters->isEmpty()) {
            return redirect()->back()->with('error', 'Không tìm thấy dữ liệu phân ca của tuần trước để sao chép!');
        }

        $copiedCount = 0;

        foreach ($prevRosters as $prev) {
            $dayOffset = Carbon::parse($prev->date)->dayOfWeekIso - 1; // 0 for Mon, 6 for Sun
            $newDate = $targetWeek->copy()->startOfWeek()->addDays($dayOffset)->toDateString();

            $exists = ShiftRoster::where('staff_id', $prev->staff_id)
                ->where('date', $newDate)
                ->exists();

            if (!$exists) {
                ShiftRoster::create([
                    'staff_id' => $prev->staff_id,
                    'shift_id' => $prev->shift_id,
                    'block_id' => $prev->block_id,
                    'date' => $newDate,
                    'status' => 'scheduled',
                    'notes' => $prev->notes,
                ]);
                $copiedCount++;
            }
        }

        return redirect()->back()->with('success', "Đã sao chép {$copiedCount} ca làm việc từ tuần trước thành công!");
    }
}
