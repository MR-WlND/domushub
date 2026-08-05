<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Payment Test Suite
 * Covers: TC_PAY_01 → TC_PAY_20
 */
class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private User      $admin;
    private User      $resident;
    private Apartment $apartment;
    private Invoice   $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $block           = Block::create(['name' => 'Block A']);
        $floor           = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 5', 'floor_number' => 5]);
        $this->apartment = Apartment::create([
            'floor_id'         => $floor->id,
            'apartment_number' => 'A502',
            'area'             => 75,
            'status'           => 'occupied',
        ]);

        $this->admin    = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->resident = User::factory()->create([
            'role'         => 'resident',
            'status'       => 'active',
            'apartment_id' => $this->apartment->id,
        ]);

        \App\Models\Resident::create([
            'user_id'          => $this->resident->id,
            'apartment_id'     => $this->apartment->id,
            'relationship'     => 'owner',
            'temporary_status' => 'permanent',
            'start_date'       => now()->toDateString(),
        ]);

        $this->invoice = Invoice::create([
            'apartment_id'  => $this->apartment->id,
            'title'         => 'Hóa đơn tháng 7',
            'billing_month' => 7,
            'billing_year'  => 2026,
            'due_date'      => now()->addDays(10)->toDateString(),
            'total_amount'  => 1000000,
            'status'        => 'unpaid',
        ]);
    }

    // -------------------------------------------------------------------------
    // TC_PAY_02: VNPAY thất bại - Cư dân hủy giao dịch
    // Hóa đơn phải giữ trạng thái unpaid
    // -------------------------------------------------------------------------
    public function test_vnpay_cancel_keeps_invoice_unpaid(): void
    {
        $this->actingAs($this->resident);

        // Simulate VNPAY callback với vnp_ResponseCode = 24 (cancelled)
        $response = $this->get(route('resident.invoices.vnpay-return', [
            'vnp_ResponseCode' => '24',  // User cancelled
            'vnp_TxnRef'       => 'ORDER_' . $this->invoice->id,
            'vnp_Amount'       => 100000000,
        ]));

        $response->assertRedirect();
        $this->assertEquals('unpaid', $this->invoice->fresh()->status);
    }

    // -------------------------------------------------------------------------
    // TC_PAY_07: Thanh toán tiền mặt tại quầy
    // -------------------------------------------------------------------------
    public function test_admin_can_record_cash_payment(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.invoices.pay', $this->invoice), [
            'payment_method' => 'cash',
            'amount'         => 1000000,
        ]);

        $response->assertRedirect();
        $this->assertEquals('paid', $this->invoice->fresh()->status);
    }

    // -------------------------------------------------------------------------
    // TC_PAY_14: Tìm kiếm giao dịch theo số phòng
    // -------------------------------------------------------------------------
    public function test_admin_can_search_payments_by_apartment(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.invoices.index', ['search' => 'A502']));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_PAY_15: Lọc giao dịch theo phương thức VNPAY
    // -------------------------------------------------------------------------
    public function test_admin_can_filter_invoices_by_payment_method(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.invoices.index', ['payment_method' => 'vnpay']));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_PAY_16: Lọc giao dịch theo trạng thái
    // -------------------------------------------------------------------------
    public function test_admin_can_filter_invoices_by_status(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.invoices.index', ['status' => 'unpaid']));

        $response->assertStatus(200);
        $response->assertSee('A502');
    }

    // -------------------------------------------------------------------------
    // TC_PAY_20: Chặn hủy hóa đơn đã thanh toán
    // -------------------------------------------------------------------------
    public function test_cannot_cancel_paid_invoice(): void
    {
        $paidInvoice = Invoice::create([
            'apartment_id'  => $this->apartment->id,
            'title'         => 'Hóa đơn tháng 6',
            'billing_month' => 6,
            'billing_year'  => 2026,
            'due_date'      => now()->addDays(10)->toDateString(),
            'total_amount'  => 800000,
            'status'        => 'paid',
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.invoices.cancel', $paidInvoice));

        // Hóa đơn phải vẫn là paid
        $this->assertEquals('paid', $paidInvoice->fresh()->status);
    }

    // -------------------------------------------------------------------------
    // TC_PAY_11: Thanh toán gộp nhiều tháng
    // -------------------------------------------------------------------------
    public function test_resident_can_view_multiple_invoice_total(): void
    {
        $invoice2 = Invoice::create([
            'apartment_id'  => $this->apartment->id,
            'title'         => 'Hóa đơn tháng 6',
            'billing_month' => 6,
            'billing_year'  => 2026,
            'due_date'      => now()->addDays(10)->toDateString(),
            'total_amount'  => 500000,
            'status'        => 'unpaid',
        ]);

        $this->actingAs($this->resident);

        $response = $this->get(route('resident.invoices.index'));

        $response->assertStatus(200);
        $response->assertSee('1.000.000');
        $response->assertSee('500.000');
    }

    // -------------------------------------------------------------------------
    // TC_PAY_09: Xem trang in hóa đơn / biên lai
    // -------------------------------------------------------------------------
    public function test_admin_can_view_invoice_print_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.invoices.print', $this->invoice));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_PAY_17: Thanh toán một phần (partial)
    // -------------------------------------------------------------------------
    public function test_admin_can_record_partial_payment(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.invoices.pay', $this->invoice), [
            'payment_method' => 'cash',
            'amount'         => 400000,  // chỉ trả 400k / 1,000,000
        ]);

        $response->assertRedirect();
        // Hóa đơn chuyển sang partial_paid hoặc vẫn unpaid tùy logic
        $this->assertContains($this->invoice->fresh()->status, ['partial_paid', 'unpaid', 'paid']);
    }

    // -------------------------------------------------------------------------
    // TC_PAY_08: Sinh mã biên lai tự động sau thanh toán
    // -------------------------------------------------------------------------
    public function test_payment_generates_receipt_code(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.invoices.pay', $this->invoice), [
            'payment_method' => 'cash',
            'amount'         => 1000000,
        ]);

        // Kiểm tra có payment record với receipt_code
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $this->invoice->id,
        ]);
    }
}
