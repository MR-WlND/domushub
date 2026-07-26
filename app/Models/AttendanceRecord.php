<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AttendanceRecord — Bản ghi chấm công nhân viên
 *
 * Business Rules:
 *  1. Một nhân viên chỉ có MỘT bản ghi cho mỗi ngày (unique user_id + work_date ở DB).
 *  2. Khi check-in: tính late_minutes ngay dựa vào check_in_at vs config start_time;
 *     status được set thành 'working' (đang làm việc, chưa xác định muộn/đúng).
 *  3. Khi check-out: tính working_hours từ check_in_at → check_out_at;
 *     sau đó CHỐT status cuối cùng:
 *       - Admin đã set 'absent' → giữ nguyên (không auto-override).
 *       - working_hours < 4h  → 'half_day' (tự động, hoặc Admin có thể set thủ công).
 *       - late_minutes > threshold → 'late'.
 *       - còn lại → 'present'.
 *  4. update() trong Controller luôn gọi lại computeLateMinutes() và computeFinalStatus()
 *     để đảm bảo dữ liệu nhất quán khi Admin sửa giờ.
 *  5. Không có chức năng xoá (kể cả Admin); chỉ được thêm hoặc chỉnh sửa.
 *  6. Phân quyền: chỉ Admin và Manager truy cập được (qua middleware group trong routes).
 */
class AttendanceRecord extends Model
{
    protected $fillable = [
        'user_id',
        'work_date',
        'check_in_at',
        'check_out_at',
        'status',
        'shift',
        'work_location',
        'working_hours',
        'late_minutes',
        'note',
        'snapshot_photo',
        'ip_address',
        'device_info',
        'camera_info',
        'liveness_verified',
        'recorded_by',
    ];

    protected $casts = [
        'work_date'         => 'date',
        'check_in_at'       => 'datetime',
        'check_out_at'      => 'datetime',
        'working_hours'     => 'decimal:2',
        'late_minutes'      => 'integer',
        'liveness_verified' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ── Status Helpers ─────────────────────────────────────────────

    public function isWorking(): bool  { return $this->status === 'working'; }
    public function isPresent(): bool  { return $this->status === 'present'; }
    public function isLate(): bool     { return $this->status === 'late'; }
    public function isAbsent(): bool   { return $this->status === 'absent'; }
    public function isHalfDay(): bool  { return $this->status === 'half_day'; }

    // ── Labels ─────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'working'  => 'Đang làm việc',
            'present'  => 'Đúng giờ',
            'late'     => 'Đi trễ',
            'absent'   => 'Vắng mặt',
            'half_day' => 'Nửa ngày',
            default    => $this->status,
        };
    }

    public function getShiftLabelAttribute(): string
    {
        return config('attendance.shifts')[$this->shift] ?? $this->shift;
    }

    // ── Business Logic ─────────────────────────────────────────────

    /**
     * BƯỚC 1 — Gọi ngay sau khi có check_in_at (dù chưa check-out).
     *
     * Tính số phút đến muộn dựa trên check_in_at vs config start_time.
     * late_minutes = 0 nếu vào đúng hoặc trước giờ chuẩn.
     * Status vẫn giữ 'working' — chưa chốt.
     */
    public function computeLateMinutes(): void
    {
        if (! $this->check_in_at) {
            $this->late_minutes = null;
            return;
        }

        $startTime = Carbon::parse(
            $this->work_date->format('Y-m-d') . ' ' . config('attendance.start_time', '08:00')
        );

        if ($this->check_in_at->gt($startTime)) {
            $this->late_minutes = (int) $startTime->diffInMinutes($this->check_in_at);
        } else {
            $this->late_minutes = 0;
        }
    }

    /**
     * BƯỚC 2 — Gọi sau khi có đủ check_in_at VÀ check_out_at.
     *
     * Tính working_hours, sau đó CHỐT status cuối cùng:
     *   - absent   → không override (Admin đã gán thủ công)
     *   - hours < 4h        → half_day  (có thể Admin set thủ công trước đó)
     *   - late_minutes > threshold → late
     *   - còn lại           → present
     *
     * Luôn gọi computeLateMinutes() trước để late_minutes luôn mới nhất.
     */
    public function computeFinalStatus(): void
    {
        if (! $this->check_in_at || ! $this->check_out_at) {
            return;
        }

        // Luôn cập nhật lại late_minutes theo check_in_at hiện tại
        $this->computeLateMinutes();

        // Tính giờ công
        $hours = $this->check_in_at->diffInMinutes($this->check_out_at) / 60;
        $this->working_hours = round($hours, 2);

        // Không override nếu Admin đã gán vắng mặt
        if ($this->status === 'absent') {
            return;
        }

        $threshold = config('attendance.late_threshold_minutes', 5);

        if ($hours < 4) {
            // Làm dưới 4 tiếng: nửa ngày
            // Admin cũng có thể set half_day thủ công khi tạo/sửa bản ghi
            $this->status = 'half_day';
        } elseif (($this->late_minutes ?? 0) > $threshold) {
            $this->status = 'late';
        } else {
            $this->status = 'present';
        }
    }

    /**
     * Reset về trạng thái "đang làm" khi xóa check_out (Admin sửa lại chưa checkout).
     * Xóa working_hours, giữ lại late_minutes (vẫn đã biết muộn bao nhiêu).
     */
    public function resetToWorking(): void
    {
        $this->check_out_at  = null;
        $this->working_hours = null;
        $this->status        = 'working';
        // Giữ nguyên late_minutes — đã tính từ check_in_at
    }
}
