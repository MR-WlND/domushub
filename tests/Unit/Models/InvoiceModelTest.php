<?php

namespace Tests\Unit\Models;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class InvoiceModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Tự động làm tròn số tiền hóa đơn đến hàng nghìn đồng.
     */
    public function test_invoice_rounds_total_amount_to_thousands(): void
    {
        $apartment = $this->makeApartment();

        $invoice = Invoice::create([
            'apartment_id'  => $apartment->id,
            'title'         => 'Hóa đơn làm tròn',
            'billing_month' => 7,
            'billing_year'  => 2026,
            'total_amount'  => 1500450,
            'paid_amount'   => 0,
            'status'        => 'unpaid',
            'due_date'      => now()->addDays(10),
        ]);

        $this->assertEquals(1500000, (float) $invoice->fresh()->total_amount);
    }

    /**
     * Kiểm tra quan hệ Invoice thuộc về Apartment.
     */
    public function test_invoice_belongs_to_apartment(): void
    {
        $apartment = $this->makeApartment();

        $invoice = Invoice::create([
            'apartment_id'  => $apartment->id,
            'title'         => 'Hóa đơn tháng 08',
            'billing_month' => 8,
            'billing_year'  => 2026,
            'total_amount'  => 2000000,
            'due_date'      => now()->addDays(10),
        ]);

        $this->assertEquals($apartment->id, $invoice->apartment->id);
    }
}
