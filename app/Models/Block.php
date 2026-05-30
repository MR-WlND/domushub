<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = ['name', 'description'];

    public function floors()
    {
        return $this->hasMany(Floor::class);
    }

    public function apartments()
    {
        return $this->hasManyThrough(Apartment::class, Floor::class);
    }
}
