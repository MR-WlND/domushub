<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Apartment;
use App\Models\Vehicle;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Carbon\Carbon;

#[Signature('parking:calculate-fee')]
#[Description('Tự động tạo hóa đơn tiền gửi xe hàng tháng cho các căn hộ')]
class CalculateParkingFee extends Command
{
    public function handle()
    {
        $this->info('Bắt đầu tính phí gửi xe...');

        // Giả sử: Giá xe máy/điện 100k, giá ô tô 1500k
        $motoPrice = 100000;
        $carPrice = 1500000;

        $apartments = Apartment::where('status', 'occupied')->get();
        $countInvoices = 0;

        foreach ($apartments as $apartment) {
            // Đếm xe máy & xe điện active
            $motoCount = Vehicle::where('apartment_id', $apartment->id)
                ->whereIn('vehicle_type', ['motorbike', 'electric_bike'])
                ->where('status', 'active')
                ->count();

            // Đếm ô tô active (đã có lốt)
            $carCount = Vehicle::where('apartment_id', $apartment->id)
                ->where('vehicle_type', 'car')
                ->where('status', 'active')
                ->count();

            if ($motoCount == 0 && $carCount == 0) {
                continue;
            }

            $totalAmount = ($motoCount * $motoPrice) + ($carCount * $carPrice);

            // Ensure parking service price exists
            $parkingService = \App\Models\ServicePrice::firstOrCreate(
                ['type' => 'parking', 'status' => 'active'],
                [
                    'name' => 'Phí gửi xe',
                    'unit_price' => 0,
                    'description' => 'Phí trông giữ phương tiện hàng tháng'
                ]
            );

            // Tạo hóa đơn
            $invoice = Invoice::create([
                'apartment_id'  => $apartment->id,
                'title'         => 'Phí gửi xe tháng ' . Carbon::now()->format('m/Y'),
                'billing_month' => Carbon::now()->month,
                'billing_year'  => Carbon::now()->year,
                'total_amount'  => $totalAmount,
                'status'        => 'unpaid',
                'due_date'      => Carbon::now()->addDays(10), // Hạn nộp 10 ngày
            ]);

            // Thêm chi tiết hóa đơn
            if ($motoCount > 0) {
                InvoiceDetail::create([
                    'bill_id'          => $invoice->id,
                    'service_price_id' => $parkingService->id,
                    'quantity'         => $motoCount,
                    'amount'           => $motoCount * $motoPrice
                ]);
            }

            if ($carCount > 0) {
                InvoiceDetail::create([
                    'bill_id'          => $invoice->id,
                    'service_price_id' => $parkingService->id,
                    'quantity'         => $carCount,
                    'amount'           => $carCount * $carPrice
                ]);
            }

            $countInvoices++;
            $this->line("Đã tạo hóa đơn cho căn hộ: {$apartment->apartment_number} - Tổng: " . number_format($totalAmount) . " VND");
        }

        $this->info("Hoàn tất! Đã tạo $countInvoices hóa đơn.");
    }
}
