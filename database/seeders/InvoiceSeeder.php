<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Payment;
use App\Models\ServicePrice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dọn dẹp dữ liệu hóa đơn, chi tiết hóa đơn, thanh toán và mối quan hệ cư dân cũ
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('payments')->truncate();
        DB::table('bill_details')->truncate();
        DB::table('bills')->truncate();
        DB::table('residents')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Đảm bảo các dữ liệu nền cần thiết đã chạy
        if (User::where('role', 'resident')->count() === 0) {
            $this->call(ResidentSeeder::class);
        }
        if (ServicePrice::count() === 0) {
            $this->call(ServicePriceSeeder::class);
        }
        if (Apartment::count() === 0) {
            $this->call(BlockFloorApartmentSeeder::class);
        }

        $now = Carbon::now();

        // 3. Liên kết Cư Dân A với căn hộ A101 (Tòa A, tầng 1, phòng 101)
        $apartmentA = Apartment::whereHas('floor.block', function ($q) {
            $q->where('code', 'A');
        })->where('apartment_number', '101')->first();

        $userA = User::where('email', 'resident.a@example.com')->first();

        if ($userA && $apartmentA) {
            DB::table('residents')->insert([
                'user_id'          => $userA->id,
                'apartment_id'     => $apartmentA->id,
                'relationship'     => 'owner',
                'temporary_status' => 'permanent',
                'start_date'       => $now->toDateString(),
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            // Lấy phí dịch vụ để gán vào chi tiết hóa đơn
            $servicePrice = ServicePrice::where('type', 'service')->first() ?? ServicePrice::first();

            // --- TẠO HÓA ĐƠN CHƯA THANH TOÁN (100,000đ) để test chức năng thanh toán ---
            $unpaidBill = Invoice::create([
                'apartment_id'  => $apartmentA->id,
                'title'         => 'Phí quản lý thử nghiệm tháng 06/2026',
                'billing_month' => 6,
                'billing_year'  => 2026,
                'due_date'      => Carbon::create(2026, 6, 25),
                'total_amount'  => 100000,
                'status'        => 'unpaid',
            ]);

            if ($servicePrice) {
                InvoiceDetail::create([
                    'bill_id'          => $unpaidBill->id,
                    'service_price_id' => $servicePrice->id,
                    'quantity'         => 1,
                    'amount'           => 100000,
                ]);
            }

            // --- TẠO HÓA ĐƠN QUÁ HẠN (100,000đ) để test hiển thị quá hạn và thanh toán ---
            $overdueBill = Invoice::create([
                'apartment_id'  => $apartmentA->id,
                'title'         => 'Tiền điện nước thử nghiệm tháng 05/2026',
                'billing_month' => 5,
                'billing_year'  => 2026,
                'due_date'      => Carbon::create(2026, 5, 15),
                'total_amount'  => 100000,
                'status'        => 'overdue',
            ]);

            if ($servicePrice) {
                InvoiceDetail::create([
                    'bill_id'          => $overdueBill->id,
                    'service_price_id' => $servicePrice->id,
                    'quantity'         => 1,
                    'amount'           => 100000,
                ]);
            }

            // --- TẠO HÓA ĐƠN ĐÃ THANH TOÁN (100,000đ) để test lịch sử thanh toán ---
            $paidBill = Invoice::create([
                'apartment_id'  => $apartmentA->id,
                'title'         => 'Phí gửi xe thử nghiệm tháng 05/2026',
                'billing_month' => 5,
                'billing_year'  => 2026,
                'due_date'      => Carbon::create(2026, 5, 15),
                'total_amount'  => 100000,
                'status'        => 'paid',
            ]);

            if ($servicePrice) {
                InvoiceDetail::create([
                    'bill_id'          => $paidBill->id,
                    'service_price_id' => $servicePrice->id,
                    'quantity'         => 1,
                    'amount'           => 100000,
                ]);
            }

            Payment::create([
                'bill_id'          => $paidBill->id,
                'amount'           => 100000,
                'payment_method'   => 'bank_transfer',
                'transaction_code' => 'TEST-PAID-001',
                'receipt_code'     => 'REC-20260510-TEST1',
                'vnp_txn_ref'      => null, // Không có mã VNPay vì là bank_transfer
                'status'           => 'success',
                'paid_at'          => Carbon::create(2026, 5, 10),
            ]);
        }

        // 4. Liên kết Cư Dân B với căn hộ A201 (Tòa A, tầng 2, phòng 201)
        $apartmentB = Apartment::whereHas('floor.block', function ($q) {
            $q->where('code', 'A');
        })->where('apartment_number', '201')->first();

        $userB = User::where('email', 'resident.b@example.com')->first();

        if ($userB && $apartmentB) {
            DB::table('residents')->insert([
                'user_id'          => $userB->id,
                'apartment_id'     => $apartmentB->id,
                'relationship'     => 'owner',
                'temporary_status' => 'permanent',
                'start_date'       => $now->toDateString(),
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }

        $this->command->info('Đã tạo thành công hóa đơn thử nghiệm 100,000đ cho Cư Dân A và liên kết phòng!');
    }
}
