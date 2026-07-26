<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMucKhauTru extends Model
{
    protected $table = 'danh_muc_khau_tru';

    protected $fillable = [
        'ten_khau_tru',
        'loai',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
