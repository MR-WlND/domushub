<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];


    // 1 Block có nhiều Floor
    public function floors()
    {
        return $this->hasMany(Floor::class);
    }
}
