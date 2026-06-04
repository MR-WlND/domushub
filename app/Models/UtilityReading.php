<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityReading extends Model
{
    protected $fillable = [
        'apartment_id',
        'service_type',
        'billing_month',
        'billing_year',
        'previous_reading',
        'current_reading',
        'consumption',
        'unit_price',
        'total_amount',
        'status',
        'recorded_by',
        'finalized_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'previous_reading' => 'decimal:2',
            'current_reading' => 'decimal:2',
            'consumption' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'finalized_at' => 'datetime',
        ];
    }

    // ── Relationships ──

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ── Scopes ──

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('billing_month', $month)->where('billing_year', $year);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalized');
    }

    // ── Helpers ──

    /**
     * Lấy chỉ số cuối kỳ của tháng trước (dùng làm chỉ số đầu kỳ tháng này).
     */
    public static function getPreviousReading(int $apartmentId, string $serviceType, int $month, int $year): float
    {
        // Tháng trước
        $prevMonth = $month === 1 ? 12 : $month - 1;
        $prevYear = $month === 1 ? $year - 1 : $year;

        $previous = static::where('apartment_id', $apartmentId)
            ->where('service_type', $serviceType)
            ->where('billing_month', $prevMonth)
            ->where('billing_year', $prevYear)
            ->first();

        return (float) ($previous?->current_reading ?? 0);
    }

    /**
     * Tính toán tiêu thụ và thành tiền.
     */
    public function calculateAmounts(): void
    {
        $this->consumption = max(0, $this->current_reading - $this->previous_reading);
        $this->total_amount = $this->consumption * $this->unit_price;
    }
}
