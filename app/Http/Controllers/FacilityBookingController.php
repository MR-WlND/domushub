<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilityBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilityBookingController extends Controller
{
    /**
     * Xem khung giờ còn trống
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'booking_date' => 'required|date|after_or_equal:today',
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

        // Kiểm tra tổng số người đang đặt trong khung giờ đó có vượt sức chứa không
        $bookedPeople = FacilityBooking::where('facility_id', $facility->id)
            ->whereDate('booking_date', $request->booking_date)
            ->whereIn('status', ['approved', 'used'])
            ->where(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>', $request->start_time);
            })
            ->sum('number_of_people');

        $remaining = $facility->capacity - $bookedPeople;

        if ($remaining <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Khung giờ này đã đạt sức chứa tối đa (' . $facility->capacity . ' người). Vui lòng chọn khung giờ khác.',
            ], 422);
        }

        if ($request->number_of_people > $remaining) {
            return response()->json([
                'success' => false,
                'message' => 'Khung giờ này chỉ còn chỗ cho ' . $remaining . ' người nữa (sức chứa tối đa: ' . $facility->capacity . ' người).',
            ], 422);
        }

        $booking = FacilityBooking::create([
            'facility_id' => $facility->id,
            'user_id' => $user->id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'number_of_people' => $request->number_of_people,
            'status' => 'approved', // Auto-approve
        ]);

        $booking->generateQrCode();

        return response()->json([
            'success' => true,
            'message' => 'Đặt lịch thành công',
            'booking' => $this->formatBooking($booking),
        ], 201);
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
     */
    public function payBooking(Request $request, $bookingId)
    {
        $user = Auth::user();
        $booking = FacilityBooking::findOrFail($bookingId);

        if ($booking->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền',
            ], 403);
        }

        if ($booking->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ thanh toán cho lịch đã duyệt',
            ], 422);
        }

        $request->validate([
            'payment_method' => 'required|in:bank_transfer,cash,vnpay',
        ]);

        $paymentMethod = $request->payment_method;
        $paymentStatus = in_array($paymentMethod, ['bank_transfer', 'vnpay']) ? 'paid' : 'unpaid';

        $booking->update([
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentMethod,
        ]);

        // Tính tiền dựa trên giá tiền của tiện ích
        $amount = $this->calculateAmount($booking);

        return response()->json([
            'success' => true,
            'message' => $paymentStatus === 'paid' ? 'Thanh toán thành công' : 'Đã ghi nhận phương thức thanh toán tiền mặt',
            'payment' => [
                'amount' => $amount,
                'formatted_amount' => number_format($amount) . 'đ',
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'booking' => $this->formatBooking($booking),
            ],
        ], 201);
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
     * Tính tiền thanh toán (số slot × giá × số người)
     */
    private function calculateAmount($booking)
    {
        if (!$booking->facility || !$booking->facility->price_per_slot) {
            return 0;
        }

        $duration = $booking->facility->slot_duration;
        if ($duration == 0) {
            $slots = 1;
        } else {
            $startTime = strtotime($booking->start_time);
            $endTime   = strtotime($booking->end_time);
            $minutes   = ($endTime - $startTime) / 60;
            $slots     = ceil($minutes / $duration);
        }
        $people    = max(1, (int)($booking->number_of_people ?? 1));

        return intval($slots * $booking->facility->price_per_slot * $people);
    }
}
