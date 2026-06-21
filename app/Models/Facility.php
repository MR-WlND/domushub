<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $table = 'facilities';

    protected $fillable = [
        'name',
        'capacity',
        'description',
        'status',
    ];

    /**
     * Tất cả booking của tiện ích này
     */
    public function bookings()
    {
        return $this->hasMany(FacilityBooking::class, 'facility_id');
    }

    /**
     * Booking đang chờ duyệt
     */
    public function pendingBookings()
    {
        return $this->hasMany(FacilityBooking::class, 'facility_id')->where('status', 'pending');
    }

    /**
     * Label trạng thái tiếng Việt
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available'   => 'Hoạt động',
            'maintenance' => 'Bảo trì',
            'closed'      => 'Đóng cửa',
            default       => $this->status,
        };
    }
}
