<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\StaffSchedule;
use App\Models\Staff;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $startOfWeek = Carbon::parse($date)->startOfWeek(); // Monday
        $endOfWeek = Carbon::parse($date)->endOfWeek(); // Sunday

        $shifts = Shift::with('requirements')->get();
        $departments = \App\Models\Department::where('is_shift', true)->where('status', 'active')->get();
        $blocks = \App\Models\Block::orderBy('name')->get();

        // Get schedules for the week
        $schedules = StaffSchedule::with(['staff', 'shift', 'block', 'floor'])
            ->whereBetween('work_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->get();

        // Organize schedules by date and shift
        // $scheduleMatrix[date][shift_id][] = StaffSchedule
        $scheduleMatrix = [];
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $dayStr = $day->format('Y-m-d');
            $days[] = $day;
            
            foreach ($shifts as $shift) {
                $scheduleMatrix[$dayStr][$shift->id] = [];
            }
        }

        foreach ($schedules as $schedule) {
            $scheduleMatrix[$schedule->work_date->format('Y-m-d')][$schedule->shift_id][] = $schedule;
        }

        $today = Carbon::today()->format('Y-m-d');

        return view('admin.schedules.index', compact('days', 'shifts', 'departments', 'scheduleMatrix', 'startOfWeek', 'endOfWeek', 'date', 'today', 'blocks'));
    }

    public function updateRequirements(Request $request, Shift $shift)
    {
        $request->validate([
            'requirements' => 'array',
            'requirements.*' => 'integer|min:0'
        ]);

        if ($request->has('requirements')) {
            foreach ($request->requirements as $deptId => $reqCount) {
                \App\Models\ShiftRequirement::updateOrCreate(
                    ['shift_id' => $shift->id, 'department_id' => $deptId],
                    ['required_staff' => $reqCount]
                );
            }
        }

        \App\Helpers\SystemLogger::log('Cập nhật định mức ca', 'Ca: ' . $shift->name);

        return response()->json(['success' => true, 'message' => 'Đã cập nhật định mức thành công!']);
    }
}
