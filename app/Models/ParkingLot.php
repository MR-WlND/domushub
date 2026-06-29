<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingLot extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_number',
        'zone',
        'lot_type',
        'status',
        'apartment_id',
        'capacity',
    ];

    /**
     * Căn hộ đang sử dụng lốt đỗ này
     */
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    /**
     * Phương tiện đang đỗ tại lốt này
     */
    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }

    // --- Helpers ---

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    /**
     * Lấy số lượng xe đang sử dụng thực tế (dành cho Zone xe máy)
     */
    public function getCurrentUsage(): int
    {
        if ($this->lot_type === 'motorbike') {
            return Vehicle::where('status', 'active')
                ->whereIn('vehicle_type', ['motorbike', 'electric_bike'])
                ->count();
        }
        
        return $this->status === 'occupied' ? 1 : 0;
    }
}
