<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketProgress extends Model
{
    public $timestamps = false;

    protected $table = 'ticket_progress';

    protected $fillable = [
        'ticket_id',
        'status',
        'comment',
        'image_proof',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function getImageProofAttribute($value)
    {
        if (!$value) return [];
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        return [$value]; // Legacy single image string
    }

    // ── Relationships ───────────────────────────────────────────

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Label Helper ────────────────────────────────────────────

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'     => 'Chờ xử lý',
            'assigned'    => 'Đã phân công',
            'in_progress' => 'Đang xử lý',
            'completed'   => 'Hoàn thành',
            'cancelled'   => 'Đã hủy',
            default       => $this->status,
        };
    }

    protected static function booted()
    {
        static::created(function ($progress) {
            broadcast(new \App\Events\TicketProgressUpdated($progress))->toOthers();
        });
    }
}
