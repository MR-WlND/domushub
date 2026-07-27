<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'sender_name',
        'tracking_code',
        'carrier',
        'description',
        'status',
        'arrived_at',
        'received_at',
        'returned_at',
        'note',
        'created_by',
    ];

    protected $casts = [
        'arrived_at'  => 'datetime',
        'received_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isReceived(): bool
    {
        return $this->status === 'received';
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Chờ nhận',
            'notified' => 'Đã thông báo',
            'received' => 'Đã nhận',
            'returned' => 'Đã hoàn trả',
            default    => 'Không rõ',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'warning',
            'notified' => 'info',
            'received' => 'success',
            'returned' => 'secondary',
            default    => 'secondary',
        };
    }
}
