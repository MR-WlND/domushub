<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'status',
        'image',
        'total_floors',
        'total_basements',
        'apartments_per_floor',
        'amenities',
    ];

    protected $casts = [
        'amenities' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($block) {
            if ($block->isForceDeleting()) {
                $block->floors()->withTrashed()->each(function ($floor) {
                    $floor->forceDelete();
                });
            } else {
                $block->floors()->each(function ($floor) {
                    $floor->delete(); // Soft delete floors
                });
            }
        });

        static::restoring(function ($block) {
            $block->floors()->withTrashed()->each(function ($floor) {
                $floor->restore(); // Restore soft-deleted floors
            });
        });
    }

    /**
     * Tòa nhà có nhiều tầng
     */
    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class)->orderBy('floor_number');
    }

    /**
     * Tòa nhà có nhiều căn hộ (qua tầng)
     */
    public function apartments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Apartment::class,
            Floor::class,
            'block_id',   // FK trên floors
            'floor_id',   // FK trên apartments
            'id',
            'id'
        );
    }
}
