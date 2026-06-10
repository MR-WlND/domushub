<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SepayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $apikey = $request->header('Authorization');
        $expectedKey = 'Apikey ' . config('payment.sepay.webhook_secret');

        if ($apikey !== $expectedKey) {
            Log::warning('Sepay webhook: invalid API key');
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        Log::info('Sepay webhook received', $data);

        // Nội dung chuyển khoản (transferType: in = tiền vào)
        if (($data['transferType'] ?? '') !== 'in') {
            return response()->json(['success' => true]);
        }

        $content    = $data['content'] ?? '';
        $amount     = (float)($data['transferAmount'] ?? 0);

        // Tìm transaction_code trong nội dung chuyển khoản (format: TX-{id}-{timestamp})
        if (!preg_match('/(TX-\d+-\d+)/', $content, $matches)) {
            Log::info('Sepay webhook: no transaction_code found in content', ['content' => $content]);
            return response()->json(['success' => true]);
        }

        $transactionCode = $matches[1];

        $payment = Payment::where('transaction_code', $transactionCode)
            ->where('status', 'pending')
            ->first();


        if (!$payment) {
            Log::info('Sepay webhook: payment not found or already processed', ['code' => $transactionCode]);
            return response()->json(['success' => true]);
        }


        // Kiểm tra số tiền
        if ($amount < $payment->amount) {
            Log::warning('Sepay webhook: insufficient amount', [
                'expected' => $payment->amount,
                'received' => $amount,
                'transaction_code' => $transactionCode
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Insufficient amount'
            ]);
        }

        
        // Xác nhận thanh toán
        $payment->update([
            'status'  => 'success',
            'paid_at' => now(),
        ]);

        $invoice = $payment->invoice;
        if ($invoice && $invoice->status !== 'paid') {
            $invoice->update(['status' => 'paid']);
        }

        Log::info('Sepay webhook: payment confirmed', ['transaction_code' => $transactionCode, 'amount' => $amount]);

        return response()->json(['success' => true]);
    }
}
