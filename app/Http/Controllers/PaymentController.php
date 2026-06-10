<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\ZaloPayService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payWithZaloPay(
        Invoice $bill,
        ZaloPayService $zalopay
    )
    {
        if ($bill->status === 'paid') {
            return back()->with(
                'error',
                'Hóa đơn đã thanh toán'
            );
        }

        $result = $zalopay->createOrder($bill);

        if (!isset($result['order_url'])) {

            return back()->with(
                'error',
                'Không tạo được giao dịch'
            );
        }

        Payment::create([
            'bill_id' => $bill->id,
            'amount' => $bill->total_amount,
            'payment_method' => 'zalopay',
            'status' => 'pending',
        ]);

        return redirect($result['order_url']);
    }
}