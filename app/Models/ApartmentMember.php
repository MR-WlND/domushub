<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApartmentMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'user_id',
        'name',
        'birth_year',
        'date_of_birth',
        'relationship',
        'phone',
        'email',
        'status',
    ];

    protected $casts = [
        'birth_year' => 'integer',
        'date_of_birth' => 'date',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invitation()
    {
        return $this->hasOne(Invitation::class, 'apartment_member_id');
    }
}
