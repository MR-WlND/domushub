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

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($resident) {
            $apartment = $resident->apartment;
            if ($apartment) {
                // Lưu căn hộ sẽ kích hoạt sự kiện saving của Apartment và tự động tính toán lại trạng thái
                $apartment->save();
            }

            // Đồng bộ apartment_id vào bảng users
            $user = $resident->user;
            if ($user && $user->apartment_id !== $resident->apartment_id) {
                $user->update(['apartment_id' => $resident->apartment_id]);
            }
        });

        static::deleted(function ($resident) {
            $apartment = $resident->apartment;
            if ($apartment) {
                // Lưu căn hộ sẽ kích hoạt sự kiện saving của Apartment và tự động tính toán lại trạng thái
                $apartment->save();
            }

            // Đồng bộ apartment_id vào bảng users (gỡ bỏ hoặc lấy căn hộ còn lại của user)
            $user = $resident->user;
            if ($user) {
                $otherResident = self::where('user_id', $user->id)->first();
                $user->update(['apartment_id' => $otherResident ? $otherResident->apartment_id : null]);
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
