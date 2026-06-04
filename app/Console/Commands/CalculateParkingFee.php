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

            // Tạo hóa đơn
            $invoice = Invoice::create([
                'apartment_id' => $apartment->id,
                'title' => 'Phí gửi xe tháng ' . Carbon::now()->format('m/Y'),
                'amount' => $totalAmount,
                'status' => 'unpaid',
                'due_date' => Carbon::now()->addDays(10), // Hạn nộp 10 ngày
            ]);

            // Thêm chi tiết hóa đơn
            if ($motoCount > 0) {
                InvoiceDetail::create([
                    'invoice_id' => $invoice->id,
                    'description' => "Phí gửi xe máy/xe điện ($motoCount xe)",
                    'amount' => $motoCount * $motoPrice
                ]);
            }

            if ($carCount > 0) {
                InvoiceDetail::create([
                    'invoice_id' => $invoice->id,
                    'description' => "Phí gửi xe ô tô ($carCount xe)",
                    'amount' => $carCount * $carPrice
                ]);
            }

            $countInvoices++;
            $this->line("Đã tạo hóa đơn cho căn hộ: {$apartment->apartment_number} - Tổng: " . number_format($totalAmount) . " VND");
        }

        $this->info("Hoàn tất! Đã tạo $countInvoices hóa đơn.");
    }
}
