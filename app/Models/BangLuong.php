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
        'so_gio_ot_thuong',      // OT ngày thường (×1.5)
        'so_gio_ot_cuoi_tuan',   // OT thứ 7/CN (×2.0)
        'so_gio_ot_ngay_le',     // OT ngày lễ (×3.0)
        'canh_bao_ot',           // true nếu vượt trần 40h/tháng
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
        'so_gio_ot_thuong'      => 'decimal:2',
        'so_gio_ot_cuoi_tuan'   => 'decimal:2',
        'so_gio_ot_ngay_le'     => 'decimal:2',
        'canh_bao_ot'           => 'boolean',
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
     * 1. Tổng hợp ngày công thực tế và phân loại giờ OT theo luật VN:
     *    - OT ngày thường (Mon–Fri, không phải lễ) → hệ số 1.5
     *    - OT cuối tuần (Sat, Sun)                 → hệ số 2.0
     *    - OT ngày lễ quốc gia                     → hệ số 3.0
     *
     * Đồng thời kiểm tra cảnh báo nếu tổng OT vượt trần 40h/tháng.
     */
    public function computeCongThucTe(): void
    {
        $attendanceRecords = AttendanceRecord::where('user_id', $this->user_id)
            ->whereYear('work_date', $this->nam)
            ->whereMonth('work_date', $this->thang)
            ->get();

        $congThucTe   = 0.0;
        $gioOtThuong  = 0.0;
        $gioOtCuoiTuan = 0.0;
        $gioOtNgayLe  = 0.0;

        // Danh sách ngày lễ (format: d-m)
        $ngayLe = config('attendance.ngay_le_vn', []);

        $gioChuanCaConfig = array_merge(
            ['full_day' => 8, 'morning' => 4, 'afternoon' => 4, 'night' => 8, 'office' => 8],
            config('payroll.gio_chuan_ca', [])
        );

        foreach ($attendanceRecords as $rec) {
            // ── Tính điểm công ──
            if (in_array($rec->status, ['present', 'late'])) {
                $congThucTe += 1.0;
            } elseif ($rec->status === 'half_day') {
                $congThucTe += 0.5;
            }

            // ── Tính OT phân loại ──
            if ($rec->working_hours > 0) {
                $standardHours = $gioChuanCaConfig[$rec->shift] ?? 8;
                $gioOtCa = max(0, (float)$rec->working_hours - $standardHours);

                if ($gioOtCa > 0) {
                    $workDate  = $rec->work_date; // Carbon instance
                    $dayMonStr = $workDate->format('d-m');

                    if (in_array($dayMonStr, $ngayLe)) {
                        $gioOtNgayLe  += $gioOtCa;   // Ngày lễ × 3.0
                    } elseif ($workDate->isWeekend()) {
                        $gioOtCuoiTuan += $gioOtCa;  // T7/CN × 2.0
                    } else {
                        $gioOtThuong  += $gioOtCa;   // Ngày thường × 1.5
                    }
                }
            }
        }

        $tongOt = $gioOtThuong + $gioOtCuoiTuan + $gioOtNgayLe;
        $tranOt = config('attendance.ot_tran_thang', 40);

        $this->so_ngay_cong_thuc_te = round($congThucTe, 1);
        $this->so_gio_ot            = round($tongOt, 2);
        $this->so_gio_ot_thuong     = round($gioOtThuong, 2);
        $this->so_gio_ot_cuoi_tuan  = round($gioOtCuoiTuan, 2);
        $this->so_gio_ot_ngay_le    = round($gioOtNgayLe, 2);
        $this->canh_bao_ot          = $tongOt > $tranOt;
    }

    /**
     * 2. Tính tiền lương theo công và tiền OT (phân loại ngày thường/cuối tuần/lễ).
     *
     * Công thức OT theo Bộ luật Lao động 2019:
     *  - Ngày thường  : lương giờ × 1.5
     *  - Cuối tuần    : lương giờ × 2.0
     *  - Ngày lễ      : lương giờ × 3.0
     */
    public function computeTienLuong(): void
    {
        $soNgayChuan = $this->so_ngay_cong_chuan > 0 ? $this->so_ngay_cong_chuan : 26;
        $luongNgay   = $this->luong_co_ban / $soNgayChuan;

        // Tiền lương theo công thực tế
        $this->tien_luong_theo_cong = round($luongNgay * $this->so_ngay_cong_thuc_te, 2);

        // Lương giờ chuẩn (dùng làm base cho OT)
        $gioChuanFullDay = config('payroll.gio_chuan_ca.full_day', 8);
        $luongGio        = $luongNgay / $gioChuanFullDay;

        // Hệ số OT từ config
        $heSo = [
            'ngay_thuong' => config('payroll.he_so_ot.ngay_thuong', 1.5),
            'cuoi_tuan'   => config('payroll.he_so_ot.cuoi_tuan',   2.0),
            'ngay_le'     => config('payroll.he_so_ot.ngay_le',     3.0),
        ];

        // Tính tiền OT từng loại và cộng tổng
        $tienOtThuong   = ($this->so_gio_ot_thuong   ?? 0) * $luongGio * $heSo['ngay_thuong'];
        $tienOtCuoiTuan = ($this->so_gio_ot_cuoi_tuan ?? 0) * $luongGio * $heSo['cuoi_tuan'];
        $tienOtNgayLe   = ($this->so_gio_ot_ngay_le  ?? 0) * $luongGio * $heSo['ngay_le'];

        $this->tien_ot = round($tienOtThuong + $tienOtCuoiTuan + $tienOtNgayLe, 2);
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
