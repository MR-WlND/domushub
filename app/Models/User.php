<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'cccd',
        'email',
        'password',
        'role',
        'status',
        'avatar',
        'apartment_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($user) {
            $user->residents()->delete();
        });
    }

    /**
     * Một user có thể là cư dân của nhiều căn hộ (lịch sử)
     */
    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    /**
     * Căn hộ hiện tại của cư dân
     */
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    // ── Role helpers ────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }

    /**
     * Kiểm tra user có thuộc nhóm admin portal hay không
     */
    public function isAdminPortalUser(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'staff', 'technician'], true);
    }

    /**
     * Các bài viết của user
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Các bình luận của user
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Các báo cáo bài viết mà user đã thực hiện
     */
    public function reports()
    {
        return $this->hasMany(PostReport::class);
    }

    /**
     * Các báo cáo bình luận mà user đã thực hiện
     */
    public function commentReports()
    {
        return $this->hasMany(CommentReport::class);
    }
}
