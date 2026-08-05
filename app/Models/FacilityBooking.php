<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityBooking extends Model
{
    protected $table = 'facility_bookings';

    protected $fillable = [
        'facility_id',
        'user_id',
        'booking_date',
        'start_time',
        'end_time',
        'number_of_people',
        'status',
        'qr_code',
        'checked_in_at',
        'payment_status',
        'payment_method',
        'bill_id',
    ];

    protected $casts = [
        'booking_date'   => 'date',
        'checked_in_at'  => 'datetime',
    ];

    /**
     * Hóa đơn liên kết với lịch đặt (nếu có phí)
     */
    public function bill()
    {
        return $this->belongsTo(\App\Models\Invoice::class, 'bill_id');
    }

    /**
     * Tiện ích được đặt
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    /**
     * Cư dân đặt lịch
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Tạo QR code unique cho booking
     */
    public function generateQrCode(): void
    {
        $qr = 'QR_' . $this->id . '_' . \Str::random(12) . '_' . time();
        $this->update(['qr_code' => $qr]);
    }

    /**
     * Check-in bằng QR → chuyển sang trạng thái 'used'
     */
    public function checkIn(): bool
    {
        if ($this->status !== 'approved') {
            return false;
        }
        $this->update([
            'status'        => 'used',
            'checked_in_at' => now(),
        ]);
        return true;
    }

    /**
     * Label trạng thái tiếng Việt
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Chờ duyệt',
            'approved'  => 'Đã duyệt',
            'used'      => 'Đã sử dụng',
            'cancelled' => 'Đã hủy',
            'rejected'  => 'Từ chối',
            'completed' => 'Hoàn thành',
            default     => $this->status,
        };
    }

    /**
     * CSS class theo trạng thái
     */
    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'warning',
            'approved'  => 'success',
            'used'      => 'info',
            'cancelled' => 'secondary',
            'rejected'  => 'danger',
            'completed' => 'primary',
            default     => 'secondary',
        };
    }

    /**
     * Label trạng thái thanh toán
     */
    public function getPaymentLabelAttribute(): string
    {
        return $this->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
    }

    public function getAmountAttribute(): int
    {
        if (!$this->facility) {
            return 0;
        }

        $feeType = $this->facility->fee_type;
        $price = (int)($this->facility->price ?? 0);

        if ($feeType === 'free' || $price <= 0) {
            return 0;
        }

        if ($feeType === 'per_person') {
            $people = max(1, (int)($this->number_of_people ?? 1));
            return $price * $people;
        }

        if ($feeType === 'per_use') {
            return $price;
        }

        if ($feeType === 'per_hour') {
            $startTime = strtotime($this->start_time);
            $endTime   = strtotime($this->end_time);
            $minutes   = ($endTime - $startTime) / 60;
            // Calculate hours (e.g. 90 mins = 1.5 hours)
            // But since price is int, maybe use round or ceil for parts of an hour
            $hours = ceil($minutes / 60);
            return (int)($price * $hours);
        }

        return 0;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Chờ duyệt',
            'approved'  => 'Đã duyệt',
            'used'      => 'Đã sử dụng',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy',
            'rejected'  => 'Bị từ chối',
            default     => $this->status ?? 'Không xác định',
        };
    }
}
