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
        // 1. Check for expired registrations (end_date < today)
        $expiredRegistrations = TemporaryRegistration::with(['user', 'apartment'])
            ->where('status', 'approved')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', today())
            ->get();

        $countExpired = 0;

        foreach ($expiredRegistrations as $reg) {
            $reg->status = 'expired';
            $reg->save();
            
            $resident = Resident::where('user_id', $reg->user_id)
                                ->where('apartment_id', $reg->apartment_id)
                                ->first();

            if ($resident) {
                if ($reg->type === 'residence' && $resident->temporary_status === 'temporary') {
                    $resident->temporary_status = null;
                    $resident->save();
                    $this->info("Đã cập nhật hết hạn tạm trú cho user {$reg->user_id} tại căn hộ {$reg->apartment_id}");
                } elseif ($reg->type === 'absence' && $resident->temporary_status === 'absent') {
                    $resident->temporary_status = null;
                    $resident->save();
                    $this->info("Đã cập nhật hết hạn tạm vắng cho user {$reg->user_id} tại căn hộ {$reg->apartment_id}");
                }
            }
            
            if ($reg->user) {
                $reg->user->notify(new \App\Notifications\TemporaryRegistrationExpiredNotification($reg));
            }
            $countExpired++;
        }

        // 2. Check for expiring registrations (end_date == today + 3 days OR today + 7 days)
        $expiringRegistrations = TemporaryRegistration::with(['user', 'apartment'])
            ->where('status', 'approved')
            ->whereNotNull('end_date')
            ->where(function($q) {
                $q->whereDate('end_date', today()->addDays(3))
                  ->orWhereDate('end_date', today()->addDays(7));
            })
            ->get();
            
        $countExpiring = 0;
        foreach ($expiringRegistrations as $reg) {
            if ($reg->user) {
                $reg->user->notify(new \App\Notifications\TemporaryRegistrationExpiringNotification($reg));
            }
            $countExpiring++;
            $this->info("Đã gửi thông báo sắp hết hạn cho đơn {$reg->id}");
        }

        $this->info("Hoàn tất kiểm tra. Đã xử lý $countExpired đăng ký hết hạn và nhắc nhở $countExpiring đơn sắp hết hạn.");
    }
}
