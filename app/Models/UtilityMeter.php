<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $apartment_id
 * @property string $type
 * @property int $record_month
 * @property int $record_year
 * @property int $old_value
 * @property int $new_value
 * @property int $usage_amount
 * @property string|null $image_proof
 * @property array|null $images
 * @property int $recorded_by
 * @property string $status
 * @property bool $is_reset
 * @property int|null $rejected_by
 * @property string|null $reject_reason
 * @property-read \App\Models\Apartment $apartment
 * @property-read \App\Models\User|null $recorder
 * @property-read \App\Models\User|null $rejecter
 */
class UtilityMeter extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

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
        'images',
        'recorded_by',
        'status',
        'is_reset',
        'rejected_by',
        'reject_reason',
        'is_complained',
        'complaint_reason',
    ];

    /**
     * Tự động cast cột `images` (JSON) ↔ PHP array
     */
    protected $casts = [
        'images' => 'array',
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

        static::created(function ($meter) {
            try {
                \App\Helpers\SystemLogger::log(
                    'utility',
                    'Ghi nhận số kỳ mới: ' . number_format($meter->new_value),
                    [
                        'utility_meter_id' => $meter->id,
                        'apartment_id'     => $meter->apartment_id,
                        'user_id'          => \Illuminate\Support\Facades\Auth::id() ?? $meter->recorded_by,
                        'type'             => $meter->type,
                        'record_month'     => $meter->record_month,
                        'record_year'      => $meter->record_year,
                        'old_value'        => $meter->old_value,
                        'new_value'        => $meter->new_value,
                        'action'           => 'recorded',
                    ]
                );
            } catch (\Exception $e) {
                // Ignore failure to prevent locking the app
            }
        });

        static::updated(function ($meter) {
            try {
                $action = 'updated';
                $rejectReason = null;
                $userId = \Illuminate\Support\Facades\Auth::id();

                if ($meter->wasChanged('status')) {
                    if ($meter->status === 'approved') {
                        $action = 'approved';
                    } elseif ($meter->status === 'rejected') {
                        $action = 'rejected';
                        $rejectReason = $meter->reject_reason;
                        $userId = \Illuminate\Support\Facades\Auth::id() ?? $meter->rejected_by;
                    }
                }

                // Log if any of the monitored fields changed
                if ($meter->wasChanged(['old_value', 'new_value', 'status', 'reject_reason'])) {
                    $desc = 'Cập nhật chỉ số điện nước';
                    if ($action === 'approved') $desc = 'Đã duyệt & chốt số kỳ này';
                    if ($action === 'rejected') $desc = 'Từ chối chốt số';
                    
                    \App\Helpers\SystemLogger::log(
                        'utility',
                        $desc,
                        [
                            'utility_meter_id' => $meter->id,
                            'apartment_id'     => $meter->apartment_id,
                            'user_id'          => $userId,
                            'type'             => $meter->type,
                            'record_month'     => $meter->record_month,
                            'record_year'      => $meter->record_year,
                            'old_value'        => $meter->old_value,
                            'new_value'        => $meter->new_value,
                            'original_new_value' => $meter->getOriginal('new_value'),
                            'action'           => $action,
                            'reject_reason'    => $rejectReason,
                        ]
                    );
                }
            } catch (\Exception $e) {
                // Ignore failure to prevent locking the app
            }
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

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
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
            'water' => 'Nước',
            default => 'Không xác định',
        };
    }
}
