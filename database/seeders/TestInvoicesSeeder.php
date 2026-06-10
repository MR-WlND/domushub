<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\ServicePrice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestInvoicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy căn hộ đầu tiên
        $apartment = Apartment::first();
        $servicePrice = ServicePrice::first();

        if (!$apartment || !$servicePrice) {
            $this->command->error('Không tìm thấy căn hộ hoặc giá dịch vụ!');
            return;
        }

        $now = Carbon::now();

        // Hóa đơn 1: Phí quản lý tháng 6/2026 - 1,500,000đ - Chưa thanh toán
        $invoice1 = Invoice::create([
            'apartment_id' => $apartment->id,
            'title' => 'Phí quản lý tháng 06/2026',
            'billing_month' => 6,
            'billing_year' => 2026,
            'due_date' => Carbon::create(2026, 6, 25),
            'total_amount' => 1500000,
            'status' => 'unpaid',
        ]);

        InvoiceDetail::create([
            'bill_id' => $invoice1->id,
            'service_price_id' => $servicePrice->id,
            'quantity' => 1,
            'amount' => 1500000,
        ]);

        $this->command->line('✓ Hóa đơn 1: ' . $invoice1->title . ' - ' . number_format($invoice1->total_amount, 0, ',', '.') . 'đ');

        // Hóa đơn 2: Tiền điện nước tháng 5/2026 - 2,000,000đ - Quá hạn
        $invoice2 = Invoice::create([
            'apartment_id' => $apartment->id,
            'title' => 'Tiền điện nước tháng 05/2026',
            'billing_month' => 5,
            'billing_year' => 2026,
            'due_date' => Carbon::create(2026, 5, 15),
            'total_amount' => 2000000,
            'status' => 'unpaid',
        ]);

        InvoiceDetail::create([
            'bill_id' => $invoice2->id,
            'service_price_id' => $servicePrice->id,
            'quantity' => 1,
            'amount' => 2000000,
        ]);

        $this->command->line('✓ Hóa đơn 2: ' . $invoice2->title . ' - ' . number_format($invoice2->total_amount, 0, ',', '.') . 'đ');

        // Hóa đơn 3: Phí gửi xe tháng 4/2026 - 800,000đ - Quá hạn
        $invoice3 = Invoice::create([
            'apartment_id' => $apartment->id,
            'title' => 'Phí gửi xe tháng 04/2026',
            'billing_month' => 4,
            'billing_year' => 2026,
            'due_date' => Carbon::create(2026, 4, 30),
            'total_amount' => 800000,
            'status' => 'unpaid',
        ]);

        InvoiceDetail::create([
            'bill_id' => $invoice3->id,
            'service_price_id' => $servicePrice->id,
            'quantity' => 1,
            'amount' => 800000,
        ]);

        $this->command->line('✓ Hóa đơn 3: ' . $invoice3->title . ' - ' . number_format($invoice3->total_amount, 0, ',', '.') . 'đ');

        // Hóa đơn 4: Phí bảo vệ tháng 3/2026 - 500,000đ - Quá hạn
        $invoice4 = Invoice::create([
            'apartment_id' => $apartment->id,
            'title' => 'Phí bảo vệ tháng 03/2026',
            'billing_month' => 3,
            'billing_year' => 2026,
            'due_date' => Carbon::create(2026, 3, 31),
            'total_amount' => 500000,
            'status' => 'unpaid',
        ]);

        InvoiceDetail::create([
            'bill_id' => $invoice4->id,
            'service_price_id' => $servicePrice->id,
            'quantity' => 1,
            'amount' => 500000,
        ]);

        $this->command->line('✓ Hóa đơn 4: ' . $invoice4->title . ' - ' . number_format($invoice4->total_amount, 0, ',', '.') . 'đ');

        // Summary
        $this->command->newLine();
        $this->command->info('📊 Tóm tắt:');
        $this->command->line('✓ Đã tạo 4 hóa đơn chưa thanh toán');
        $this->command->line('✓ Căn hộ: ' . $apartment->apartment_number . ' (' . $apartment->floor->block->name . ')');
        $totalAmount = 1500000 + 2000000 + 800000 + 500000;
        $this->command->line('✓ Tổng tiền: ' . number_format($totalAmount, 0, ',', '.') . 'đ');
    }
}
