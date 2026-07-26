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

        // Đọc đơn giá từ bảng service_prices theo loại parking_fee
        $prices = \App\Models\ServicePrice::where('status', 'active')
            ->where('type', 'parking_fee')
            ->get();

        if ($prices->isEmpty()) {
            $this->warn('Chưa có đơn giá gửi xe nào được cấu hình (parking_fee). Vui lòng thêm trong Biểu giá dịch vụ.');
            return;
        }

        $apartments = Apartment::where('status', 'occupied')->get();
        $countInvoices = 0;

        foreach ($apartments as $apartment) {
            $totalAmount       = 0;
            $invoiceAmountAdded = 0;

            // Tìm hóa đơn hiện có trong tháng
            $invoice = Invoice::where('apartment_id', $apartment->id)
                ->where('billing_month', Carbon::now()->month)
                ->where('billing_year', Carbon::now()->year)
                ->first();

            foreach ($prices as $servicePrice) {
                $vType = $servicePrice->vehicle_type;
                if (!$vType) continue;

                // Đếm xe theo loại
                $vehicleCount = Vehicle::where('apartment_id', $apartment->id)
                    ->where('vehicle_type', $vType)
                    ->where('status', 'active')
                    ->count();

                if ($vehicleCount === 0) {
                    continue;
                }

                // Tạo hóa đơn nếu chưa tồn tại
                if (!$invoice) {
                    $invoice = Invoice::create([
                        'apartment_id'  => $apartment->id,
                        'title'         => 'Hóa đơn tháng ' . Carbon::now()->format('m/Y'),
                        'billing_month' => Carbon::now()->month,
                        'billing_year'  => Carbon::now()->year,
                        'total_amount'  => 0,
                        'status'        => 'unpaid',
                        'due_date'      => Carbon::now()->addDays(10),
                    ]);
                }

                // Xóa dòng cũ cùng type (nếu đã tồn tại) để tránh trùng
                InvoiceDetail::where('bill_id', $invoice->id)
                    ->where('service_price_id', $servicePrice->id)
                    ->delete();

                $detailAmount = $vehicleCount * $servicePrice->unit_price;
                $vLabels = [
                    'motorbike' => 'xe máy',
                    'electric_bike' => 'xe điện',
                    'car' => 'ô tô',
                    'bicycle' => 'xe đạp'
                ];
                $label = $vLabels[$vType] ?? 'xe';

                InvoiceDetail::create([
                    'bill_id'          => $invoice->id,
                    'service_price_id' => $servicePrice->id,
                    'quantity'         => $vehicleCount,
                    'amount'           => $detailAmount,
                    'note'             => "Phí gửi {$label}: " . number_format($servicePrice->unit_price, 0, ',', '.') . "đ/xe x {$vehicleCount} xe",
                ]);

                $invoiceAmountAdded += $detailAmount;
            }

            if ($invoice && $invoiceAmountAdded > 0) {
                // Cập nhật lại tổng tiền hóa đơn
                $detailTotal = InvoiceDetail::where('bill_id', $invoice->id)->sum('amount');
                $invoice->update(['total_amount' => $detailTotal]);
                $countInvoices++;
                $this->line("Căn hộ {$apartment->apartment_number}: " . number_format($invoiceAmountAdded) . " VND");
            }
        }

        $this->info("Hoàn tất! Đã xử lý $countInvoices căn hộ.");
    }
}
