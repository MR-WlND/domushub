<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostHide extends Model
{
    use HasFactory;

    protected $table = 'post_hides';

    protected $fillable = [
        'post_id',
        'user_id',
    ];

    /**
     * Mối quan hệ với bài đăng bị ẩn
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Mối quan hệ với người dùng đã ẩn bài đăng
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
