<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apartment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'floor_id',
        'apartment_number',
        'area',
        'status',
    ];

    /*
    |----------------------------
    | RELATIONSHIPS
    |----------------------------
    */

    // Thuộc tầng
    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    // Có nhiều cư dân
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Có nhiều xe
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
