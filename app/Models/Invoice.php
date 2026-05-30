<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'apartment_id',
        'created_by',
        'invoice_code',
        'title',
        'type',
        'amount',
        'billing_month',
        'due_date',
        'status',
        'paid_at',
        'payment_method',
        'note',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'billing_month' => 'date',
        'due_date'      => 'date',
        'paid_at'       => 'datetime',
    ];

    /* ── Relationships ── */

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /* ── Scopes ── */

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeOfMonth($query, $year, $month)
    {
        return $query->whereYear('billing_month', $year)
                     ->whereMonth('billing_month', $month);
    }

    /* ── Helpers ── */

    public static function typeLabel(string $type): string
    {
        return match($type) {
            'electricity'    => 'Tiền điện',
            'water'          => 'Tiền nước',
            'management_fee' => 'Phí quản lý',
            'parking'        => 'Phí gửi xe',
            'other'          => 'Phí khác',
            default          => $type,
        };
    }

    public static function statusLabel(string $status): string
    {
        return match($status) {
            'paid'    => 'Đã thanh toán',
            'unpaid'  => 'Chưa thanh toán',
            'overdue' => 'Quá hạn',
            default   => $status,
        };
    }

    public static function generateCode(): string
    {
        $year  = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('INV-%d-%04d', $year, $count);
    }
}
