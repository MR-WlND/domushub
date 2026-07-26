<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietPhuCap extends Model
{
    protected $table = 'chi_tiet_phu_cap';

    protected $fillable = [
        'bang_luong_id',
        'danh_muc_phu_cap_id',
        'so_tien',
    ];

    protected $casts = [
        'so_tien' => 'decimal:2',
    ];

    public function bangLuong(): BelongsTo
    {
        return $this->belongsTo(BangLuong::class, 'bang_luong_id');
    }

    public function danhMuc(): BelongsTo
    {
        return $this->belongsTo(DanhMucPhuCap::class, 'danh_muc_phu_cap_id');
    }
}
