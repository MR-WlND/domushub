<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePrice extends Model
{
    protected $fillable = [
        'service_type',
        'price_per_unit',
        'effective_from',
        'effective_to',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'price_per_unit' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    /**
     * Lấy đơn giá hiện hành theo loại dịch vụ.
     */
    public static function getCurrentPrice(string $serviceType, ?string $date = null): ?float
    {
        $date = $date ?? now()->toDateString();

        $price = static::where('service_type', $serviceType)
            ->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', $date);
            })
            ->orderByDesc('effective_from')
            ->first();

        return $price?->price_per_unit;
    }
}
