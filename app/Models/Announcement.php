<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'status',
        'pinned',
        'image_path',
    ];

    protected $casts = [
        'pinned' => 'boolean',
    ];

    /**
     * Get the user (admin/manager) who created the announcement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to only include published announcements.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to order announcements: pinned first, then newest.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('pinned', 'desc')
                     ->orderBy('created_at', 'desc');
    }
}
