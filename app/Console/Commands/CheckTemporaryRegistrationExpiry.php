<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TemporaryRegistration;
use App\Models\Resident;

class CheckTemporaryRegistrationExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temporary-registration:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và tự động xử lý các đăng ký Tạm trú/Tạm vắng đã hết hạn.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredRegistrations = TemporaryRegistration::where('status', 'approved')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', today())
            ->get();

        $count = 0;

        foreach ($expiredRegistrations as $reg) {
            $resident = Resident::where('user_id', $reg->user_id)
                                ->where('apartment_id', $reg->apartment_id)
                                ->first();

            if ($resident) {
                if ($reg->type === 'residence' && $resident->temporary_status === 'temporary') {
                    // Người tạm trú đã hết hạn
                    $resident->temporary_status = null;
                    $resident->save();
                    $count++;
                    
                    $this->info("Đã cập nhật hết hạn tạm trú cho user {$reg->user_id} tại căn hộ {$reg->apartment_id}");
                } elseif ($reg->type === 'absence' && $resident->temporary_status === 'absent') {
                    // Người tạm vắng đã hết hạn vắng mặt (quay về)
                    $resident->temporary_status = null;
                    $resident->save();
                    $count++;
                    
                    $this->info("Đã cập nhật hết hạn tạm vắng cho user {$reg->user_id} tại căn hộ {$reg->apartment_id}");
                }
            }
        }

        $this->info("Hoàn tất kiểm tra. Đã xử lý $count đăng ký hết hạn.");
    }
}
