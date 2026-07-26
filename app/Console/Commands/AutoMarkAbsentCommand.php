<?php

namespace App\Console\Commands;

use App\Helpers\SystemLogger;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * AutoMarkAbsentCommand
 *
 * Chạy mỗi ngày lúc 23:55 — tự động tạo bản ghi "vắng mặt" cho tất cả nhân viên
 * nội bộ active mà chưa có bản ghi chấm công trong ngày hôm nay.
 *
 * Quy tắc:
 *  - Chỉ áp dụng cho ngày hôm nay (không áp dụng hồi tố).
 *  - Không ghi đè nếu đã có bản ghi (dù là working/present/late/half_day).
 *  - Ghi log hệ thống với số lượng bản ghi được tạo.
 *
 * Đăng ký trong routes/console.php:
 *   Schedule::command('attendance:auto-absent')->dailyAt('23:55');
 */
class AutoMarkAbsentCommand extends Command
{
    protected $signature   = 'attendance:auto-absent {--date= : Ngày muốn xử lý (Y-m-d), mặc định là hôm nay}';
    protected $description = 'Tự động đánh vắng mặt cho nhân viên không có bản ghi chấm công trong ngày';

    private const STAFF_ROLES = ['admin', 'manager', 'staff', 'technician', 'security', 'cleaning'];

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : today()->toDateString();

        $this->info("⏰ Auto-absent cho ngày: {$date}");

        // Lấy danh sách user_id đã có bản ghi trong ngày
        $checkedInIds = AttendanceRecord::where('work_date', $date)
            ->pluck('user_id')
            ->toArray();

        // Lấy nhân viên nội bộ active chưa chấm công
        $absentStaff = User::whereIn('role', self::STAFF_ROLES)
            ->where('status', 'active')
            ->whereNotIn('id', $checkedInIds)
            ->get();

        if ($absentStaff->isEmpty()) {
            $this->info('✅ Tất cả nhân viên đã chấm công. Không cần tạo bản ghi vắng.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($absentStaff as $user) {
            AttendanceRecord::create([
                'user_id'       => $user->id,
                'work_date'     => $date,
                'check_in_at'   => null,
                'check_out_at'  => null,
                'status'        => 'absent',
                'shift'         => 'full_day',
                'working_hours' => null,
                'late_minutes'  => null,
                'note'          => 'Tự động ghi nhận bởi hệ thống (không có bản ghi check-in)',
                'recorded_by'   => null, // system
            ]);
            $count++;
            $this->line("  → Đã đánh vắng: {$user->name} ({$user->role})");
        }

        SystemLogger::log(
            'Auto-Absent',
            "Ngày {$date} — Đã tự động đánh vắng {$count} nhân viên không chấm công."
        );

        $this->info("✅ Hoàn tất: {$count} bản ghi vắng mặt được tạo.");

        return self::SUCCESS;
    }
}
