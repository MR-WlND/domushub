<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apartment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'floor_id',
        'apartment_number',
        'area',
        'status',
    ];

    /**
     * Quan hệ tầng
     */
    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    /**
     * Quan hệ cư dân
     */
    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }

    /**
     * Scope căn hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where(
            'status',
            '!=',
            'maintenance'
        );
    }

    /**
     * Scope căn trống
     */
    public function scopeVacant($query)
    {
        return $query->where(
            'status',
            'vacant'
        );
    }

    /**
     * Accessor trạng thái tiếng Việt
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {

            'vacant' => 'Còn trống',

            'occupied' => 'Đang ở',

            'maintenance' => 'Bảo trì',

            default => 'Không xác định',
        };
    }
}
