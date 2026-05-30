<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    protected $fillable = ['block_id', 'floor_number'];

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }
}
