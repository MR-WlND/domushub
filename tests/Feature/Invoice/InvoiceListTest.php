<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class InvoiceListTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách hóa đơn.
     */
    public function test_admin_can_view_invoice_list(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.invoices.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin xem hóa đơn của một căn hộ cụ thể.
     */
    public function test_admin_can_view_apartment_invoices(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654322']);
        $apartment = $this->makeApartment();

        $this->actingAs($admin);
        $response = $this->get(route('admin.invoices.apartment', $apartment));
        $response->assertStatus(200);
    }

    /**
     * Resident xem danh sách hóa đơn của mình.
     */
    public function test_resident_can_view_own_invoices(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

        $this->actingAs($resident);

        $response = $this->get(route('resident.invoices.index'));
        $response->assertStatus(200);
    }

    /**
     * Resident xem chi tiết hóa đơn của mình.
     */
    public function test_resident_can_view_invoice_detail(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

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

        $this->actingAs($resident);

        $response = $this->get(route('resident.invoices.show', $invoice->id));
        $response->assertStatus(200);
    }

    /**
     * Admin xem thống kê hóa đơn.
     */
    public function test_admin_can_view_invoice_stats(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654323']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.invoices.stats'));
        $response->assertStatus(200);
    }
}
