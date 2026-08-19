<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id', 'shift_id', 'work_date', 'is_leader', 'block_id', 'floor_id'];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }
}
