<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMucPhuCap extends Model
{
    protected $table = 'danh_muc_phu_cap';

    protected $fillable = [
        'ten_phu_cap',
        'muc_mac_dinh',
        'is_active',
    ];

    protected $casts = [
        'muc_mac_dinh' => 'decimal:2',
        'is_active'    => 'boolean',
    ];
}
