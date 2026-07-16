<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Invoice extends Model
{
    use SoftDeletes;

    protected $table = 'bills';

    protected $fillable = [
        'apartment_id',
        'title',
        'billing_month',
        'billing_year',
        'due_date',
        'total_amount',
        'paid_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, 'bill_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'bill_id');
    }

    /**
     * Lịch đặt tiện ích liên kết với hóa đơn này (nếu có)
     */
    public function facilityBooking()
    {
        return $this->hasOne(\App\Models\FacilityBooking::class, 'bill_id');
    }

    // ─── RELATIONSHIP ALIASES & DUMMIES ───

    public function items()
    {
        return $this->details();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault([
            'name' => 'Hệ thống'
        ]);
    }

    // ─── ACCESSORS ───

    public function getInvoiceCodeAttribute(): string
    {
        return 'BILL-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getTypeAttribute(): string
    {
        $firstDetail = $this->details->first();
        if ($firstDetail && $firstDetail->servicePrice) {
            return $firstDetail->servicePrice->type;
        }
        return 'other';
    }

    public function getAmountAttribute(): float
    {
        return (float) $this->total_amount;
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->paid_amount);
    }

    public function getPaidPercentAttribute(): int
    {
        if ($this->total_amount <= 0) return 0;
        return (int) min(100, round($this->paid_amount / $this->total_amount * 100));
    }

    public function getBillingMonthAttribute($value)
    {
        return Carbon::createFromDate($this->billing_year, (int) $value, 1);
    }

    // ─── LABELS ───

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'paid'    => 'Đã thanh toán',
            'partial_paid' => 'Thanh toán một phần',
            'unpaid'  => 'Chưa thanh toán',
            'overdue' => 'Quá hạn',
            'cancelled' => 'Đã hủy',
            default   => $status,
        };
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'electricity' => 'Tiền điện',
            'water' => 'Tiền nước',
            'parking' => 'Phí gửi xe',
            'management_fee' => 'Phí quản lý',
            'internet' => 'Internet',
            'service' => 'Dịch vụ',
            default => 'Khác',
        };
    }

    /**
     * Tự động tính toán lại trạng thái của các dòng chi tiết hóa đơn (InvoiceDetail)
     * dựa trên tổng số tiền đã thanh toán (paid_amount).
     */
    public function recalculateDetailsStatus()
    {
        $paidAmount = (float) $this->paid_amount;
        $details = $this->details()->orderBy('id')->get();
        
        // Bước 1: Ưu tiên các chi tiết đã được gán cứng vào một payment_id
        foreach ($details as $detail) {
            if ($detail->payment_id) {
                if ($detail->status !== 'paid') {
                    $detail->update(['status' => 'paid']);
                }
                $paidAmount -= (float) $detail->amount;
            }
        }

        // Bước 2: Phân bổ số tiền còn lại cho các chi tiết chưa gán payment_id (waterfall)
        foreach ($details as $detail) {
            if ($detail->payment_id) {
                continue; // Đã xử lý ở bước 1
            }
            if ($paidAmount >= (float) $detail->amount - 0.001) { // trừ hao sai số float
                if ($detail->status !== 'paid') {
                    $detail->update(['status' => 'paid']);
                }
                $paidAmount -= (float) $detail->amount;
            } else {
                if ($detail->status !== 'unpaid') {
                    $detail->update(['status' => 'unpaid']);
                }
            }
        }
    }
}
