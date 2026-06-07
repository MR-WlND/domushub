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
        'note',
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
}
