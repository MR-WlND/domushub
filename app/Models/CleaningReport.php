<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleaningReport extends Model
{
    protected $fillable = [
        'reported_by', 'title', 'location', 'priority',
        'description', 'images', 'status',
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
}
