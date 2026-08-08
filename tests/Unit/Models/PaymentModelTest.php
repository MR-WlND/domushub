<?php

namespace Tests\Unit\Models;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class PaymentModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Payment thuộc về Invoice (qua bill_id).
     */
    public function test_payment_belongs_to_invoice(): void
    {
        $apartment = $this->makeApartment();
        $invoice   = Invoice::create([
            'apartment_id'  => $apartment->id,
            'title'         => 'Hóa đơn thanh toán',
            'billing_month' => 7,
            'billing_year'  => 2026,
            'total_amount'  => 1000000,
            'due_date'      => now()->addDays(10),
        ]);

        $payment = Payment::create([
            'bill_id'        => $invoice->id,
            'amount'         => 1000000,
            'payment_method' => 'vnpay',
            'status'         => 'success',
        ]);

        $this->assertEquals($invoice->id, $payment->invoice->id);
    }
}
