<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleLog extends Model
{
    use HasFactory;

    protected $table = 'vehicle_logs';

    protected $fillable = [
        'vehicle_id',
        'checked_in_by',
        'checked_out_by',
        'check_in_at',
        'check_out_at',
        'qr_code',
        'status',
        'guest_plate',
        'guest_vehicle_type',
        'guest_name',
        'guest_phone',
        'guest_note',
        'is_guest',
    ];

    protected $casts = [
        'check_in_at'  => 'datetime',
        'check_out_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Xe liên quan đến lượt ra/vào
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Nhân viên bảo vệ check-in
     */
    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    /**
     * Nhân viên bảo vệ check-out
     */
    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    /**
     * Xe hiện đang ở trong (inside)
     */
    public function isInside(): bool
    {
        return $this->status === 'inside';
    }
}
