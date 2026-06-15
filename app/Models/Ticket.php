<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'apartment_id',
        'sender_id',
        'handler_id',
        'title',
        'description',
        'images',
        'priority',
        'status',
        'rating',
        'feedback_comment',
        'reopened_count',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    // ── Relationships ───────────────────────────────────────────

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

    public function progress()
    {
        return $this->hasMany(TicketProgress::class)->orderBy('created_at', 'asc');
    }

    // ── Label Helpers ───────────────────────────────────────────

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

    public function priorityLabel(): string
    {
        return match ($this->priority) {
            'low'    => 'Thấp',
            'medium' => 'Trung bình',
            'high'   => 'Cao',
            'urgent' => 'Khẩn cấp',
            default  => $this->priority,
        };
    }

    // ── Status Checkers ─────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canCancel(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Kiểm tra user cụ thể có quyền hủy phản ánh không (chỉ người gửi)
     */
    public function canCancelBy(int $userId): bool
    {
        return $this->canCancel() && $this->sender_id === $userId;
    }

    public function canFeedback(): bool
    {
        return $this->status === 'completed' && is_null($this->rating);
    }

    public function needsRecheck(): bool
    {
        return $this->status === 'in_progress' && $this->reopened_count > 0;
    }
}
