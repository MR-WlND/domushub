<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Payment;
use App\Models\ServicePrice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        
        $month = $lastMonth->month;
        $year = $lastMonth->year;
        
        // Find apartments that have residents
        $apartments = Apartment::whereHas('residents')->get();
        
        if ($apartments->isEmpty()) {
            $this->command->error('No apartments with residents found!');
            return;
        }

        $waterPrice = ServicePrice::where('type', 'water')->first();
        $managementPrice = ServicePrice::where('type', 'management_fee')->first();
        $internetPrice = ServicePrice::where('type', 'internet')->first();

        foreach ($apartments as $apt) {
            $existing = Invoice::where('apartment_id', $apt->id)
                ->where('billing_month', $month)
                ->where('billing_year', $year)
                ->first();
                
            if ($existing) {
                Payment::where('bill_id', $existing->id)->delete();
                InvoiceDetail::where('bill_id', $existing->id)->delete();
                $existing->delete();
            }

            $totalAmount = 0;
            $paidAmount = 0;
            $details = [];

            if ($managementPrice) {
                $details[] = [
                    'service_price_id' => $managementPrice->id,
                    'quantity' => 1,
                    'amount' => 250000,
                    'status' => 'paid',
                ];
                $totalAmount += 250000;
                $paidAmount += 250000;
            }

            if ($waterPrice) {
                $details[] = [
                    'service_price_id' => $waterPrice->id,
                    'quantity' => 15,
                    'amount' => 120000,
                    'status' => 'unpaid',
                ];
                $totalAmount += 120000;
            }

            if ($internetPrice) {
                $details[] = [
                    'service_price_id' => $internetPrice->id,
                    'quantity' => 1,
                    'amount' => 150000,
                    'status' => 'unpaid',
                ];
                $totalAmount += 150000;
            }

            if ($totalAmount === 0) continue;

            $payerName = $apt->residents->first()->user->name ?? 'Cư dân';

            $invoice = Invoice::create([
                'apartment_id'  => $apt->id,
                'title'         => "Hóa đơn phí dịch vụ Tháng " . str_pad($month, 2, '0', STR_PAD_LEFT) . "/{$year}",
                'billing_month' => $month,
                'billing_year'  => $year,
                'due_date'      => $lastMonth->copy()->endOfMonth(),
                'total_amount'  => $totalAmount,
                'paid_amount'   => $paidAmount,
                'status'        => 'partial_paid',
            ]);

            foreach ($details as $d) {
                InvoiceDetail::create([
                    'bill_id'          => $invoice->id,
                    'service_price_id' => $d['service_price_id'],
                    'quantity'         => $d['quantity'],
                    'amount'           => $d['amount'],
                    'status'           => $d['status'],
                ]);
            }

            if ($paidAmount > 0) {
                Payment::create([
                    'bill_id'          => $invoice->id,
                    'amount'           => $paidAmount,
                    'payment_method'   => 'vnpay',
                    'transaction_code' => 'TXN-TEST-' . $apt->id . '-' . time(),
                    'receipt_code'     => 'REC-TEST-' . $apt->id . '-' . time(),
                    'status'           => 'success',
                    'paid_at'          => $lastMonth->copy()->addDays(15),
                    'note'             => 'Thanh toán 1 phần mẫu để test UI',
                    'payer_name'       => $payerName,
                ]);
            }
        }

        $this->command->info("Đã tạo hóa đơn tháng trước ({$month}/{$year}) THIẾU/THANH TOÁN 1 PHẦN cho các căn hộ thành công!");
    }
}
