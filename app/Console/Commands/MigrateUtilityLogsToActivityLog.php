<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class MigrateUtilityLogsToActivityLog extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'utility-logs:migrate
                            {--dry-run : Chỉ hiển thị số lượng sẽ migrate, không thực sự ghi dữ liệu}
                            {--chunk=200 : Số bản ghi xử lý mỗi lần}';

    /**
     * The console command description.
     */
    protected $description = 'Chuyển đổi (migrate) lịch sử ghi số điện nước từ bảng utility_meter_logs sang bảng activity_log (Spatie)';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $chunkSize = (int) $this->option('chunk');

        $total = DB::table('utility_meter_logs')->count();

        if ($total === 0) {
            $this->info('✅ Bảng utility_meter_logs trống, không có gì để migrate.');
            return self::SUCCESS;
        }

        $this->info("📋 Tổng số bản ghi cần migrate: {$total}");

        if ($isDryRun) {
            $this->warn("⚠️  Đây là dry-run, không có dữ liệu nào được ghi vào database.");
            return self::SUCCESS;
        }

        if (!$this->confirm("Bạn có chắc muốn chuyển {$total} bản ghi vào bảng activity_log?")) {
            $this->info('Đã hủy.');
            return self::SUCCESS;
        }

        $migrated = 0;
        $skipped  = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        DB::table('utility_meter_logs')
            ->orderBy('id')
            ->chunk($chunkSize, function ($rows) use (&$migrated, &$skipped, $bar) {
                foreach ($rows as $row) {
                    // Kiểm tra xem đã được migrate chưa (tránh trùng lặp)
                    $exists = Activity::where('log_name', 'utility')
                        ->where('properties->utility_meter_id', $row->utility_meter_id)
                        ->where('properties->action', $row->action)
                        ->where('created_at', $row->created_at)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }

                    $desc = 'Ghi nhận số kỳ mới: ' . number_format($row->new_value);
                    if ($row->action === 'updated') $desc = 'Cập nhật chỉ số điện nước';
                    if ($row->action === 'approved') $desc = 'Đã duyệt & chốt số kỳ này';
                    if ($row->action === 'rejected') $desc = 'Từ chối chốt số';

                    Activity::create([
                        'log_name'      => 'utility',
                        'description'   => $desc,
                        'subject_type'  => 'App\\Models\\UtilityMeter',
                        'subject_id'    => $row->utility_meter_id,
                        'causer_type'   => $row->user_id ? 'App\\Models\\User' : null,
                        'causer_id'     => $row->user_id,
                        'properties'    => json_encode([
                            'utility_meter_id' => $row->utility_meter_id,
                            'apartment_id'     => $row->apartment_id,
                            'user_id'          => $row->user_id,
                            'type'             => $row->type,
                            'record_month'     => $row->record_month,
                            'record_year'      => $row->record_year,
                            'old_value'        => $row->old_value,
                            'new_value'        => $row->new_value,
                            'action'           => $row->action,
                            'reject_reason'    => $row->reject_reason,
                            'migrated_from'    => 'utility_meter_logs',
                        ]),
                        'created_at'    => $row->created_at,
                        'updated_at'    => $row->updated_at,
                        'batch_uuid'    => null,
                        'event'         => $row->action,
                    ]);

                    $migrated++;
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();

        $this->info("✅ Hoàn tất! Đã migrate: {$migrated} | Bỏ qua (đã tồn tại): {$skipped}");

        return self::SUCCESS;
    }
}
