<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class InvoiceExportTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem trang xuất dữ liệu hóa đơn.
     */
    public function test_admin_can_view_export_invoice_data(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->actingAs($admin);

        $apartment = $this->makeApartment();
        Invoice::create([
            'apartment_id'  => $apartment->id,
            'title'         => 'Hóa đơn tháng 05/2026',
            'billing_month' => 5,
            'billing_year'  => 2026,
            'total_amount'  => 1800000,
            'paid_amount'   => 1800000,
            'status'        => 'paid',
            'due_date'      => now()->subDays(5),
        ]);

        $response = $this->get(route('admin.invoices.index', ['status' => 'paid']));
        $response->assertStatus(200);
    }

    /**
     * Admin xem chi tiết hóa đơn.
     */
    public function test_admin_can_view_invoice_detail(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654322']);
        $apartment = $this->makeApartment();

        $invoice = Invoice::create([
            'apartment_id'  => $apartment->id,
            'title'         => 'Hóa đơn tháng 06/2026',
            'billing_month' => 6,
            'billing_year'  => 2026,
            'total_amount'  => 2500000,
            'paid_amount'   => 0,
            'status'        => 'unpaid',
            'due_date'      => now()->addDays(10),
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.invoices.show', $invoice));
        $response->assertStatus(200);
    }

    /**
     * Admin cập nhật hóa đơn.
     */
    public function test_admin_can_update_invoice(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654323']);
        $apartment = $this->makeApartment();

        $invoice = Invoice::create([
            'apartment_id'  => $apartment->id,
            'title'         => 'Hóa đơn tháng 07/2026',
            'billing_month' => 7,
            'billing_year'  => 2026,
            'total_amount'  => 1500000,
            'paid_amount'   => 0,
            'status'        => 'unpaid',
            'due_date'      => now()->addDays(20),
        ]);

        $this->actingAs($admin);

        $response = $this->put(route('admin.invoices.update', $invoice), [
            'total_amount' => 1600000,
            'due_date'     => now()->addDays(25)->format('Y-m-d'),
            'status'       => 'unpaid',
        ]);

        $response->assertRedirect();
    }

    /**
     * Admin hủy hóa đơn.
     */
    public function test_admin_can_cancel_invoice(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654324']);
        $apartment = $this->makeApartment();

        $invoice = Invoice::create([
            'apartment_id'  => $apartment->id,
            'title'         => 'Hóa đơn tháng 08/2026',
            'billing_month' => 8,
            'billing_year'  => 2026,
            'total_amount'  => 1200000,
            'paid_amount'   => 0,
            'status'        => 'unpaid',
            'due_date'      => now()->addDays(30),
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.invoices.cancel', $invoice));
        $response->assertRedirect();
    }
}
