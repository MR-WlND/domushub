<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    use HasFactory;

    protected $table = 'employee_contracts';

    protected $fillable = [
        'user_id',
        'ma_hop_dong',
        'loai_hop_dong',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'luong_co_ban',
        'trang_thai',
        'ghi_chu',
        'created_by',
    ];

    protected $casts = [
        'ngay_bat_dau'  => 'date',
        'ngay_ket_thuc' => 'date',
        'luong_co_ban'  => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Tên loại hợp đồng hiển thị tiếng Việt
     */
    public function getLoaiHopDongLabelAttribute(): string
    {
        $labels = [
            'thu_viec'          => 'Thử việc (2 tháng)',
            'xac_dinh_thoi_han'  => 'Hợp đồng xác định thời hạn',
            'khong_thoi_han'    => 'Hợp đồng không xác định thời hạn',
            'vendor_thue_ngoai' => 'Hợp đồng Vendor / Thuê ngoài',
            'thoi_vu'           => 'Hợp đồng Thời vụ / Dự án',
        ];

        return $labels[$this->loai_hop_dong] ?? $this->loai_hop_dong;
    }

    /**
     * Tên trạng thái hiển thị
     */
    public function getTrangThaiLabelAttribute(): string
    {
        $labels = [
            'hieu_luc'    => 'Đang hiệu lực',
            'sap_het_han' => 'Sắp hết hạn',
            'het_han'     => 'Đã hết hạn',
            'thanh_ly'    => 'Đã thanh lý',
        ];

        return $labels[$this->trang_thai] ?? $this->trang_thai;
    }

    /**
     * Scope lấy danh sách hợp đồng sắp hết hạn trong N ngày (mặc định 30 ngày)
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('ngay_ket_thuc')
            ->where('trang_thai', '!=', 'thanh_ly')
            ->whereBetween('ngay_ket_thuc', [today(), today()->addDays($days)]);
    }

    /**
     * Kiểm tra hợp đồng có sắp hết hạn không
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        if (!$this->ngay_ket_thuc || $this->trang_thai === 'thanh_ly') {
            return false;
        }

        return $this->ngay_ket_thuc->isFuture() && $this->ngay_ket_thuc->diffInDays(today()) <= $days;
    }

    /**
     * Tự động cập nhật trạng thái hợp đồng theo ngày
     */
    public function updateCalculatedStatus(): void
    {
        if ($this->trang_thai === 'thanh_ly') {
            return;
        }

        if ($this->ngay_ket_thuc) {
            if ($this->ngay_ket_thuc->isPast()) {
                $this->trang_thai = 'het_han';
            } elseif ($this->isExpiringSoon(30)) {
                $this->trang_thai = 'sap_het_han';
            } else {
                $this->trang_thai = 'hieu_luc';
            }
        } else {
            $this->trang_thai = 'hieu_luc';
        }
    }
}
