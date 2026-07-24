<?php

namespace App\Models;

use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apartment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'floor_id',
        'apartment_type_id',
        'apartment_number',
        'area',
        'status',
        'description',
    ];

    /**
     * Tự động cập nhật trạng thái dựa trên số lượng cư dân
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($apartment) {
            // Đếm số cư dân hiện tại của căn hộ
            $residentsCount = $apartment->residents()->count();

            if ($residentsCount > 0) {
                // Nếu có cư dân, bắt buộc trạng thái là "Đang ở"
                $apartment->status = 'occupied';
            } else {
                // Nếu không có cư dân
                // Nếu trạng thái đang là "Đang ở", tự động chuyển về "Trống"
                if ($apartment->status === 'occupied') {
                    $apartment->status = 'vacant';
                }
                // Giữ nguyên trạng thái "Bảo trì" hoặc "Trống" theo thiết lập thủ công của Admin
            }
        });
    }

    /*
    |----------------------------
    | RELATIONSHIPS
    |----------------------------
    */

    /**
     * Quan hệ loại căn hộ
     */
    public function apartmentType(): BelongsTo
    {
        return $this->belongsTo(ApartmentType::class);
    }

    /**
     * Quan hệ tầng
     */
    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    /**
     * Quan hệ cư dân (Resident records)
     */
    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }

    /**
     * Users thuộc căn hộ này
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Nhân khẩu khai báo thuộc căn hộ này
     */
    public function declaredMembers(): HasMany
    {
        return $this->hasMany(ApartmentMember::class);
    }

    /**
     * Xe đăng ký thuộc căn hộ này
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Hóa đơn thuộc căn hộ này
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /*
    |----------------------------
    | SCOPES
    |----------------------------
    */

    /**
     * Scope căn hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'maintenance');
    }

    /**
     * Scope căn trống
     */
    public function scopeVacant($query)
    {
        return $query->where('status', 'vacant');
    }

    /*
    |----------------------------
    | ACCESSORS
    |----------------------------
    */

    /**
     * Accessor trạng thái tiếng Việt
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'vacant'      => 'Còn trống',
            'occupied'    => 'Đang ở',
            'maintenance' => 'Bảo trì',
            default       => 'Không xác định',
        };
    }

    /**
     * Lấy tên chủ hộ hoặc cư dân đầu tiên của căn hộ
     */
    public function getOwnerNameAttribute(): string
    {
        $owner = $this->residents()
            ->where('relationship', 'owner')
            ->first();
            
        if (!$owner) {
            $owner = $this->residents()->first();
        }
        
        return $owner ? ($owner->user->name ?? 'Cư dân căn hộ') : 'Cư dân căn hộ';
    }
}
