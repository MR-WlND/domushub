<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilityMeter extends Model
{
    protected $table = 'utility_meters';

    protected $fillable = [
        'apartment_id',
        'type',
        'record_month',
        'record_year',
        'old_value',
        'new_value',
        'usage_amount',
        'image_proof',
        'recorded_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($meter) {
            $meter->usage_amount = max(0, $meter->new_value - $meter->old_value);
        });
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
