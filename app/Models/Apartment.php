<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apartment extends Model
{
    use SoftDeletes;

    protected $fillable = ['floor_id', 'apartment_number', 'area', 'status'];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Tên đầy đủ: Block A – Tầng 3 – P301
     */
    public function getFullNameAttribute(): string
    {
        $block = optional($this->floor->block)->name ?? '';
        $floor = optional($this->floor)->floor_number ?? '';
        return "{$block} – Tầng {$floor} – {$this->apartment_number}";
    }
}
