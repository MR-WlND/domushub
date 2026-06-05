<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Vehicle;
use App\Models\Invoice;
use Carbon\Carbon;

#[Signature('vehicles:check-renewal')]
#[Description('Quét hóa đơn phí gửi xe quá hạn và chuyển xe tương ứng sang pending_renewal')]
class CheckVehicleRenewal extends Command
{
    public function handle(): void
    {
        $this->info('[' . Carbon::now()->format('Y-m-d H:i') . '] Bắt đầu quét hóa đơn phí gửi xe quá hạn...');

        // Tìm các hóa đơn phí gửi xe CHƯA thanh toán và đã quá hạn
        // Hóa đơn được tạo bởi CalculateParkingFee (title chứa "Phí gửi xe")
        $overdueInvoices = Invoice::where('status', 'unpaid')
            ->where('due_date', '<', Carbon::now())
            ->where('title', 'like', 'Phí gửi xe%')
            ->get();

        if ($overdueInvoices->isEmpty()) {
            $this->info('✓ Không có hóa đơn phí gửi xe nào quá hạn.');
            return;
        }

        $this->info("Tìm thấy {$overdueInvoices->count()} hóa đơn quá hạn. Đang xử lý...");

        $totalUpdated = 0;

        foreach ($overdueInvoices as $invoice) {
            // Lấy tất cả xe đang active thuộc căn hộ có hóa đơn quá hạn
            $vehicles = Vehicle::where('apartment_id', $invoice->apartment_id)
                ->where('status', 'active')
                ->get();

            if ($vehicles->isEmpty()) {
                continue;
            }

            foreach ($vehicles as $vehicle) {
                $vehicle->update(['status' => 'pending_renewal']);
                $totalUpdated++;
                $apartmentNumber = $vehicle->apartment->apartment_number ?? $invoice->apartment_id;
                $this->line(
                    "  → Xe " . $vehicle->license_plate . " (" . $vehicle->typeLabel() . ") " .
                    "của căn hộ " . $apartmentNumber . " " .
                    "→ pending_renewal"
                );
            }
        }

        $this->info("✓ Hoàn tất! Đã cập nhật $totalUpdated phương tiện sang trạng thái chờ gia hạn phí.");
    }
}
