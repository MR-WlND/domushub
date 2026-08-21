<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\ServicePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FacilityBookingController extends Controller
{
    /**
     * Xem khung giờ còn trống
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'facility_id'  => 'required|exists:facilities,id',
            'booking_date' => 'required|date|after_or_equal:today',
        ], [
            'facility_id.required'         => 'Vui lòng chọn tiện ích.',
            'facility_id.exists'           => 'Tiện ích không tồn tại.',
            'booking_date.required'        => 'Vui lòng chọn ngày đặt.',
            'booking_date.date'            => 'Ngày đặt không đúng định dạng.',
            'booking_date.after_or_equal'  => 'Ngày đặt không được nhỏ hơn ngày hiện tại.',
        ]);

        $facility = Facility::findOrFail($request->facility_id);
        $bookingDate = $request->booking_date;

        // Lấy tất cả time slots
        $allSlots = $facility->getTimeSlots();

        // Lấy các booking đã duyệt hoặc đã sử dụng
        $bookedSlots = FacilityBooking::where('facility_id', $facility->id)
            ->whereDate('booking_date', $bookingDate)
            ->whereIn('status', ['approved', 'used'])
            ->get(['start_time', 'end_time', 'number_of_people'])
            ->map(function ($b) {
                return [
                    'start_time' => substr($b->start_time, 0, 5),
                    'end_time'   => substr($b->end_time, 0, 5),
                    'number_of_people' => (int)($b->number_of_people ?? 1),
                ];
            })
            ->toArray();

        // Lọc khung giờ trống dựa trên sức chứa còn lại của mỗi slot
        $availableSlots = [];
        foreach ($allSlots as $slot) {
            $bookedPeople = 0;
            foreach ($bookedSlots as $booking) {
                if ($booking['start_time'] < $slot['end'] && $booking['end_time'] > $slot['start']) {
                    $bookedPeople += $booking['number_of_people'];
                }
            }
            $remaining = $facility->capacity - $bookedPeople;
            if ($remaining > 0) {
                $slot['remaining_capacity'] = $remaining;
                $availableSlots[] = $slot;
            }
        }

        return response()->json([
            'success' => true,
            'facility' => [
                'id' => $facility->id,
                'name' => $facility->name,
                'operating_hours' => $facility->operating_hours,
                'price_label' => $facility->price_label,
                'capacity' => $facility->capacity,
            ],
            'booking_date' => $bookingDate,
            'available_slots' => array_values($availableSlots),
        ]);
    }

    /**
     * Đặt lịch sử dụng
     */
    public function bookSlot(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'number_of_people' => 'required|integer|min:1',
        ]);

        $facility = Facility::findOrFail($request->facility_id);

        // Kiểm tra trạng thái bảo trì
        if ($facility->status === 'maintenance') {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Tiện ích "' . $facility->name . '" đang bảo trì, không thể đặt lịch.',
            ], 422);
        }

        if ($facility->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Tiện ích này hiện đã đóng cửa, không thể đặt lịch.',
            ], 422);
        }

        // Kiểm tra cư dân chưa có lịch trùng khung giờ (chính họ)
        $ownConflict = FacilityBooking::where('user_id', $user->id)
            ->where('facility_id', $facility->id)
            ->whereDate('booking_date', $request->booking_date)
            ->whereIn('status', ['approved', 'used'])
            ->where(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($ownConflict) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã có lịch đặt tiện ích này trong khung giờ này rồi.',
            ], 422);
        }

        // 1. Lấy danh sách booking đã duyệt hoặc sử dụng trong ngày
        $bookedSlots = FacilityBooking::where('facility_id', $facility->id)
            ->whereDate('booking_date', $request->booking_date)
            ->whereIn('status', ['approved', 'used'])
            ->get(['start_time', 'end_time', 'number_of_people'])
            ->map(function ($b) {
                return [
                    'start_time' => substr($b->start_time, 0, 5),
                    'end_time'   => substr($b->end_time, 0, 5),
                    'number_of_people' => (int)($b->number_of_people ?? 1),
                ];
            })
            ->toArray();

        // 2. Lấy tất cả slots của tiện ích
        $allSlots = $facility->getTimeSlots();

        if ($facility->slot_duration > 0 && !empty($allSlots)) {
            $requestedStart = substr($request->start_time, 0, 5);
            $requestedEnd = substr($request->end_time, 0, 5);
            $overlapSlotsCount = 0;

            foreach ($allSlots as $slot) {
                if ($slot['start'] < $requestedEnd && $slot['end'] > $requestedStart) {
                    $overlapSlotsCount++;

                    // Tính tổng số người đã đặt cho riêng slot này
                    $slotBookedPeople = 0;
                    foreach ($bookedSlots as $booking) {
                        if ($booking['start_time'] < $slot['end'] && $booking['end_time'] > $slot['start']) {
                            $slotBookedPeople += $booking['number_of_people'];
                        }
                    }

                    $remaining = $facility->capacity - $slotBookedPeople;
                    if ($remaining <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Khung giờ ' . $slot['label'] . ' đã đạt sức chứa tối đa (' . $facility->capacity . ' người).',
                        ], 422);
                    }
                    if ($request->number_of_people > $remaining) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Khung giờ ' . $slot['label'] . ' chỉ còn chỗ cho ' . $remaining . ' người.',
                        ], 422);
                    }
                }
            }

            if ($overlapSlotsCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khung giờ chọn không khớp với bất kỳ slot hoạt động nào.',
                ], 422);
            }
        } else {
            // Trường hợp slot_duration = 0 (cả ngày hoặc không chia slot)
            $bookedPeople = 0;
            foreach ($bookedSlots as $booking) {
                if ($booking['start_time'] < $request->end_time && $booking['end_time'] > $request->start_time) {
                    $bookedPeople += $booking['number_of_people'];
                }
            }
            $remaining = $facility->capacity - $bookedPeople;
            if ($remaining <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiện ích đã đạt sức chứa tối đa trong khung giờ này.',
                ], 422);
            }
            if ($request->number_of_people > $remaining) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiện ích chỉ còn chỗ cho ' . $remaining . ' người nữa.',
                ], 422);
            }
        }

        $booking = FacilityBooking::create([
            'facility_id' => $facility->id,
            'user_id' => $user->id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'number_of_people' => $request->number_of_people,
            'status' => 'approved', // Auto-approve
            'payment_status' => 'unpaid',
        ]);

        $booking->generateQrCode();

        // Nếu có phí sử dụng, tự động tạo hóa đơn
        if ($booking->amount > 0) {
            $this->createInvoiceForBooking($booking, $facility, $user);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đặt lịch thành công' . ($booking->amount > 0 ? '. Vui lòng thanh toán hóa đơn để nhận mã QR check-in.' : ''),
            'booking' => $this->formatBooking($booking),
            'requires_payment' => $booking->amount > 0,
        ], 201);
    }

    /**
     * Tạo hóa đơn cho lịch đặt tiện ích có phí
     */
    private function createInvoiceForBooking(FacilityBooking $booking, Facility $facility, $user): void
    {
        try {
            // Lấy căn hộ từ bảng residents (giống InvoiceController::index)
            $resident = $user->residents()
                ->whereNull('deleted_at')
                ->latest()
                ->first();

            // Fallback: thử cột apartment_id trực tiếp trên users
            $apartmentId = $resident?->apartment_id ?? $user->apartment_id;

            if (!$apartmentId) {
                logger()->warning('API Booking #' . $booking->id . ': Cư dân không có căn hộ, bỏ qua tạo hóa đơn.');
                return;
            }

            $servicePrice = ServicePrice::where('status', 'active')->first();

            $amount = $booking->amount;
            $title  = 'Phí sử dụng tiện ích: ' . $facility->name
                . ' (' . \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y')
                . ', ' . substr($booking->start_time, 0, 5) . '–' . substr($booking->end_time, 0, 5) . ')';

            DB::transaction(function () use ($booking, $apartmentId, $amount, $title, $servicePrice) {
                $inv = Invoice::create([
                    'apartment_id'  => $apartmentId,
                    'title'         => $title,
                    'billing_month' => now()->month,
                    'billing_year'  => now()->year,
                    'total_amount'  => $amount,
                    'paid_amount'   => 0,
                    'status'        => 'unpaid',
                    'due_date'      => now()->addDays(7),
                ]);

                if ($servicePrice) {
                    InvoiceDetail::create([
                        'bill_id'          => $inv->id,
                        'service_price_id' => $servicePrice->id,
                        'quantity'         => 1,
                        'amount'           => $amount,
                        'status'           => 'unpaid',
                    ]);
                }

                $booking->update(['bill_id' => $inv->id]);
            });
        } catch (\Throwable $e) {
            logger()->error('API: Lỗi tạo hóa đơn cho booking #' . $booking->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Xem lịch sử đặt chỗ
     */
    public function getHistory(Request $request)
    {
        $user = Auth::user();

        $query = FacilityBooking::where('user_id', $user->id)
            ->with('facility')
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $bookings->map(fn ($b) => $this->formatBooking($b)),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'total' => $bookings->total(),
                'last_page' => $bookings->lastPage(),
            ],
        ]);
    }

    /**
     * Hủy lịch
     */
    public function cancelBooking($bookingId)
    {
        $user = Auth::user();
        $booking = FacilityBooking::findOrFail($bookingId);

        if ($booking->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền',
            ], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Lịch đã bị hủy',
            ], 422);
        }

        if ($booking->status === 'used') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy lịch đã sử dụng',
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        if ($booking->bill_id) {
            $invoice = Invoice::find($booking->bill_id);
            if ($invoice && !in_array($invoice->status, ['paid', 'cancelled'])) {
                $invoice->update(['status' => 'cancelled']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Hủy lịch thành công',
        ]);
    }

    /**
     * Check-in bằng QR code
     */
    public function checkIn(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        $booking = FacilityBooking::where('qr_code', $request->qr_code)->firstOrFail();

        if ($booking->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Lịch này không ở trạng thái được duyệt',
            ], 422);
        }

        if ($booking->booking_date != now()->toDateString()) {
            return response()->json([
                'success' => false,
                'message' => 'Lịch này không phải hôm nay',
            ], 422);
        }

        if ($booking->checkIn()) {
            return response()->json([
                'success' => true,
                'message' => 'Check-in thành công',
                'booking' => $this->formatBooking($booking),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Check-in thất bại',
        ], 400);
    }

    /**
     * Thanh toán phí sử dụng
     * @deprecated Việc thanh toán tiện ích giờ được xử lý qua hệ thống hóa đơn.
     */
    public function payBooking(Request $request, $bookingId)
    {
        return response()->json([
            'success' => false,
            'message' => 'Thanh toán tiện ích giờ được thực hiện qua trang Hóa đơn cư dân. Vui lòng truy cập /resident/invoices để thanh toán.',
        ], 410);
    }

    /**
     * Format booking response
     */
    private function formatBooking($booking)
    {
        $amount = $this->calculateAmount($booking);
        $needsPayment = $amount > 0 && ($booking->payment_status !== 'paid' || !in_array($booking->payment_method, ['bank_transfer', 'vnpay']));

        return [
            'id' => $booking->id,
            'facility_name' => $booking->facility->name,
            'booking_date' => $booking->booking_date->format('Y-m-d'),
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'number_of_people' => $booking->number_of_people,
            'status' => $booking->status,
            'status_label' => $booking->status_label,
            'qr_code' => $needsPayment ? null : $booking->qr_code,
            'checked_in_at' => $booking->checked_in_at,
            'amount' => $amount,
        ];
    }

    /**
     * Tính tiền thanh toán
     */
    private function calculateAmount($booking)
    {
        if (!$booking->facility || $booking->facility->fee_type === 'free' || !$booking->facility->price) {
            return 0;
        }

        $price = (float)$booking->facility->price;
        $feeType = $booking->facility->fee_type;
        $people = max(1, (int)($booking->number_of_people ?? 1));

        if ($feeType === 'per_person') {
            return (int)($price * $people);
        }

        if ($feeType === 'per_use') {
            return (int)$price;
        }

        if ($feeType === 'per_hour') {
            $startTime = strtotime($booking->start_time);
            $endTime   = strtotime($booking->end_time);
            $minutes   = ($endTime - $startTime) / 60;
            $hours     = ceil($minutes / 60); // Tính theo block 1 giờ hoặc có thể chia theo số phút chính xác
            // Ở đây tính theo block giờ (1h, 2h...)
            return (int)($hours * $price);
        }

        // Fallback for old system or missing fee_type
        return 0;
    }
}
