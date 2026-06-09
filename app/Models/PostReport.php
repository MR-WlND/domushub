<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostReport extends Model
{
    use HasFactory;

    protected $table = 'post_reports';

    protected $fillable = [
        'post_id',
        'user_id',
        'reason',
    ];

    /**
     * Báo cáo này thuộc về bài viết nào
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Báo cáo này được thực hiện bởi cư dân nào
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
