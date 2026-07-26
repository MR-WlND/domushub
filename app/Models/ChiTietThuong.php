<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietThuong extends Model
{
    protected $table = 'chi_tiet_thuong';

    protected $fillable = [
        'bang_luong_id',
        'danh_muc_thuong_id',
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
        return $this->belongsTo(DanhMucThuong::class, 'danh_muc_thuong_id');
    }
}
