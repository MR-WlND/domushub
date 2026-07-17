<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'code',
        'role',
        'permissions',
        'max_uses',
        'uses_count',
        'expires_at',
        'created_by'
    ];

    protected $casts = [
        'permissions' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Kiểm tra xem mã mời còn hợp lệ để dùng hay không.
     */
    public function isValid(): bool
    {
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        return $this->uses_count < $this->max_uses;
    }
}
