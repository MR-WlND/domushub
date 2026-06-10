<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'resident_id',
        'apartment_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'technician_note',
        'resolved_at',
        'confirmed_by',
        'confirmed_at',
        'images',
    ];

    protected $casts = [
        'images'       => 'array',
        'assigned_at'  => 'datetime',
        'resolved_at'  => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    public function resident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public static function categoryLabel(string $category): string
    {
        return match ($category) {
            'electrical'     => 'Điện',
            'plumbing'       => 'Nước / Ống nước',
            'elevator'       => 'Thang máy',
            'cleaning'       => 'Vệ sinh',
            'security'       => 'An ninh',
            'infrastructure' => 'Hạ tầng / Kết cấu',
            default          => 'Khác',
        };
    }

    public static function priorityLabel(string $priority): string
    {
        return match ($priority) {
            'low'    => 'Thấp',
            'medium' => 'Trung bình',
            'high'   => 'Cao',
            'urgent' => 'Khẩn cấp',
            default  => $priority,
        };
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'pending'    => 'Chờ tiếp nhận',
            'assigned'   => 'Đã phân công',
            'in_progress' => 'Đang xử lý',
            'resolved'   => 'Đã xử lý',
            'confirmed'  => 'Hoàn thành',
            'closed'     => 'Đã đóng',
            default      => $status,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel($this->status);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryLabel($this->category);
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::priorityLabel($this->priority);
    }
}
