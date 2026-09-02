<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FacilityBooking;
use App\Models\Invoice;
use Carbon\Carbon;

class CheckFacilityBookingExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-facility-booking-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và cập nhật trạng thái các lượt đặt tiện ích đã quá hạn';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nowDate = Carbon::now()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');

        $this->info("Bắt đầu kiểm tra lượt đặt tiện ích quá hạn vào lúc {$nowDate} {$nowTime}...");

        // 1. Pending (Chờ duyệt) mà quá hạn -> Cancelled (Đã hủy)
        $expiredPending = FacilityBooking::where('status', 'pending')
            ->where(function ($q) use ($nowDate, $nowTime) {
                $q->where('booking_date', '<', $nowDate)
                  ->orWhere(function ($q2) use ($nowDate, $nowTime) {
                      $q2->where('booking_date', $nowDate)
                         ->where('end_time', '<', $nowTime);
                  });
            })
            ->get();

        $countPending = 0;
        foreach ($expiredPending as $booking) {
            $booking->update(['status' => 'cancelled']);
            
            // Hủy hóa đơn liên quan nếu chưa thanh toán
            if ($booking->bill_id) {
                $invoice = Invoice::find($booking->bill_id);
                if ($invoice && !in_array($invoice->status, ['paid', 'cancelled'])) {
                    $invoice->update(['status' => 'cancelled']);
                }
            }
            $countPending++;
        }
        $this->info("- Đã hủy {$countPending} lượt đặt 'Chờ duyệt' quá hạn.");

        // 2. Approved (Đã xác nhận) mà quá hạn -> Used (Đã hoàn thành)
        $expiredApproved = FacilityBooking::where('status', 'approved')
            ->where(function ($q) use ($nowDate, $nowTime) {
                $q->where('booking_date', '<', $nowDate)
                  ->orWhere(function ($q2) use ($nowDate, $nowTime) {
                      $q2->where('booking_date', $nowDate)
                         ->where('end_time', '<', $nowTime);
                  });
            })
            ->update(['status' => 'used']);

        $this->info("- Đã chuyển {$expiredApproved} lượt đặt 'Đã xác nhận' quá hạn thành 'Đã hoàn thành'.");
        
        $this->info('Hoàn tất!');
    }
}
