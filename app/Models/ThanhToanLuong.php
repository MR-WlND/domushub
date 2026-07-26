<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThanhToanLuong extends Model
{
    protected $table = 'thanh_toan_luong';

    protected $fillable = [
        'bang_luong_id',
        'hinh_thuc',
        'ngay_thanh_toan',
        'trang_thai',
        'xu_ly_boi',
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_thanh_toan' => 'datetime',
    ];

    public function bangLuong(): BelongsTo
    {
        return $this->belongsTo(BangLuong::class, 'bang_luong_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'xu_ly_boi');
    }
}
