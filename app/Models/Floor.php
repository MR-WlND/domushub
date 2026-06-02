<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    protected $fillable = [
        'block_id',
        'floor_number',
        'name',
        'status',
        'description',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class);
    }

    /**
     * Tên hiển thị: ưu tiên name, fallback về "Tầng {floor_number}"
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? 'Tầng ' . $this->floor_number;
    }
}