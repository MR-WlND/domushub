<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'grace_period_minutes',
        'shift_rate',
        'status',
        'description',
    ];

    public function rosters()
    {
        return $this->hasMany(ShiftRoster::class);
    }
}
