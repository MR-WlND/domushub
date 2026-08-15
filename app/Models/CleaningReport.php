<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleaningReport extends Model
{
    protected $fillable = [
        'reported_by', 'block_id', 'floor_id', 'title', 'location', 'priority',
        'description', 'images', 'status', 'assigned_to', 'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
        ];
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
