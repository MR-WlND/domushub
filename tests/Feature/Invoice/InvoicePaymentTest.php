<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class InvoicePaymentTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    private function makeUnpaidInvoice(): array
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

        $invoice = Invoice::create([
            'apartment_id'  => $apartment->id,
            'title'         => 'Hóa đơn tháng 07/2026',
            'billing_month' => 7,
            'billing_year'  => 2026,
            'total_amount'  => 2000000,
            'paid_amount'   => 0,
            'status'        => 'unpaid',
            'due_date'      => now()->addDays(15),
        ]);

        return [$invoice, $resident, $apartment];
    }

    /**
     * Resident xem trang hóa đơn chưa thanh toán.
     */
    public function test_resident_can_view_unpaid_invoices(): void
    {
        [$invoice, $resident] = $this->makeUnpaidInvoice();
        $this->actingAs($resident);

        $response = $this->get(route('resident.invoices.index'));
        $response->assertStatus(200);
    }

    /**
     * Resident xem lịch sử thanh toán.
     */
    public function test_resident_can_view_payment_history(): void
    {
        [$invoice, $resident] = $this->makeUnpaidInvoice();
        $this->actingAs($resident);

        $response = $this->get(route('resident.invoices.history'));
        $response->assertStatus(200);
    }

    /**
     * Resident thanh toán hóa đơn bằng VNPay được redirect ra ngoài.
     */
    public function test_resident_can_initiate_vnpay_payment(): void
    {
        [$invoice, $resident] = $this->makeUnpaidInvoice();
        $this->actingAs($resident);

        $response = $this->post(route('resident.invoices.pay'), [
            'invoice_id'     => $invoice->id,
            'payment_method' => 'vnpay',
        ]);

        $response->assertRedirect();
    }

    /**
     * Admin in hóa đơn (PDF view).
     */
    public function test_admin_can_print_invoice(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        [$invoice] = $this->makeUnpaidInvoice();

        $this->actingAs($admin);

        $response = $this->get(route('admin.invoices.print', $invoice));
        $response->assertStatus(200);
    }
}
