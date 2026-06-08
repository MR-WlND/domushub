<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityMeter extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';

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
        'status',
    ];

    /*
    |----------------------------
    | BOOT – Tự động tính tiêu thụ
    |----------------------------
    */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($meter) {
            $meter->usage_amount = max(0, $meter->new_value - $meter->old_value);
        });
    }

    /*
    |----------------------------
    | RELATIONSHIPS
    |----------------------------
    */

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /*
    |----------------------------
    | HELPERS
    |----------------------------
    */

    /**
     * Lấy chỉ số mới (new_value) của kỳ trước làm chỉ số cũ cho kỳ hiện tại.
     * Ưu tiên tháng liền trước, nếu không có thì lấy bản ghi gần nhất.
     */
    public static function getPreviousNewValue(int $apartmentId, string $type, int $month, int $year): ?int
    {
        // Tính tháng/năm trước
        $prevMonth = $month - 1;
        $prevYear  = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear  = $year - 1;
        }

        // Thử lấy đúng tháng trước
        $prev = self::where('apartment_id', $apartmentId)
            ->where('type', $type)
            ->where('record_month', $prevMonth)
            ->where('record_year', $prevYear)
            ->value('new_value');

        if (! is_null($prev)) {
            return (int) $prev;
        }

        // Nếu không có, lấy bản ghi gần nhất trước kỳ hiện tại
        return self::where('apartment_id', $apartmentId)
            ->where('type', $type)
            ->where(function ($q) use ($month, $year) {
                $q->where('record_year', '<', $year)
                  ->orWhere(function ($q2) use ($month, $year) {
                      $q2->where('record_year', $year)
                         ->where('record_month', '<', $month);
                  });
            })
            ->orderByDesc('record_year')
            ->orderByDesc('record_month')
            ->value('new_value');
    }

    /*
    |----------------------------
    | ACCESSORS
    |----------------------------
    */

    /**
     * Nhãn loại tiếng Việt
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'electricity' => 'Điện',
            'water'       => 'Nước',
            default       => $this->type,
        };
    }
}
