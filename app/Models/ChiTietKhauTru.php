<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietKhauTru extends Model
{
    protected $table = 'chi_tiet_khau_tru';

    protected $fillable = [
        'bang_luong_id',
        'danh_muc_khau_tru_id',
        'so_tien',
        'ly_do',
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
        return $this->belongsTo(DanhMucKhauTru::class, 'danh_muc_khau_tru_id');
    }
}
