<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $table = 'facilities';

    protected $fillable = [
        'name',
        'facility_type',
        'block_id',
        'floor_id',
        'capacity',
        'description',
        'status',
        'open_time',
        'close_time',
        'operating_days',
        'slot_duration',
        'fee_type',
        'price',
        'min_advance_booking_hours',
        'max_advance_booking_days',
        'booking_type',
        'rules',
        'images',
        // old fields for backward compatibility
        'price_per_slot',
        'price_per_person',
    ];

    protected $casts = [
        'price'            => 'decimal:0',
        'price_per_slot'   => 'decimal:0',
        'price_per_person' => 'decimal:0',
        'images'           => 'array',
        'operating_days'   => 'array',
    ];

    /**
     * Tòa nhà
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Tầng
     */
    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

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
        if ($this->fee_type === 'free' || !$this->price || $this->price == 0) {
            return 'Miễn phí';
        }

        $formattedPrice = number_format($this->price) . 'đ';

        return match ($this->fee_type) {
            'per_hour'   => $formattedPrice . ' / giờ',
            'per_use'    => $formattedPrice . ' / lượt',
            'per_person' => $formattedPrice . ' / người',
            default      => $formattedPrice,
        };
    }

    /**
     * Loại đặt chỗ
     */
    public function getBookingTypeLabelAttribute(): string
    {
        return match ($this->booking_type ?? 'time_slot') {
            'none'       => 'Không cần đặt trước',
            'time_slot'  => 'Theo khung giờ',
            'person'     => 'Theo người (Cũ)',
            'slot'       => 'Theo thời gian (Cũ)',
            default      => 'Theo khung giờ',
        };
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
        $current  = strtotime($this->open_time);
        $end      = strtotime($this->close_time);

        if ($this->slot_duration == 0) {
            $slots[] = [
                'start' => date('H:i', $current),
                'end'   => date('H:i', $end),
                'label' => date('H:i', $current) . ' – ' . date('H:i', $end),
            ];
            return $slots;
        }

        $duration = $this->slot_duration;
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
