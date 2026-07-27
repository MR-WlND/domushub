<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\StaffSchedule;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StaffScheduleController extends Controller
{
    /**
     * Lấy danh sách nhân viên theo phòng ban (REST API cho Tom Select)
     */
    public function getStaffs(Request $request)
    {
        $departmentId = $request->input('department_id');
        $query = Staff::where('status', 'active');
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        } else {
            // Chỉ lấy nhân sự thuộc các phòng ban có phân ca
            $query->whereHas('department', function ($q) {
                $q->where('is_shift', true)->where('status', 'active');
            });
        }
        
        $staffs = $query->get(['id', 'full_name as text']);
        
        return response()->json($staffs);
    }

    /**
     * Phân ca (Thêm nhân viên vào ca)
     */
    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staffs,id',
            'shift_id' => 'required|exists:shifts,id',
            'work_date' => 'required|date',
        ]);

        $shift = Shift::findOrFail($request->shift_id);
        $workDate = $request->work_date;
        $staffId = $request->staff_id;
        
        // Thời gian bắt đầu và kết thúc của ca đang định gán
        // Cần xử lý vắt ngang đêm nếu end_time < start_time
        $startB = Carbon::parse($workDate . ' ' . $shift->start_time);
        $endB = Carbon::parse($workDate . ' ' . $shift->end_time);
        if ($endB <= $startB) {
            $endB->addDay();
        }

        // Lấy tất cả các ca trong ngày đó của nhân viên (và ngày hôm trước để check ca đêm vắt qua)
        $previousDate = Carbon::parse($workDate)->subDay()->format('Y-m-d');
        
        $existingSchedules = StaffSchedule::where('staff_id', $staffId)
            ->whereIn('work_date', [$previousDate, $workDate])
            ->with('shift')
            ->get();

        foreach ($existingSchedules as $schedule) {
            $exShift = $schedule->shift;
            $startA = Carbon::parse($schedule->work_date->format('Y-m-d') . ' ' . $exShift->start_time);
            $endA = Carbon::parse($schedule->work_date->format('Y-m-d') . ' ' . $exShift->end_time);
            if ($endA <= $startA) {
                $endA->addDay();
            }

            // Công thức kiểm tra giao nhau: startA < endB AND endA > startB
            if ($startA < $endB && $endA > $startB) {
                return response()->json([
                    'success' => false, 
                    'message' => "Nhân viên đã có lịch trực từ {$startA->format('H:i')} đến {$endA->format('H:i')}, trùng thời gian với ca này."
                ]);
            }
        }

        // Nếu hợp lệ, lưu vào
        $schedule = StaffSchedule::create([
            'staff_id' => $staffId,
            'shift_id' => $shift->id,
            'work_date' => $workDate,
            'is_leader' => false
        ]);

        $schedule->load('staff.department');

        return response()->json([
            'success' => true, 
            'message' => 'Đã gán ca thành công!',
            'assignment' => [
                'id' => $schedule->id,
                'staff_name' => $schedule->staff->full_name,
                'department_id' => $schedule->staff->department_id,
                'department_name' => optional($schedule->staff->department)->name,
                'is_leader' => $schedule->is_leader
            ]
        ]);
    }

    /**
     * Hủy phân ca
     */
    public function destroy(StaffSchedule $staff_schedule)
    {
        $staff_schedule->delete();
        return response()->json(['success' => true, 'message' => 'Đã gỡ nhân sự khỏi ca!']);
    }

    /**
     * Đổi trưởng ca
     */
    public function toggleLeader(StaffSchedule $staff_schedule)
    {
        $staff_schedule->update(['is_leader' => !$staff_schedule->is_leader]);
        return response()->json([
            'success' => true, 
            'message' => 'Đã cập nhật trưởng ca!',
            'is_leader' => $staff_schedule->is_leader
        ]);
    }

    /**
     * Sao chép lịch (hỗ trợ copy ngày → nhiều ngày, mode skip/overwrite)
     */
    public function copy(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_dates' => 'required|array|min:1',
            'to_dates.*' => 'date|different:from_date',
            'mode' => 'in:skip,overwrite',
        ]);

        $mode = $request->input('mode', 'skip');
        $fromSchedules = StaffSchedule::with('shift')
            ->whereDate('work_date', $request->from_date)
            ->get();

        if ($fromSchedules->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có dữ liệu lịch ở ngày nguồn.']);
        }

        $totalCreated = 0;
        $totalSkipped = 0;
        $totalOverwritten = 0;
        $targetDays = count($request->to_dates);

        DB::transaction(function () use ($fromSchedules, $request, $mode, &$totalCreated, &$totalSkipped, &$totalOverwritten) {
            foreach ($request->to_dates as $toDate) {
                if ($mode === 'overwrite') {
                    $deleted = StaffSchedule::whereDate('work_date', $toDate)->delete();
                    $totalOverwritten += $deleted;
                }

                foreach ($fromSchedules as $sc) {
                    if ($mode === 'skip') {
                        $exists = StaffSchedule::where('staff_id', $sc->staff_id)
                            ->where('shift_id', $sc->shift_id)
                            ->where('work_date', $toDate)
                            ->exists();

                        if ($exists) {
                            $totalSkipped++;
                            continue;
                        }

                        $overlapMessage = $this->checkOverlap($sc->staff_id, $sc->shift, $toDate);
                        if ($overlapMessage) {
                            $totalSkipped++;
                            continue;
                        }
                    }

                    StaffSchedule::create([
                        'staff_id' => $sc->staff_id,
                        'shift_id' => $sc->shift_id,
                        'work_date' => $toDate,
                        'is_leader' => $sc->is_leader
                    ]);
                    $totalCreated++;
                }
            }
        });

        $msg = "Sao chép thành công {$totalCreated} phân công → {$targetDays} ngày.";
        if ($totalSkipped > 0) {
            $msg .= " Bỏ qua {$totalSkipped} do trùng.";
        }
        if ($totalOverwritten > 0) {
            $msg .= " Ghi đè {$totalOverwritten} bản ghi cũ.";
        }

        \App\Helpers\SystemLogger::log('Sao chép lịch phân ca', "Từ {$request->from_date} → {$targetDays} ngày: {$totalCreated} bản ghi");

        return response()->json(['success' => true, 'message' => $msg]);
    }

    /**
     * Preview: đếm số bản ghi nguồn sẽ copy
     */
    public function previewCopy(Request $request)
    {
        $request->validate(['from_date' => 'required|date']);

        $schedules = StaffSchedule::with('shift')
            ->whereDate('work_date', $request->from_date)
            ->get();

        $breakdown = $schedules->groupBy('shift_id')->map(fn($group) => [
            'shift_name' => $group->first()->shift->name,
            'count' => $group->count()
        ])->values();

        return response()->json([
            'total' => $schedules->count(),
            'breakdown' => $breakdown,
        ]);
    }
}
