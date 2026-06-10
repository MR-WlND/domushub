<?php

namespace App\Services\PaymentGateway;

interface PaymentGatewayInterface
{
    /**
     * Tạo QR code cho thanh toán
     */
    public function generateQR(array $data): array;

    /**
     * Lấy thông tin trạng thái thanh toán
     */
    public function getPaymentStatus(string $transactionCode): array;

    /**
     * Xác minh callback từ gateway
     */
    public function verifyCallback(array $data): bool;

    /**
     * Xử lý callback từ gateway
     */
    public function handleCallback(array $data): array;

    /**
     * Hoàn tiền
     */
    public function refund(string $transactionCode, float $amount): array;
}
