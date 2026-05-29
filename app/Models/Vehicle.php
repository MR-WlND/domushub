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
     * Mối quan hệ: Xe thuộc về một căn hộ
     * Dùng để hiển thị thông tin căn hộ lên bảng Admin
     */
    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    // --- Các hàm kiểm tra trạng thái ---

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
