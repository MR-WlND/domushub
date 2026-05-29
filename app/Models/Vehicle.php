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

    /**
     * Kiểm tra xe đang chờ duyệt
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Kiểm tra xe đã được duyệt
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Kiểm tra xe bị từ chối
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
