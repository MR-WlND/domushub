<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'registered_by',
        'guest_name',
        'guest_phone',
        'qr_token',
        'expired_at',
        'check_in_at',
        'check_out_at',
        'check_in_by',
        'check_out_by',
        'status',
        'note',
        'vehicle_plate',
        'vehicle_type',
        'walk_in',
        'resident_to_meet',
        'confirmed_by_resident',
        'face_image',
    ];

    protected $casts = [
        'expired_at'   => 'datetime',
        'check_in_at'  => 'datetime',
        'check_out_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'check_in_by');
    }

    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'check_out_by');
    }

    public function confirmedByResident()
    {
        return $this->belongsTo(User::class, 'confirmed_by_resident');
    }

    // =========================================================================
    // STATUS HELPERS
    // =========================================================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCheckedIn(): bool
    {
        return $this->status === 'checked_in';
    }

    public function isCheckedOut(): bool
    {
        return $this->status === 'checked_out';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->isPending() && $this->expired_at->isPast());
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isValid(): bool
    {
        return $this->status === 'pending' && $this->expired_at->isFuture();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'     => 'Chờ vào',
            'checked_in'  => 'Đã vào',
            'checked_out' => 'Đã ra',
            'expired'     => 'Hết hạn',
            'cancelled'   => 'Đã hủy',
            default       => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'     => 'blue',
            'checked_in'  => 'green',
            'checked_out' => 'gray',
            'expired'     => 'orange',
            'cancelled'   => 'red',
            default       => 'gray',
        };
    }

    // =========================================================================
    // VEHICLE HELPERS
    // =========================================================================

    public function hasVehicle(): bool
    {
        return !empty($this->vehicle_plate);
    }

    public function vehicleTypeLabel(): string
    {
        return match ($this->vehicle_type) {
            'car'           => 'Ô tô',
            'motorbike'     => 'Xe máy',
            'electric_bike' => 'Xe điện',
            default         => 'Phương tiện',
        };
    }

    // =========================================================================
    // STATIC FACTORY
    // =========================================================================

    /**
     * Tạo qr_token unique (UUID-based, 64 chars)
     */
    public static function generateToken(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Đường dẫn file QR image (lưu theo token)
     */
    public function qrImagePath(): string
    {
        return 'qr/visitors/' . $this->qr_token . '.svg';
    }

    public function qrImageUrl(): string
    {
        $path = storage_path('app/public/' . $this->qrImagePath());
        if (file_exists($path)) {
            return asset('storage/' . $this->qrImagePath());
        }
        return '';
    }
}
