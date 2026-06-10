<?php

namespace App\Services\PaymentGateway;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected array $gateways = [];

    public function __construct()
    {
        $this->gateways['mbbank'] = new MBBankPaymentGateway();
        $this->gateways['momo']   = new MomoPaymentGateway();
    }

    public function gateway(string $name): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$name])) {
            throw new \Exception("Gateway '{$name}' không tồn tại.");
        }
        return $this->gateways[$name];
    }

    public function generatePaymentQR(Invoice $invoice, string $gateway): array
    {
        try {
            if ($invoice->status === 'paid') {
                return ['success' => false, 'message' => 'Hóa đơn này đã được thanh toán rồi.'];
            }

            $transactionCode = 'TX-' . $invoice->id . '-' . time();

            $data = [
                'bill_id'          => $invoice->id,
                'amount'           => $invoice->total_amount,
                'transaction_code' => $transactionCode,
                'description'      => $transactionCode, // Sepay sẽ match cỗi này trong nội dung CK
            ];

            $result = $this->gateway($gateway)->generateQR($data);

            if ($result['success'] ?? false) {
                Payment::create([
                    'bill_id'          => $invoice->id,
                    'amount'           => $invoice->total_amount,
                    'payment_method'   => $gateway,
                    'transaction_code' => $transactionCode,
                    'status'           => 'pending',
                ]);

                Log::info("Payment QR generated for invoice {$invoice->id} via {$gateway}", $result);

                return array_merge($result, ['bill_id' => $invoice->id]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Payment QR Generation Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function handleCallback(string $gateway, array $data): array
    {
        try {
            $result = $this->gateway($gateway)->handleCallback($data);

            if ($result['success'] ?? false) {
                $payment = Payment::where('transaction_code', $result['transaction_code'] ?? null)->first();
                if ($payment) {
                    $payment->update(['status' => 'success', 'paid_at' => now()]);
                    optional($payment->invoice)->update(['status' => 'paid']);
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Payment callback error for {$gateway}", ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function checkPaymentStatus(string $gateway, string $transactionCode): array
    {
        try {
            return $this->gateway($gateway)->getPaymentStatus($transactionCode);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function refundPayment(string $gateway, string $transactionCode, float $amount): array
    {
        try {
            $result = $this->gateway($gateway)->refund($transactionCode, $amount);

            if ($result['success'] ?? false) {
                $payment = Payment::where('transaction_code', $transactionCode)->first();
                if ($payment) {
                    $payment->update(['status' => 'refunded']);
                    optional($payment->invoice)->update(['status' => 'unpaid']);
                }
            }

            return $result;
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getAvailableGateways(): array
    {
        return [
            'mbbank' => [
                'name'        => 'MB Bank',
                'description' => 'Chuyển khoản qua QR MB Bank (VietQR)',
                'icon'        => 'mbbank',
            ],
            'momo' => [
                'name'        => 'Momo',
                'description' => 'Chuyển khoản qua QR Momo (VietQR)',
                'icon'        => 'momo',
            ],
        ];
    }
}
