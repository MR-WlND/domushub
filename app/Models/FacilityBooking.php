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
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

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
     * Label trạng thái tiếng Việt
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Chờ duyệt',
            'approved'  => 'Đã duyệt',
            'rejected'  => 'Từ chối',
            'cancelled' => 'Đã hủy',
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
            'rejected'  => 'danger',
            'cancelled' => 'secondary',
            'completed' => 'info',
            default     => 'secondary',
        };
    }
}
