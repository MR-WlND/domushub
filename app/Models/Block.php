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
            'block_id', // foreign key on floors table
            'floor_id', // foreign key on apartments table
            'id',       // local key on blocks table
            'id'        // local key on floors table
        );
    }
}