<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'bill_id',
        'amount',
        'payment_method',
        'transaction_code',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'bill_id');
    }

    /**
     * Các scopes hữu ích
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('payment_method', $gateway);
    }

    public function scopeByMonth($query, int $month, int $year)
    {
        return $query->whereHas('invoice', function ($q) use ($month, $year) {
            $q->where('billing_month', $month)
                ->where('billing_year', $year);
        });
    }

    /**
     * Kiểm tra xem thanh toán đã thành công chưa
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Kiểm tra xem thanh toán đang chờ xác nhận chưa
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Kiểm tra xem thanh toán đã thất bại chưa
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Lấy tên hiển thị của phương thức thanh toán
     */
    public function getPaymentMethodName(): string
    {
        $methods = [
            'cash' => 'Tiền mặt',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'momo' => 'Momo',
            'vnpay' => 'VNPay',
            'mbbank' => 'MB Bank',
            'zalopay' => 'ZaloPay',
            'other' => 'Khác',
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Lấy tên hiển thị của trạng thái
     */
    public function getStatusName(): string
    {
        $statuses = [
            'pending' => 'Chờ xác nhận',
            'success' => 'Thành công',
            'failed' => 'Thất bại',
            'expired' => 'Hết hạn',
            'refunded' => 'Đã hoàn tiền',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Lấy màu cho badge trạng thái
     */
    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'success' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'expired' => 'bg-orange-100 text-orange-800',
            'refunded' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Format số tiền
     */
    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 0, ',', '.') . ' đ';
    }
}
