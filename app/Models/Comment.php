<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
    ];

    /**
     * Bình luận thuộc về một bài đăng
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Bình luận được viết bởi một người dùng
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bình luận cha (nếu là câu trả lời cho bình luận khác)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Danh sách các câu trả lời trực tiếp của bình luận này
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Danh sách các báo cáo vi phạm của bình luận này
     */
    public function reports(): HasMany
    {
        return $this->hasMany(CommentReport::class);
    }

    /**
     * Danh sách lượt thích của bình luận (quan hệ đa hình)
     */
    public function likes(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Kiểm tra nhanh xem user hiện tại đã thích bình luận chưa (tránh N+1 query)
     */
    public function likedByCurrentUser(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Like::class, 'likeable')->where('user_id', auth()->id());
    }
}
