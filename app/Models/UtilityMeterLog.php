<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $utility_meter_id
 * @property int $apartment_id
 * @property int|null $user_id
 * @property string $type
 * @property int $record_month
 * @property int $record_year
 * @property int $old_value
 * @property int $new_value
 * @property string $action
 * @property string|null $reject_reason
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\UtilityMeter|null $utilityMeter
 * @property-read \App\Models\Apartment $apartment
 * @property-read \App\Models\User|null $user
 */
class UtilityMeterLog extends Model
{
    protected $table = 'utility_meter_logs';

    protected $fillable = [
        'utility_meter_id',
        'apartment_id',
        'user_id',
        'type',
        'record_month',
        'record_year',
        'old_value',
        'new_value',
        'action',
        'reject_reason',
    ];

    /**
     * Relationship to the original utility meter.
     */
    public function utilityMeter(): BelongsTo
    {
        return $this->belongsTo(UtilityMeter::class, 'utility_meter_id');
    }

    /**
     * Relationship to the apartment.
     */
    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    /**
     * Relationship to the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get utility type label in Vietnamese.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'electricity' => 'Điện',
            'water'       => 'Nước',
            default       => $this->type,
        };
    }

    /**
     * Get action label in Vietnamese.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'recorded' => 'Ghi số',
            'updated'  => 'Chỉnh sửa',
            'approved' => 'Chốt số',
            'rejected' => 'Từ chối',
            default    => $this->action,
        };
    }

    /**
     * Get action badge CSS class.
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match ($this->action) {
            'recorded' => 'util-badge--recorded',
            'updated'  => 'util-badge--updated',
            'approved' => 'util-badge--success',
            'rejected' => 'util-badge--danger',
            default    => 'util-badge--outline',
        };
    }
}
