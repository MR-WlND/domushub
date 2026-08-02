<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApartmentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'base_service_fee',
        'bedroom_count',
        'bathroom_count',
        'living_room_count',
        'balcony_direction',
        'furniture_status',
        'furniture_list',
    ];

    protected $casts = [
        'furniture_list' => 'array',
    ];

    /**
     * Quan hệ danh sách căn hộ thuộc loại này
     */
    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class);
    }
}
