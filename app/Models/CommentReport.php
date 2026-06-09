<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentReport extends Model
{
    use HasFactory;

    protected $table = 'comment_reports';

    protected $fillable = [
        'comment_id',
        'user_id',
        'reason',
    ];

    /**
     * Báo cáo này thuộc về bình luận nào
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Báo cáo này do cư dân nào thực hiện
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
