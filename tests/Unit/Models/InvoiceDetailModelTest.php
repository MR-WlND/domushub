<?php

namespace Tests\Unit\Models;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\ServicePrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class InvoiceDetailModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * InvoiceDetail thuộc về Invoice.
     */
    public function test_invoice_detail_belongs_to_invoice(): void
    {
        $apartment    = $this->makeApartment();
        $servicePrice = ServicePrice::create([
            'type'       => 'other',
            'name'       => 'Phí dịch vụ',
            'unit_price' => 500000,
            'unit'       => 'tháng',
            'status'     => 'active',
        ]);

        $invoice = Invoice::create([
            'apartment_id'  => $apartment->id,
            'title'         => 'Hóa đơn chi tiết',
            'billing_month' => 7,
            'billing_year'  => 2026,
            'total_amount'  => 500000,
            'due_date'      => now()->addDays(10),
        ]);

        $detail = InvoiceDetail::create([
            'bill_id'          => $invoice->id,
            'service_price_id' => $servicePrice->id,
            'description'      => 'Phí vệ sinh',
            'amount'           => 500000,
        ]);

        $this->assertEquals($invoice->id, $detail->bill_id);
        $this->assertEquals($servicePrice->id, $detail->service_price_id);
    }
}
