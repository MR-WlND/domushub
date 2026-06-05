<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_id',
        'floor_number',
        'name',
        'status',
        'description',
    ];

    /**
     * Tầng thuộc tòa nhà
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Tầng có nhiều căn hộ
     */
    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class);
    }

    /**
     * Tên hiển thị: ưu tiên name, fallback về "Tầng {floor_number}"
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? 'Tầng ' . $this->floor_number;
    }

    /**
     * Tự động sửa lỗi hiển thị dấu tiếng Việt nếu bị lỗi database (ví dụ T?ng -> Tầng)
     */
    public function getNameAttribute($value): ?string
    {
        if (is_null($value)) {
            return null;
        }
        return str_replace(['T?ng', '?'], ['Tầng', 'ầ'], $value);
    }
}
