<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_id',
        'floor_number',
    ];

    // Thuộc block
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    // Có nhiều căn hộ
    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }
}
