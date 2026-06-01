<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'code',
        'role',
        'permissions',
        'building_id',
        'apartment_id',
        'apartment_member_id',
        'type',
        'status',
        'max_uses',
        'uses_count',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'expires_at' => 'datetime',
    ];

    public function apartmentMember()
    {
        return $this->belongsTo(ApartmentMember::class, 'apartment_member_id');
    }

    public function building()
    {
        return $this->belongsTo(Block::class, 'building_id');
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return $this->uses_count < $this->max_uses;
    }
}
