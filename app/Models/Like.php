<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
    ];

    /**
     * Mối quan hệ đa hình với Post hoặc Comment
     */
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Lượt thích thuộc về một người dùng
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
