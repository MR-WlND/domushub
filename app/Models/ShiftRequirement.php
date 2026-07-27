<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShiftRequirement extends Model
{
    use HasFactory;

    protected $fillable = ['shift_id', 'department_id', 'required_staff'];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
