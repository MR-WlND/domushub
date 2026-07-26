<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BangLuong extends Model
{
    protected $table = 'bang_luong';

    protected $fillable = [
        'user_id',
        'thang',
        'nam',
        'luong_co_ban',
        'so_ngay_cong_chuan',
        'so_ngay_cong_thuc_te',
        'so_gio_ot',
        'tien_luong_theo_cong',
        'tien_ot',
        'tong_phu_cap',
        'tong_thuong',
        'tong_khau_tru',
        'thuc_linh',
        'trang_thai_duyet',
        'duyet_boi',
        'ngay_duyet',
        'recorded_by',
    ];

    protected $casts = [
        'luong_co_ban'          => 'decimal:2',
        'so_ngay_cong_chuan'   => 'decimal:1',
        'so_ngay_cong_thuc_te'  => 'decimal:1',
        'so_gio_ot'             => 'decimal:2',
        'tien_luong_theo_cong'  => 'decimal:2',
        'tien_ot'               => 'decimal:2',
        'tong_phu_cap'          => 'decimal:2',
        'tong_thuong'           => 'decimal:2',
        'tong_khau_tru'         => 'decimal:2',
        'thuc_linh'             => 'decimal:2',
        'ngay_duyet'            => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'duyet_boi');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function chiTietPhuCaps(): HasMany
    {
        return $this->hasMany(ChiTietPhuCap::class, 'bang_luong_id');
    }

    public function chiTietThuongs(): HasMany
    {
        return $this->hasMany(ChiTietThuong::class, 'bang_luong_id');
    }

    public function chiTietKhauTrus(): HasMany
    {
        return $this->hasMany(ChiTietKhauTru::class, 'bang_luong_id');
    }

    public function thanhToan(): HasOne
    {
        return $this->hasOne(ThanhToanLuong::class, 'bang_luong_id');
    }

    // ── Status Helpers ─────────────────────────────────────────────

    public function isApproved(): bool
    {
        return $this->trang_thai_duyet === 'da_duyet';
    }

    public function isPaid(): bool
    {
        return optional($this->thanhToan)->trang_thai === 'da_thanh_toan';
    }

    public function canRegenerate(): bool
    {
        return ! $this->isApproved() && ! $this->isPaid();
    }

    public function getTrangThaiDuyetLabelAttribute(): string
    {
        return match ($this->trang_thai_duyet) {
            'nhap'      => 'Bản nháp',
            'cho_duyet' => 'Chờ duyệt',
            'da_duyet'  => 'Đã duyệt',
            default     => $this->trang_thai_duyet,
        };
    }

    // ── Business Calculation Logic ─────────────────────────────────

    /**
     * 1. Tổng hợp ngày công thực tế và số giờ OT từ bảng attendance_records.
     */
    public function computeCongThucTe(): void
    {
        $attendanceRecords = AttendanceRecord::where('user_id', $this->user_id)
            ->whereYear('work_date', $this->nam)
            ->whereMonth('work_date', $this->thang)
            ->get();

        $congThucTe = 0.0;
        $gioOt      = 0.0;

        $gioChuanCaConfig = config('payroll.gio_chuan_ca', [
            'full_day'  => 8,
            'morning'   => 4,
            'afternoon' => 4,
        ]);

        foreach ($attendanceRecords as $rec) {
            // Tính điểm công
            if (in_array($rec->status, ['present', 'late'])) {
                $congThucTe += 1.0;
            } elseif ($rec->status === 'half_day') {
                $congThucTe += 0.5;
            }

            // Tính OT (nếu working_hours lớn hơn giờ chuẩn ca)
            if ($rec->working_hours > 0) {
                $standardHours = $gioChuanCaConfig[$rec->shift] ?? 8;
                if ($rec->working_hours > $standardHours) {
                    $gioOt += ($rec->working_hours - $standardHours);
                }
            }
        }

        $this->so_ngay_cong_thuc_te = round($congThucTe, 1);
        $this->so_gio_ot           = round($gioOt, 2);
    }

    /**
     * 2. Tính tiền lương theo công và tiền OT theo công thức.
     */
    public function computeTienLuong(): void
    {
        $soNgayChuan = $this->so_ngay_cong_chuan > 0 ? $this->so_ngay_cong_chuan : 26;
        $luongNgay   = $this->luong_co_ban / $soNgayChuan;

        // Tiền lương theo công
        $this->tien_luong_theo_cong = round($luongNgay * $this->so_ngay_cong_thuc_te, 2);

        // Tiền OT
        $gioChuanFullDay = config('payroll.gio_chuan_ca.full_day', 8);
        $luongGio        = $luongNgay / $gioChuanFullDay;
        $heSoOt          = config('payroll.he_so_ot.ngay_thuong', 1.5);

        $this->tien_ot   = round($this->so_gio_ot * $luongGio * $heSoOt, 2);
    }

    /**
     * 3. Sinh các khoản khấu trừ tự động (đi muộn / vắng không phép).
     */
    public function syncKhauTruTuDong(): void
    {
        // 1) Lấy hoặc tạo Danh mục khấu trừ "Đi muộn / Vắng mặt"
        $dmDiMuon = DanhMucKhauTru::firstOrCreate(
            ['ten_khau_tru' => 'Khấu trừ đi muộn'],
            ['loai' => 'tu_dong', 'is_active' => true]
        );

        $dmVang = DanhMucKhauTru::firstOrCreate(
            ['ten_khau_tru' => 'Khấu trừ vắng mặt'],
            ['loai' => 'tu_dong', 'is_active' => true]
        );

        // 2) Xóa chi tiết khấu trừ tự động cũ để tránh trùng lặp
        $autoDmIds = DanhMucKhauTru::where('loai', 'tu_dong')->pluck('id');
        ChiTietKhauTru::where('bang_luong_id', $this->id)
            ->whereIn('danh_muc_khau_tru_id', $autoDmIds)
            ->delete();

        // 3) Tính toán từ attendance_records
        $attendanceRecords = AttendanceRecord::where('user_id', $this->user_id)
            ->whereYear('work_date', $this->nam)
            ->whereMonth('work_date', $this->thang)
            ->get();

        $soNgayChuan    = $this->so_ngay_cong_chuan > 0 ? $this->so_ngay_cong_chuan : 26;
        $luongNgay      = $this->luong_co_ban / $soNgayChuan;
        $donGiaPhut     = config('payroll.don_gia_phut_di_muon', 5000);
        $maxKhauTruNgay = config('payroll.khau_tru_toi_da_theo_ngay', 1.0);

        foreach ($attendanceRecords as $rec) {
            // Đi muộn
            if ($rec->late_minutes > 0) {
                $tienPhatRaw = $rec->late_minutes * $donGiaPhut;
                $tienPhatCap = $luongNgay * $maxKhauTruNgay;
                $tienPhat    = min($tienPhatRaw, $tienPhatCap);

                ChiTietKhauTru::create([
                    'bang_luong_id'        => $this->id,
                    'danh_muc_khau_tru_id' => $dmDiMuon->id,
                    'so_tien'              => round($tienPhat, 2),
                    'ly_do'                => "Đi muộn {$rec->late_minutes} phút ngày " . $rec->work_date->format('d/m/Y'),
                ]);
            }

            // Vắng mặt (absent)
            if ($rec->status === 'absent') {
                ChiTietKhauTru::create([
                    'bang_luong_id'        => $this->id,
                    'danh_muc_khau_tru_id' => $dmVang->id,
                    'so_tien'              => round($luongNgay, 2),
                    'ly_do'                => "Vắng mặt ngày " . $rec->work_date->format('d/m/Y'),
                ]);
            }
        }
    }

    /**
     * 4. Tính toán tổng phụ cấp, tổng thưởng, tổng khấu trừ và Thực lĩnh.
     */
    public function recalculateTotals(): void
    {
        $this->tong_phu_cap  = (float) $this->chiTietPhuCaps()->sum('so_tien');
        $this->tong_thuong   = (float) $this->chiTietThuongs()->sum('so_tien');
        $this->tong_khau_tru = (float) $this->chiTietKhauTrus()->sum('so_tien');

        $this->thuc_linh = round(
            $this->tien_luong_theo_cong
            + $this->tien_ot
            + $this->tong_phu_cap
            + $this->tong_thuong
            - $this->tong_khau_tru,
            2
        );
    }
}
