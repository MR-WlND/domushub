<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleaningTask extends Model
{
    protected $fillable = [
        'assigned_to', 'assigned_by', 'title', 'description',
        'area', 'area_group', 'start_time', 'end_time',
        'priority', 'status', 'checklist', 'manager_note',
        'completed_at', 'task_date',
    ];

    protected function casts(): array
    {
        return [
            'checklist' => 'array',
            'task_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
