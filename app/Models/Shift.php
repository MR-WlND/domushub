<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'start_time', 'end_time'];

    public function schedules()
    {
        return $this->hasMany(StaffSchedule::class);
    }

    public function requirements()
    {
        return $this->hasMany(ShiftRequirement::class);
    }
}
