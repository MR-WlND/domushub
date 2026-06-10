<?php

namespace App\Services\PaymentGateway;

class PaymentHelper
{
    /**
     * Định dạng tiền VND
     */
    public static function formatVND(float $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' đ';
    }

    /**
     * Tạo transaction code duy nhất
     */
    public static function generateTransactionCode(int $billId): string
    {
        return 'TX-' . $billId . '-' . time();
    }

    /**
     * Kiểm tra xem callback có hợp lệ không
     */
    public static function isCallbackValid(?array $data): bool
    {
        if (!$data) {
            return false;
        }

        // Kiểm tra các field bắt buộc
        $requiredFields = ['transaction_code', 'amount', 'status'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Kiểm tra xem thanh toán đã quá hạn chưa
     */
    public static function isPaymentExpired(\DateTime $createdAt, int $timeout = 3600): bool
    {
        $now = now();
        $diff = $now->diffInSeconds($createdAt);

        return $diff > $timeout;
    }

    /**
     * Chuyển đổi mã lỗi Momo thành thông báo
     */
    public static function getMomoErrorMessage(string $resultCode): string
    {
        $errors = [
            '0' => 'Thành công',
            '1' => 'Lỗi không xác định',
            '2' => 'Số điện thoại không tồn tại',
            '3' => 'Người dùng không tồn tại',
            '4' => 'Số dư không đủ',
            '5' => 'Tài khoản bị khóa',
            '6' => 'Tài khoản không được phép giao dịch',
            '7' => 'Giao dịch đã được cấp phép trước đó',
            '8' => 'Giao dịch bị từ chối',
            '9' => 'Giao dịch timed out',
            '10' => 'Format message không hợp lệ',
            '11' => 'Không thể xử lý',
            '99' => 'Chưa biết',
        ];

        return $errors[$resultCode] ?? 'Lỗi không xác định';
    }

    /**
     * Chuyển đổi mã lỗi VNPay thành thông báo
     */
    public static function getVNPayErrorMessage(string $responseCode): string
    {
        $errors = [
            '00' => 'Giao dịch thành công',
            '01' => 'Giao dịch bị từ chối do ngân hàng',
            '02' => 'Giao dịch bị từ chối do ngân hàng',
            '04' => 'Giao dịch bị từ chối do ngân hàng',
            '05' => 'Giao dịch bị từ chối do ngân hàng',
            '06' => 'Giao dịch bị từ chối do ngân hàng',
            '07' => 'Giao dịch bị từ chối do ngân hàng',
            '09' => 'Giao dịch bị từ chối do ngân hàng',
            '10' => 'Giao dịch bị từ chối do ngân hàng',
            '11' => 'Giao dịch bị từ chối do lỗi trong quá trình xử lý',
            '12' => 'Giao dịch bị từ chối do quá thời gian chờ',
            '13' => 'Giao dịch bị từ chối do lỗi trong quá trình xử lý',
            '51' => 'Tài khoản của bạn không đủ số dư để thực hiện giao dịch',
            '65' => 'Tài khoản của bạn đã vượt quá hạn mức giao dịch',
            '75' => 'Ngân hàng phát hành thẻ quá nhiều lần nhập sai mật khẩu',
            '79' => 'KH nhập sai mật khẩu quá nhiều lần',
            '99' => 'Các lỗi khác',
        ];

        return $errors[$responseCode] ?? 'Lỗi không xác định';
    }

    /**
     * Xác thực email của người dùng
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Xác thực số điện thoại (format VN)
     */
    public static function isValidPhoneNumber(string $phone): bool
    {
        // Loại bỏ các khoảng trắng và ký tự đặc biệt
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Kiểm tra độ dài
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }

    /**
     * Tạo ID tham chiếu duy nhất cho callback
     */
    public static function generateCallbackReference(): string
    {
        return uniqid('CB-', true);
    }

    /**
     * Kiểm tra xem gateway có khả dụng không
     */
    public static function isGatewayAvailable(string $gateway): bool
    {
        $available = ['momo', 'vnpay'];

        return in_array($gateway, $available);
    }

    /**
     * Lấy thông tin gateway
     */
    public static function getGatewayInfo(string $gateway): ?array
    {
        $gateways = [
            'momo' => [
                'name' => 'Momo',
                'display_name' => 'Ứng dụng Momo',
                'icon' => 'momo',
                'color' => '#A40D28',
            ],
            'vnpay' => [
                'name' => 'VNPay',
                'display_name' => 'VNPay',
                'icon' => 'vnpay',
                'color' => '#1A1F71',
            ],
        ];

        return $gateways[$gateway] ?? null;
    }

    /**
     * Chuyển đổi số tiền từ VND sang đơn vị tính VNPay (100 VND)
     */
    public static function convertToVNPayAmount(float $amount): int
    {
        return (int)($amount * 100);
    }

    /**
     * Chuyển đổi số tiền từ đơn vị tính VNPay sang VND
     */
    public static function convertFromVNPayAmount(int $amount): float
    {
        return $amount / 100;
    }

    /**
     * Tạo nội dung order info
     */
    public static function generateOrderInfo(int $billId, int $month, int $year): string
    {
        return "Thanh toan hoa don thang {$month}/{$year} - {$billId}";
    }

    /**
     * Validate transaction code format
     */
    public static function isValidTransactionCode(string $code): bool
    {
        return preg_match('/^TX-\d+-\d+$/', $code) === 1;
    }
}
