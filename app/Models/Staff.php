<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';

    protected $fillable = [
        'full_name', 'phone', 'cccd', 'address', 'dob', 'department_id', 'status'
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
