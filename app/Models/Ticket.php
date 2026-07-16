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
        'ticket_type',
        'title',
        'description',
        'reported_person',
        'accused_user_id',
        'accused_response',
        'accused_response_comment',
        'accused_responded_at',
        'images',
        'priority',
        'status',
        'rating',
        'feedback_comment',
    ];

    protected $casts = [
        'images'              => 'array',
        'accused_responded_at' => 'datetime',
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

    public function costs()
    {
        return $this->hasMany(TicketCost::class)->orderBy('created_at', 'asc');
    }

    public function accusedUser()
    {
        return $this->belongsTo(User::class, 'accused_user_id');
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

    public function ticketTypeLabel(): string
    {
        return match ($this->ticket_type) {
            'complaint' => 'Phản ánh sự cố',
            'report'    => 'Tố cáo',
            default     => $this->ticket_type,
        };
    }

    public function isReport(): bool
    {
        return $this->ticket_type === 'report';
    }

    public function accusedResponseLabel(): string
    {
        return match ($this->accused_response) {
            'confirmed' => 'Xác nhận',
            'denied'    => 'Phản đối',
            default     => 'Chưa phản hồi',
        };
    }

    public function hasAccusedResponse(): bool
    {
        return $this->accused_response !== null;
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
}
