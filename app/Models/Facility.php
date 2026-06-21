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
        'open_time',
        'close_time',
        'slot_duration',
        'price_per_slot',
        'rules',
    ];

    protected $casts = [
        'price_per_slot' => 'decimal:0',
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

    /**
     * Hiển thị khung giờ hoạt động
     */
    public function getOperatingHoursAttribute(): string
    {
        if ($this->open_time && $this->close_time) {
            return substr($this->open_time, 0, 5) . ' – ' . substr($this->close_time, 0, 5);
        }
        return 'Chưa cài đặt';
    }

    /**
     * Hiển thị giá dạng text
     */
    public function getPriceLabelAttribute(): string
    {
        if (!$this->price_per_slot || $this->price_per_slot == 0) {
            return 'Miễn phí';
        }
        return number_format($this->price_per_slot) . 'đ / ' . $this->slot_duration . ' phút';
    }

    /**
     * Danh sách các slot thời gian trong ngày dựa trên open/close time
     */
    public function getTimeSlots(): array
    {
        if (!$this->open_time || !$this->close_time) {
            return [];
        }

        $slots    = [];
        $duration = $this->slot_duration ?: 60;
        $current  = strtotime($this->open_time);
        $end      = strtotime($this->close_time);

        while ($current + ($duration * 60) <= $end) {
            $slotEnd = $current + ($duration * 60);
            $slots[] = [
                'start' => date('H:i', $current),
                'end'   => date('H:i', $slotEnd),
                'label' => date('H:i', $current) . ' – ' . date('H:i', $slotEnd),
            ];
            $current = $slotEnd;
        }

        return $slots;
    }
}
