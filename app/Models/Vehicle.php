<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'apartment_id',
        'parking_lot_id',
        'license_plate',
        'vehicle_type',
        'brand',
        'image',
        'qr_code',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Mối quan hệ: Xe thuộc về một căn hộ
     */
    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    /**
     * Mối quan hệ: Xe có thể thuộc về một lốt đỗ (đặc biệt với ô tô)
     */
    public function parkingLot()
    {
        return $this->belongsTo(ParkingLot::class, 'parking_lot_id');
    }

    // =========================================================================
    // STATUS HELPERS
    // =========================================================================

    /** Đang chờ duyệt đăng ký (xe mới) */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /** Đang hoạt động bình thường */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /** Kiểm tra xem xe có đang ở trong bãi không */
    public function isInside(): bool
    {
        $lastLog = \App\Models\VehicleLog::where('vehicle_id', $this->id)->latest()->first();
        return $lastLog && $lastLog->status === 'inside';
    }

    /**
     * Đến hạn đóng phí nhưng chưa thanh toán.
     * Xe vẫn được vào hầm nhưng cảnh báo.
     */
    public function isPendingRenewal(): bool
    {
        return $this->status === 'pending_renewal';
    }

    /**
     * Bị khóa do vi phạm hoặc quá hạn đóng phí quá lâu.
     * Hệ thống barie từ chối mở cửa.
     * - Ô tô: vẫn giữ lốt (không giải phóng).
     * - Xe máy/điện: không có lốt, chỉ chặn theo biển số.
     */
    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    /** Ngừng sử dụng (cư dân hủy hoặc admin vô hiệu hóa) */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    // =========================================================================
    // LOGIC HELPERS
    // =========================================================================

    /**
     * Xe có được phép vào hầm không?
     * active + pending_renewal: được vào (pending_renewal chỉ cảnh báo).
     */
    public function canEnter(): bool
    {
        return in_array($this->status, ['active', 'pending_renewal']);
    }

    /**
     * Xe có bị chặn hoàn toàn không?
     * locked + inactive: bị từ chối tại cổng.
     */
    public function isBlocked(): bool
    {
        return in_array($this->status, ['locked', 'inactive']);
    }

    // =========================================================================
    // VEHICLE TYPE HELPERS
    // =========================================================================

    public function isCar(): bool
    {
        return $this->vehicle_type === 'car';
    }

    public function isMotorbike(): bool
    {
        return in_array($this->vehicle_type, ['motorbike', 'electric_bike']);
    }

    public function isBicycle(): bool
    {
        return $this->vehicle_type === 'bicycle';
    }

    // =========================================================================
    // LABEL HELPERS
    // =========================================================================

    /** Nhãn hiển thị tiếng Việt cho status */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'            => 'Chờ duyệt',
            'awaiting_payment'   => 'Chờ thanh toán',
            'active'             => 'Đang hoạt động',
            'pending_renewal'    => 'Chờ gia hạn phí',
            'locked'             => 'Đã khóa',
            'inactive'           => 'Ngừng sử dụng',
            default              => ucfirst($this->status),
        };
    }

    /** Nhãn hiển thị tiếng Việt cho vehicle_type */
    public function typeLabel(): string
    {
        return match ($this->vehicle_type) {
            'car'           => 'Ô tô',
            'electric_bike' => 'Xe điện',
            'motorbike'     => 'Xe máy',
            'bicycle'       => 'Xe đạp',
            default         => ucfirst($this->vehicle_type),
        };
    }
}
