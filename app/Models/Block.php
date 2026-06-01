<?php

namespace App\Models;

use App\Models\Apartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Block extends Model
{
    protected $fillable = [
        'name',
        'code',
        'status',
        'number_of_floors',
        'total_apartments',
        'manager_name',
        'manager_contact',
        'description',  
    ];

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    public function apartments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Apartment::class,
            Floor::class,
            'block_id',      // Foreign key on floors table
            'floor_id',      // Foreign key on apartments table
            'id',            // Local key on blocks table
            'id'             // Local key on floors table
        );
    }
}
