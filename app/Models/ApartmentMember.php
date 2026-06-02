<?php

namespace App\Models;

use App\Models\Apartment;
use App\Models\ApartmentInvite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApartmentMember extends Model
{
    protected $fillable = [
        'apartment_id',
        'invite_id',
        'name',
        'birth_year',
        'relationship',
        'status',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function invite(): BelongsTo
    {
        return $this->belongsTo(ApartmentInvite::class, 'invite_id');
    }
}
