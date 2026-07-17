<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\Activitylog\Models\Activity;
use App\Models\Visitor;
use App\Models\VehicleLog;
use App\Models\FacilityBooking;
use App\Models\Payment;
use Carbon\Carbon;

class ActivityLogsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tab;
    protected $filters;

    public function __construct(string $tab, array $filters = [])
    {
        $this->tab = $tab;
        $this->filters = $filters;
    }

    public function collection()
    {
        $f = $this->filters;

        switch ($this->tab) {
            case 'entry_exit':
                $q = Visitor::with(['apartment', 'registeredBy', 'checkedInBy', 'checkedOutBy']);
                if (!empty($f['search'])) {
                    $q->where(function($sq) use ($f) {
                        $sq->where('guest_name', 'LIKE', '%'.$f['search'].'%')
                           ->orWhere('guest_phone', 'LIKE', '%'.$f['search'].'%');
                    });
                }
                if (!empty($f['status'])) $q->where('status', $f['status']);
                if (!empty($f['date_from']) && !empty($f['date_to'])) {
                    $q->whereBetween('created_at', [$f['date_from'].' 00:00:00', $f['date_to'].' 23:59:59']);
                }
                return $q->latest()->get();

            case 'parking':
                $q = VehicleLog::with(['vehicle.apartment', 'checkedInBy', 'checkedOutBy']);
                if (!empty($f['search'])) {
                    $q->whereHas('vehicle', function($sq) use ($f) {
                        $sq->where('license_plate', 'LIKE', '%'.$f['search'].'%');
                    });
                }
                if (!empty($f['status'])) $q->where('status', $f['status']);
                if (!empty($f['date_from']) && !empty($f['date_to'])) {
                    $q->whereBetween('check_in_at', [$f['date_from'].' 00:00:00', $f['date_to'].' 23:59:59']);
                }
                return $q->latest('check_in_at')->get();

            case 'facility':
                $q = FacilityBooking::with(['facility', 'user']);
                if (!empty($f['search'])) {
                    $q->whereHas('user', function($sq) use ($f) { $sq->where('name', 'LIKE', '%'.$f['search'].'%'); })
                      ->orWhereHas('facility', function($sq) use ($f) { $sq->where('name', 'LIKE', '%'.$f['search'].'%'); });
                }
                if (!empty($f['status'])) $q->where('status', $f['status']);
                if (!empty($f['date_from']) && !empty($f['date_to'])) {
                    $q->whereBetween('booking_date', [$f['date_from'], $f['date_to']]);
                }
                return $q->latest()->get();

            case 'finance':
                $q = Payment::with(['invoice.apartment', 'recorder']);
                if (!empty($f['search'])) {
                    $q->where(function($sq) use ($f) {
                        $sq->where('receipt_code', 'LIKE', '%'.$f['search'].'%')
                           ->orWhere('payer_name', 'LIKE', '%'.$f['search'].'%')
                           ->orWhere('transaction_code', 'LIKE', '%'.$f['search'].'%');
                    });
                }
                if (!empty($f['method'])) $q->where('payment_method', $f['method']);
                if (!empty($f['status'])) $q->where('status', $f['status']);
                if (!empty($f['date_from']) && !empty($f['date_to'])) {
                    $q->whereBetween('paid_at', [$f['date_from'].' 00:00:00', $f['date_to'].' 23:59:59']);
                }
                return $q->latest('paid_at')->get();

            case 'utility':
                $q = Activity::with('causer')->where('log_name', 'utility');
                if (!empty($f['search'])) $q->where('description', 'LIKE', '%'.$f['search'].'%');
                if (!empty($f['type'])) $q->where('properties->type', $f['type']);
                if (!empty($f['action'])) $q->where('properties->action', $f['action']);
                if (!empty($f['month'])) $q->where('properties->record_month', (int)$f['month']);
                if (!empty($f['year'])) $q->where('properties->record_year', (int)$f['year']);
                
                $blockId = $f['block_id'] ?? null;
                $floorId = $f['floor_id'] ?? null;
                if ($floorId) {
                    $apartmentIds = \App\Models\Apartment::where('floor_id', $floorId)->pluck('id')->toArray();
                    $q->whereIn('properties->apartment_id', $apartmentIds);
                } elseif ($blockId) {
                    $floorIds = \App\Models\Floor::where('block_id', $blockId)->pluck('id')->toArray();
                    $apartmentIds = \App\Models\Apartment::whereIn('floor_id', $floorIds)->pluck('id')->toArray();
                    $q->whereIn('properties->apartment_id', $apartmentIds);
                }

                if (!empty($f['date_from']) && !empty($f['date_to'])) {
                    $q->whereBetween('created_at', [$f['date_from'].' 00:00:00', $f['date_to'].' 23:59:59']);
                }
                
                $logs = $q->latest()->get();
                $apartmentMap = [];
                $allAptIds = $logs->pluck('properties.apartment_id')->filter()->unique();
                if ($allAptIds->isNotEmpty()) {
                    $apartmentMap = \App\Models\Apartment::whereIn('id', $allAptIds)->get()->keyBy('id');
                }
                foreach ($logs as $log) {
                    $aptId = $log->properties['apartment_id'] ?? null;
                    if ($aptId && isset($apartmentMap[$aptId])) {
                        $log->apartment_number = $apartmentMap[$aptId]->apartment_number;
                    } else {
                        $log->apartment_number = '';
                    }
                }
                return $logs;

            case 'system':
            case 'hardware':
            case 'communication':
                $logNames = match($this->tab) {
                    'system' => ['default', null],
                    'hardware' => ['hardware', 'qr', 'scanner'],
                    'communication' => ['notification', 'announcement', 'resident'],
                };
                $q = Activity::with('causer')->where(function($sq) use ($logNames) {
                    if (in_array(null, $logNames, true)) {
                        $sq->whereIn('log_name', ['default'])->orWhereNull('log_name');
                    } else {
                        $sq->whereIn('log_name', $logNames);
                    }
                });
                if (!empty($f['search'])) $q->where('description', 'LIKE', '%'.$f['search'].'%');
                if (!empty($f['causer_id'])) $q->where('causer_id', $f['causer_id']);
                if (!empty($f['date_from']) && !empty($f['date_to'])) {
                    $q->whereBetween('created_at', [$f['date_from'].' 00:00:00', $f['date_to'].' 23:59:59']);
                }
                return $q->latest()->get();

            default:
                return collect([]);
        }
    }

    public function headings(): array
    {
        return match ($this->tab) {
            'entry_exit' => ['Thời gian đăng ký', 'Tên khách', 'SĐT', 'Căn hộ', 'Người đăng ký', 'Check-in', 'Check-out', 'Trạng thái'],
            'parking' => ['Thời gian vào', 'Biển số', 'Loại xe', 'Căn hộ', 'Check-in bởi', 'Thời gian ra', 'Check-out bởi', 'Trạng thái'],
            'facility' => ['Ngày đặt', 'Cư dân', 'Tiện ích', 'Giờ bắt đầu', 'Giờ kết thúc', 'Số người', 'Thanh toán', 'Trạng thái'],
            'finance' => ['Thời gian TT', 'Mã biên lai', 'Mã GD', 'Người nộp', 'Căn hộ', 'Số tiền', 'Phương thức', 'Ghi nhận bởi', 'Trạng thái'],
            'utility' => ['Thời gian', 'Căn hộ', 'Kỳ (T/N)', 'Loại', 'Chỉ số cũ', 'Chỉ số mới', 'Tiêu thụ', 'Người thực hiện', 'Hành động'],
            default => ['Thời gian', 'Người thực hiện', 'Phân hệ', 'Mô tả', 'Dữ liệu thay đổi'],
        };
    }

    public function map($row): array
    {
        switch ($this->tab) {
            case 'entry_exit':
                return [
                    $row->created_at->format('d/m/Y H:i'),
                    $row->guest_name,
                    $row->guest_phone,
                    $row->apartment->apartment_number ?? '',
                    $row->registeredBy->name ?? '',
                    $row->check_in_at ? $row->check_in_at->format('d/m/Y H:i') : '',
                    $row->check_out_at ? $row->check_out_at->format('d/m/Y H:i') : '',
                    $this->translateStatus($row->status),
                ];
            case 'parking':
                return [
                    $row->check_in_at ? $row->check_in_at->format('d/m/Y H:i') : '',
                    $row->vehicle->license_plate ?? '',
                    $this->translateVehicleType($row->vehicle->type ?? ''),
                    $row->vehicle->apartment->apartment_number ?? '',
                    $row->checkedInBy->name ?? '',
                    $row->check_out_at ? $row->check_out_at->format('d/m/Y H:i') : '',
                    $row->checkedOutBy->name ?? '',
                    $row->status === 'inside' ? 'Trong bãi' : 'Đã ra',
                ];
            case 'facility':
                return [
                    Carbon::parse($row->booking_date)->format('d/m/Y'),
                    $row->user->name ?? '',
                    $row->facility->name ?? '',
                    $row->start_time,
                    $row->end_time,
                    $row->number_of_people,
                    $row->payment_status === 'paid' ? 'Đã TT' : 'Chưa TT',
                    $this->translateStatus($row->status),
                ];
            case 'finance':
                return [
                    $row->paid_at ? $row->paid_at->format('d/m/Y H:i') : '',
                    $row->receipt_code,
                    $row->transaction_code,
                    $row->payer_name,
                    $row->invoice->apartment->apartment_number ?? '',
                    $row->amount,
                    $this->translatePaymentMethod($row->payment_method),
                    $row->recorder->name ?? '',
                    $this->translatePaymentStatus($row->status),
                ];
            case 'utility':
                $props = $row->properties;
                $oldVal = $props['old_value'] ?? 0;
                $newVal = $props['new_value'] ?? 0;
                $usage = max(0, $newVal - $oldVal);
                return [
                    $row->created_at->format('d/m/Y H:i'),
                    $row->apartment_number ?? '',
                    str_pad($props['record_month'] ?? 0, 2, '0', STR_PAD_LEFT) . '/' . ($props['record_year'] ?? ''),
                    ($props['type'] ?? '') === 'electricity' ? 'Điện' : 'Nước',
                    $oldVal,
                    $newVal,
                    $usage,
                    $row->causer->name ?? '—',
                    $this->translateUtilityAction($props['action'] ?? ''),
                ];
            default:
                return [
                    $row->created_at->format('d/m/Y H:i'),
                    $row->causer->name ?? 'Hệ thống',
                    $row->log_name ?? 'default',
                    $row->description,
                    $row->properties ? json_encode($row->properties, JSON_UNESCAPED_UNICODE) : '',
                ];
        }
    }

    private function translateStatus($status) {
        return match($status) {
            'pending' => 'Chờ',
            'checked_in' => 'Đã vào',
            'checked_out' => 'Đã ra',
            'expired' => 'Hết hạn',
            'cancelled' => 'Đã hủy',
            'approved' => 'Đã duyệt',
            'used' => 'Đã dùng',
            'rejected' => 'Từ chối',
            'completed' => 'Hoàn thành',
            default => $status,
        };
    }
    private function translateVehicleType($type) {
        return match($type) {
            'car' => 'Ô tô',
            'motorbike' => 'Xe máy',
            'electric_bike' => 'Xe điện',
            default => $type,
        };
    }
    private function translatePaymentMethod($method) {
        return match($method) {
            'cash' => 'Tiền mặt',
            'bank_transfer' => 'Chuyển khoản',
            'vnpay' => 'VNPay',
            default => $method,
        };
    }
    private function translatePaymentStatus($status) {
        return match($status) {
            'paid' => 'Thành công',
            'pending' => 'Chờ xác nhận',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn',
            default => $status,
        };
    }
    private function translateUtilityAction($action) {
        return match($action) {
            'recorded' => 'Ghi số',
            'updated' => 'Cập nhật',
            'approved' => 'Chốt số',
            'rejected' => 'Từ chối',
            default => $action,
        };
    }
}
