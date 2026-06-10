<?php

namespace App\Services\PaymentGateway;

use Illuminate\Support\Facades\Log;

class MomoPaymentGateway implements PaymentGatewayInterface
{
    protected string $phone;
    protected string $name;

    public function __construct()
    {
        $this->phone = config('payment.momo.phone');
        $this->name  = config('payment.momo.name');
    }

    public function generateQR(array $data): array
    {
        try {
            $amount      = (int)($data['amount'] ?? 0);
            $description = $data['description'] ?? 'Thanh toan hoa don';
            $orderId     = $data['transaction_code'] ?? 'TX-' . time();

            // Dùng Sepay QR — tiền về tài khoản MB Bank, Sepay tự đọc sao kê
            $accountNumber = config('payment.mbbank.account_number');
            $accountName   = config('payment.mbbank.account_name');

            $qrUrl = sprintf(
                'https://qr.sepay.vn/img?bank=MB&acc=%s&template=compact&amount=%d&des=%s',
                urlencode($accountNumber),
                $amount,
                urlencode($orderId)
            );

            return [
                'success'          => true,
                'gateway'          => 'momo',
                'transaction_code' => $orderId,
                'qr_url'           => $qrUrl,
                'amount'           => $amount,
                'account_name'     => $accountName,
            ];
        } catch (\Exception $e) {
            Log::error('Momo QR Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Lỗi khi tạo QR Momo: ' . $e->getMessage()];
        }
    }

    public function getPaymentStatus(string $transactionCode): array
    {
        return ['success' => true, 'status' => 'pending'];
    }

    public function verifyCallback(array $data): bool
    {
        return true;
    }

    public function handleCallback(array $data): array
    {
        return ['success' => false, 'message' => 'Momo VietQR không hỗ trợ callback tự động.'];
    }

    public function refund(string $transactionCode, float $amount): array
    {
        return ['success' => false, 'message' => 'Vui lòng hoàn tiền thủ công qua Momo.'];
    }
}
