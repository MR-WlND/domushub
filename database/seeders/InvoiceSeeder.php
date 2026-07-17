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
        if (Apartment::count() === 0) {
            $this->call(BlockFloorApartmentSeeder::class);
        }
        if (User::where('role', 'resident')->count() === 0) {
            $this->call(ResidentSeeder::class);
        }
        if (DB::table('residents')->count() === 0) {
            $this->call(ResidentAccountSeeder::class);
        }
        if (ServicePrice::count() === 0) {
            $this->call(ServicePriceSeeder::class);
        }

        $now = Carbon::now();

        // 3. Liên kết thủ công Cư Dân A với căn hộ A101 (Tòa A, tầng 1, phòng 101)
        $apartmentA = Apartment::whereHas('floor.block', function ($q) {
            $q->where('code', 'A');
        })->where('apartment_number', '101')->first();

        $userA = User::where('email', 'resident.a@example.com')->first();

        if ($userA && $apartmentA) {
            DB::table('residents')->updateOrInsert(
                ['user_id' => $userA->id, 'apartment_id' => $apartmentA->id],
                [
                    'relationship'     => 'owner',
                    'temporary_status' => 'permanent',
                    'start_date'       => $now->toDateString(),
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]
            );
        }

        // 4. Liên kết thủ công Cư Dân B với căn hộ A201 (Tòa A, tầng 2, phòng 201)
        $apartmentB = Apartment::whereHas('floor.block', function ($q) {
            $q->where('code', 'A');
        })->where('apartment_number', '201')->first();

        $userB = User::where('email', 'resident.b@example.com')->first();

        if ($userB && $apartmentB) {
            DB::table('residents')->updateOrInsert(
                ['user_id' => $userB->id, 'apartment_id' => $apartmentB->id],
                [
                    'relationship'     => 'owner',
                    'temporary_status' => 'permanent',
                    'start_date'       => $now->toDateString(),
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]
            );
        }

        // 5. Lấy các loại giá dịch vụ
        $electricityPrice = ServicePrice::where('type', 'electricity')->first();
        $waterPrice = ServicePrice::where('type', 'water')->first();
        $managementPrice = ServicePrice::where('type', 'management_fee')->first();
        $parkingPrice = ServicePrice::where('type', 'parking')->first();
        $internetPrice = ServicePrice::where('type', 'internet')->first();
        $otherPrice = ServicePrice::where('type', 'service')->first();

        $adminUser = User::whereIn('role', ['admin', 'staff'])->first();
        $adminId = $adminUser ? $adminUser->id : null;

        // 6. Lấy danh sách 5 căn hộ khác nhau đã có cư dân
        $aptList = Apartment::whereHas('residents')
            ->with(['residents.user', 'floor.block'])
            ->orderBy('id')
            ->get();

        // Dự phòng nếu không đủ 5 căn hộ có cư dân thì lấy căn hộ bất kỳ
        if ($aptList->count() < 5) {
            $aptList = Apartment::with(['residents.user', 'floor.block'])->take(5)->get();
        }

        if ($aptList->count() < 5) {
            $this->command->error('Không đủ 5 căn hộ trong CSDL để tạo hóa đơn mẫu!');
            return;
        }

        $apt1 = $apartmentA ?? $aptList[0];
        $apt2 = $aptList[1];
        $apt3 = $aptList[2];
        $apt4 = $aptList[3];
        $apt5 = $aptList[4];

        // ==========================================
        // HÓA ĐƠN MẪU 1: Đã thanh toán (Tháng 04/2026 - Căn 1)
        // ==========================================
        $payer1 = $apt1->residents->first()->user->name ?? 'Nguyễn Văn An';
        $bill1 = Invoice::create([
            'apartment_id'  => $apt1->id,
            'title'         => 'Hóa đơn phí dịch vụ Tháng 04/2026',
            'billing_month' => 4,
            'billing_year'  => 2026,
            'due_date'      => Carbon::create(2026, 4, 25),
            'total_amount'  => 380000,
            'paid_amount'   => 380000,
            'status'        => 'paid',
        ]);

        if ($managementPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill1->id,
                'service_price_id' => $managementPrice->id,
                'quantity'         => 1,
                'amount'           => 200000,
                'status'           => 'paid',
            ]);
        }

        if ($parkingPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill1->id,
                'service_price_id' => $parkingPrice->id,
                'quantity'         => 1,
                'amount'           => 180000,
                'status'           => 'paid',
            ]);
        }

        Payment::create([
            'bill_id'          => $bill1->id,
            'amount'           => 380000,
            'payment_method'   => 'bank_transfer',
            'transaction_code' => 'TXN-APR-APT1',
            'receipt_code'     => 'REC-20260420-APT1',
            'status'           => 'success',
            'paid_at'          => Carbon::create(2026, 4, 20),
            'note'             => 'Thanh toán phí dịch vụ tháng 04/2026',
            'payer_name'       => $payer1,
            'recorded_by'      => $adminId,
        ]);

        // --- TẠO HÓA ĐƠN CHƯA THANH TOÁN CHO CƯ DÂN A (test thanh toán) ---
        $unpaidBill1 = Invoice::create([
            'apartment_id'  => $apt1->id,
            'title'         => 'Phí quản lý thử nghiệm Tháng 06/2026',
            'billing_month' => 6,
            'billing_year'  => 2026,
            'due_date'      => Carbon::create(2026, 6, 25),
            'total_amount'  => 100000,
            'paid_amount'   => 0,
            'status'        => 'unpaid',
        ]);

        if ($otherPrice) {
            InvoiceDetail::create([
                'bill_id'          => $unpaidBill1->id,
                'service_price_id' => $otherPrice->id,
                'quantity'         => 1,
                'amount'           => 100000,
                'status'           => 'unpaid',
            ]);
        }

        // --- TẠO HÓA ĐƠN QUÁ HẠN CHO CƯ DÂN A (test cảnh báo quá hạn) ---
        $overdueBill1 = Invoice::create([
            'apartment_id'  => $apt1->id,
            'title'         => 'Tiền điện nước thử nghiệm Tháng 05/2026',
            'billing_month' => 5,
            'billing_year'  => 2026,
            'due_date'      => Carbon::create(2026, 5, 15),
            'total_amount'  => 100000,
            'paid_amount'   => 0,
            'status'        => 'overdue',
        ]);

        if ($otherPrice) {
            InvoiceDetail::create([
                'bill_id'          => $overdueBill1->id,
                'service_price_id' => $otherPrice->id,
                'quantity'         => 1,
                'amount'           => 100000,
                'status'           => 'unpaid',
            ]);
        }

        // ==========================================
        // HÓA ĐƠN MẪU 2: Thanh toán một phần (Tháng 05/2026 - Căn 2)
        // ==========================================
        $payer2 = $apt2->residents->first()->user->name ?? 'Trần Thị Bích';
        $bill2 = Invoice::create([
            'apartment_id'  => $apt2->id,
            'title'         => 'Hóa đơn phí dịch vụ Tháng 05/2026',
            'billing_month' => 5,
            'billing_year'  => 2026,
            'due_date'      => Carbon::create(2026, 5, 25),
            'total_amount'  => 510000,
            'paid_amount'   => 200000,
            'status'        => 'partial_paid',
        ]);

        if ($managementPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill2->id,
                'service_price_id' => $managementPrice->id,
                'quantity'         => 1,
                'amount'           => 200000,
                'status'           => 'paid',
            ]);
        }

        if ($electricityPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill2->id,
                'service_price_id' => $electricityPrice->id,
                'quantity'         => 60,
                'amount'           => 180000,
                'status'           => 'unpaid',
            ]);
        }

        if ($internetPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill2->id,
                'service_price_id' => $internetPrice->id,
                'quantity'         => 1,
                'amount'           => 130000,
                'status'           => 'unpaid',
            ]);
        }

        Payment::create([
            'bill_id'          => $bill2->id,
            'amount'           => 200000,
            'payment_method'   => 'cash',
            'transaction_code' => 'TXN-MAY-APT2-PART',
            'receipt_code'     => 'REC-20260522-APT2',
            'status'           => 'success',
            'paid_at'          => Carbon::create(2026, 5, 22),
            'note'             => 'Đóng trước phí quản lý tháng 05',
            'payer_name'       => $payer2,
            'recorded_by'      => $adminId,
        ]);

        // ==========================================
        // HÓA ĐƠN MẪU 3: Chưa thanh toán (Tháng 06/2026 - Căn 3)
        // ==========================================
        $bill3 = Invoice::create([
            'apartment_id'  => $apt3->id,
            'title'         => 'Hóa đơn phí dịch vụ Tháng 06/2026',
            'billing_month' => 6,
            'billing_year'  => 2026,
            'due_date'      => Carbon::create(2026, 6, 25),
            'total_amount'  => 360000,
            'paid_amount'   => 0,
            'status'        => 'unpaid',
        ]);

        if ($parkingPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill3->id,
                'service_price_id' => $parkingPrice->id,
                'quantity'         => 1,
                'amount'           => 180000,
                'status'           => 'unpaid',
            ]);
        }

        if ($internetPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill3->id,
                'service_price_id' => $internetPrice->id,
                'quantity'         => 1,
                'amount'           => 130000,
                'status'           => 'unpaid',
            ]);
        }

        if ($waterPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill3->id,
                'service_price_id' => $waterPrice->id,
                'quantity'         => 3,
                'amount'           => 45000,
                'status'           => 'unpaid',
            ]);
        }

        if ($otherPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill3->id,
                'service_price_id' => $otherPrice->id,
                'quantity'         => 1,
                'amount'           => 5000,
                'status'           => 'unpaid',
            ]);
        }

        // ==========================================
        // HÓA ĐƠN MẪU 4: Quá hạn chưa thanh toán (Tháng 03/2026 - Căn 4)
        // ==========================================
        $bill4 = Invoice::create([
            'apartment_id'  => $apt4->id,
            'title'         => 'Hóa đơn phí dịch vụ Tháng 03/2026',
            'billing_month' => 3,
            'billing_year'  => 2026,
            'due_date'      => Carbon::create(2026, 3, 25),
            'total_amount'  => 330000,
            'paid_amount'   => 0,
            'status'        => 'overdue',
        ]);

        if ($managementPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill4->id,
                'service_price_id' => $managementPrice->id,
                'quantity'         => 1,
                'amount'           => 200000,
                'status'           => 'unpaid',
            ]);
        }

        if ($internetPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill4->id,
                'service_price_id' => $internetPrice->id,
                'quantity'         => 1,
                'amount'           => 130000,
                'status'           => 'unpaid',
            ]);
        }

        // ==========================================
        // HÓA ĐƠN MẪU 5: Đã thanh toán (Tháng 05/2026 - Căn 5)
        // ==========================================
        $payer5 = $apt5->residents->first()->user->name ?? 'Hoàng Minh Đức';
        $bill5 = Invoice::create([
            'apartment_id'  => $apt5->id,
            'title'         => 'Hóa đơn phí dịch vụ Tháng 05/2026',
            'billing_month' => 5,
            'billing_year'  => 2026,
            'due_date'      => Carbon::create(2026, 5, 25),
            'total_amount'  => 380000,
            'paid_amount'   => 380000,
            'status'        => 'paid',
        ]);

        if ($managementPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill5->id,
                'service_price_id' => $managementPrice->id,
                'quantity'         => 1,
                'amount'           => 200000,
                'status'           => 'paid',
            ]);
        }

        if ($parkingPrice) {
            InvoiceDetail::create([
                'bill_id'          => $bill5->id,
                'service_price_id' => $parkingPrice->id,
                'quantity'         => 1,
                'amount'           => 180000,
                'status'           => 'paid',
            ]);
        }

        Payment::create([
            'bill_id'          => $bill5->id,
            'amount'           => 380000,
            'payment_method'   => 'vnpay',
            'transaction_code' => 'VNPAY-MAY-APT5',
            'receipt_code'     => 'REC-20260524-APT5',
            'status'           => 'success',
            'paid_at'          => Carbon::create(2026, 5, 24),
            'note'             => 'Thanh toán hóa đơn tháng 05/2026 qua VNPay',
            'payer_name'       => $payer5,
        ]);

        $this->command->info('Đã tạo thành công 5 hóa đơn mẫu trên 5 căn hộ khác nhau!');
    }
}
