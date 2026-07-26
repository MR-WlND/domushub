<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMucThuong extends Model
{
    protected $table = 'danh_muc_thuong';

    protected $fillable = [
        'ten_thuong',
        'mo_ta',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
