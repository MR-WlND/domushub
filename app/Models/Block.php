<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }
}