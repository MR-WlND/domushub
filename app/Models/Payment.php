<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $table = 'payments';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->receipt_code)) {
                $payment->receipt_code = static::generateReceiptCode();
            }
        });
    }

    protected $fillable = [
        'bill_id',
        'amount',
        'payment_method',
        'transaction_code',
        'receipt_code',
        'vnp_txn_ref',
        'status',
        'paid_at',
        'note',
        'proof_image',
        'payer_name',
        'recorded_by',
        'refunded_at',
        'refund_note',
        'refunded_by',
    ];

    protected $casts = [
        'paid_at'     => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // ─── RELATIONSHIPS ───

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'bill_id');
    }

    /**
     * Sinh mã biên lai nội bộ duy nhất dạng: REC-YYYYMMDD-XXXXX
     */
    public static function generateReceiptCode(): string
    {
        do {
            $code = 'REC-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        } while (static::where('receipt_code', $code)->exists());

        return $code;
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by')->withDefault(['name' => '—']);
    }

    public function refunder()
    {
        return $this->belongsTo(User::class, 'refunded_by')->withDefault(['name' => '—']);
    }

    // ─── ACCESSORS ───

    public function getIsRefundedAttribute(): bool
    {
        return $this->status === 'refunded';
    }

    public function getPaymentCodeAttribute(): string
    {
        return 'PAY-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash'          => 'Tiền mặt',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'vnpay'         => 'VNPAY',
            'momo'          => 'Ví MoMo',
            'other'         => 'Khác',
            default         => $this->payment_method ?? 'Không xác định',
        };
    }
}
