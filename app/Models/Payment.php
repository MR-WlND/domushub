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
        'receipt_code',
        'vnp_txn_ref',
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
     * Sinh mã biên lai nội bộ duy nhất dạng: REC-YYYYMMDD-XXXXX
     */
    public static function generateReceiptCode(): string
    {
        do {
            $code = 'REC-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        } while (static::where('receipt_code', $code)->exists());

        return $code;
    }
}
