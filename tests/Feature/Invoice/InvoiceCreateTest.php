<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class InvoiceCreateTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem form tạo hóa đơn.
     */
    public function test_admin_can_view_create_invoice_form(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.invoices.create'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo hóa đơn thành công.
     */
    public function test_admin_can_create_invoice(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654322']);
        $apartment = $this->makeApartment();

        $this->actingAs($admin);

        $response = $this->post(route('admin.invoices.store'), [
            'apartment_id'  => $apartment->id,
            'title'         => 'Hóa đơn tháng 07/2026',
            'billing_month' => '2026-07',
            'due_date'      => now()->addDays(30)->format('Y-m-d'),
            'custom_fees'   => [
                [
                    'name'   => 'Phí vệ sinh',
                    'type'   => 'other',
                    'amount' => 50000,
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bills', [
            'apartment_id'  => $apartment->id,
            'billing_month' => 7,
            'billing_year'  => 2026,
        ]);
    }

    /**
     * Validate: không thể tạo hóa đơn thiếu apartment_id.
     */
    public function test_cannot_create_invoice_without_apartment(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654323']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.invoices.store'), [
            'title'         => 'Hóa đơn tháng 07/2026',
            'billing_month' => '2026-07',
            'due_date'      => now()->addDays(30)->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors(['apartment_id']);
    }

    /**
     * Admin đánh dấu hóa đơn đã thanh toán.
     */
    public function test_admin_can_mark_invoice_as_paid(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654324']);
        $apartment = $this->makeApartment();

        $invoice = Invoice::create([
            'apartment_id'  => $apartment->id,
            'title'         => 'Hóa đơn tháng 06/2026',
            'billing_month' => 6,
            'billing_year'  => 2026,
            'total_amount'  => 1500000,
            'paid_amount'   => 0,
            'status'        => 'unpaid',
            'due_date'      => now()->addDays(15),
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.invoices.mark-paid', $invoice), [
            'payment_method' => 'cash',
            'amount'         => 1500000,
        ]);

        $response->assertRedirect();
        $this->assertEquals('paid', $invoice->fresh()->status);
    }
}
