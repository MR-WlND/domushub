<?php

namespace App\Console\Commands;

use App\Models\Resident;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckResidentLeasesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'residents:check-leases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra hợp đồng của khách thuê và đổi trạng thái thành inactive nếu quá hạn';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredResidents = Resident::where('relationship', 'tenant')
            ->whereNotNull('end_date')
            ->where('end_date', '<', today())
            ->where('status', 'active')
            ->get();

        $count = $expiredResidents->count();

        foreach ($expiredResidents as $resident) {
            $resident->update(['status' => 'inactive']);
            // Tùy chọn: Xoá mềm (soft-delete) nếu muốn khách thuê mất hẳn căn hộ
            // $resident->delete();
        }

        if ($count > 0) {
            Log::info("Đã kiểm tra và vô hiệu hoá {$count} khách thuê hết hạn hợp đồng.");
            $this->info("Đã vô hiệu hoá {$count} khách thuê.");
        } else {
            $this->info("Không có khách thuê nào hết hạn hợp đồng.");
        }
    }
}
