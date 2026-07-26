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
        'base_salary',
        'banned_posting_until',

        'banned_commenting_until',
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
     * Bản ghi chấm công của nhân viên
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * Bảng lương của nhân viên
     */
    public function bangLuongs()
    {
        return $this->hasMany(BangLuong::class);
    }

    /**
     * Hợp đồng lao động của nhân viên
     */
    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    /**
     * Hợp đồng lao động hiện tại (đang hiệu lực hoặc mới nhất)
     */
    public function activeContract()
    {
        return $this->hasOne(EmployeeContract::class)
            ->whereIn('trang_thai', ['hieu_luc', 'sap_het_han'])
            ->latestOfMany('ngay_bat_dau');
    }


    /**
     * Căn hộ hiện tại của cư dân
     */
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    /**
     * Lấy danh sách apartment_id mà cư dân này thuộc về.
     * Fallback: nếu không có record trong bảng residents, dùng apartment_id trên user.
     */
    public function getApartmentIds(): array
    {
        $ids = $this->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        if (empty($ids) && $this->apartment_id) {
            $ids = [$this->apartment_id];
        }

        return $ids;
    }

    /**
     * Danh sách phản ánh kỹ thuật viên này phụ trách xử lý
     */
    public function handledTickets()
    {
        return $this->hasMany(Ticket::class, 'handler_id');
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

    public function isCleaning(): bool
    {
        return $this->role === 'cleaning';
    }

    /**
     * Scope lọc lấy danh sách nhân sự nội bộ (không bao gồm cư dân)
     */
    public function scopeStaff($query)
    {
        return $query->whereIn('role', ['admin', 'manager', 'staff', 'technician', 'security', 'cleaning']);
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

    /**
     * Kiểm tra user có bị khóa quyền đăng bài hay không
     */
    public function isBannedPosting(): bool
    {
        if (is_null($this->banned_posting_until)) {
            return false;
        }
        return \Carbon\Carbon::parse($this->banned_posting_until)->isFuture();
    }

    /**
     * Kiểm tra user có bị khóa quyền bình luận hay không
     */
    public function isBannedCommenting(): bool
    {
        if (is_null($this->banned_commenting_until)) {
            return false;
        }
        return \Carbon\Carbon::parse($this->banned_commenting_until)->isFuture();
    }
}
