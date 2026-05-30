<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'apartment_id',
        'invite_id',
        'relationship',
        'temporary_status',
        'start_date',
        'end_date',
    ];

    /**
     * Tự động kích hoạt cập nhật trạng thái căn hộ khi thêm/bớt cư dân
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($resident) {
            $apartment = $resident->apartment;
            if ($apartment) {
                // Lưu căn hộ sẽ kích hoạt sự kiện saving của Apartment và tự động tính toán lại trạng thái
                $apartment->save();
            }
        });

        static::deleted(function ($resident) {
            $apartment = $resident->apartment;
            if ($apartment) {
                // Lưu căn hộ sẽ kích hoạt sự kiện saving của Apartment và tự động tính toán lại trạng thái
                $apartment->save();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }
}
