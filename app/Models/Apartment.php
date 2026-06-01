<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_id',
        'apartment_number',
        'area',
        'status',
    ];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }
}
