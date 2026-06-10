<?php

namespace App\Services\PaymentGateway;

use Illuminate\Support\Facades\Log;

class MBBankPaymentGateway implements PaymentGatewayInterface
{
    protected string $accountNumber;
    protected string $accountName;

    public function __construct()
    {
        $this->accountNumber = config('payment.mbbank.account_number');
        $this->accountName   = config('payment.mbbank.account_name');
    }

    public function generateQR(array $data): array
    {
        try {
            $amount      = (int)($data['amount'] ?? 0);
            $description = $data['description'] ?? 'Thanh toan hoa don';
            $orderId     = $data['transaction_code'] ?? 'TX-' . time();

            // Dùng Sepay QR — nội dung = transaction_code để Sepay match
            $qrUrl = sprintf(
                'https://qr.sepay.vn/img?bank=MB&acc=%s&template=compact&amount=%d&des=%s',
                urlencode($this->accountNumber),
                $amount,
                urlencode($orderId)
            );

            return [
                'success'          => true,
                'gateway'          => 'mbbank',
                'transaction_code' => $orderId,
                'qr_url'           => $qrUrl,
                'amount'           => $amount,
                'account_number'   => $this->accountNumber,
                'account_name'     => $this->accountName,
            ];
        } catch (\Exception $e) {
            Log::error('MBBank QR Error', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Lỗi khi tạo QR MB Bank: ' . $e->getMessage(),
            ];
        }
    }

    public function getPaymentStatus(string $transactionCode): array
    {
        // MB Bank VietQR không có API kiểm tra tự động — trả pending để admin xác nhận thủ công
        return [
            'success' => true,
            'status'  => 'pending',
            'message' => 'Vui lòng xác nhận thanh toán với quản trị viên.',
        ];
    }

    public function verifyCallback(array $data): bool
    {
        return true;
    }

    public function handleCallback(array $data): array
    {
        return ['success' => false, 'message' => 'MB Bank không hỗ trợ callback tự động.'];
    }

    public function refund(string $transactionCode, float $amount): array
    {
        return ['success' => false, 'message' => 'Vui lòng hoàn tiền thủ công qua MB Bank.'];
    }
}
