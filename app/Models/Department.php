<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;
    
    protected $fillable = ['code', 'name', 'is_shift', 'description', 'status'];

    public function staffs()
    {
        return $this->hasMany(Staff::class);
    }
}
