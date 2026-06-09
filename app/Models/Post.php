<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'price',
        'status',
        'ai_flagged',
    ];

    /**
     * Mối quan hệ với danh sách hình ảnh của bài viết này
     */
    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class);
    }

    /**
     * Mối quan hệ với danh sách các báo cáo của bài viết này
     */
    public function reports(): HasMany
    {
        return $this->hasMany(PostReport::class);
    }

    /**
     * Mối quan hệ với người đăng bài (cư dân/admin)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ với danh sách bình luận của bài đăng
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Lấy danh sách bình luận cấp 1 hoặc sắp xếp theo thời gian
     */
    public function publishedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'asc');
    }
}
