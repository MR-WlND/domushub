<?php

namespace App\Models;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApartmentInvite extends Model
{
    protected $table = 'apartment_invites';

    protected $fillable = [
        'block_id',
        'apartment_id',
        'created_by',
        'invite_code',
        'intended_relationship',
        'note',
        'status',
        'expired_at',
        'max_uses',
        'uses_count',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expired_at && $this->expired_at->isPast()) {
            return false;
        }

        return $this->uses_count < $this->max_uses;
    }
}
