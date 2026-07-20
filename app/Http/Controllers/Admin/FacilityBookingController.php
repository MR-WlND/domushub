<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SystemLogger;
use App\Models\FacilityBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilityBookingController extends Controller
{
    /**
     * Xem danh sách đặt lịch
     */
    public function index(Request $request)
    {
        $query = FacilityBooking::with('facility', 'user')
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->facility_id) {
            $query->where('facility_id', $request->facility_id);
        }

        $bookings = $query->paginate(20);

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
     * Duyệt lịch đặt
     */
    public function approve($bookingId)
    {
        $booking = FacilityBooking::with('facility', 'user')->findOrFail($bookingId);

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ duyệt lịch ở trạng thái chờ duyệt',
            ], 422);
        }

        $booking->update(['status' => 'approved']);



        return response()->json([
            'success' => true,
            'message' => 'Duyệt lịch thành công',
            'booking' => $this->formatBooking($booking),
        ]);
    }

    /**
     * Hủy/Từ chối lịch đặt
     */
    public function cancel($bookingId, Request $request)
    {
        $booking = FacilityBooking::with('facility', 'user')->findOrFail($bookingId);

        if ($booking->status === 'used') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy lịch đã sử dụng',
            ], 422);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Lịch đã bị hủy',
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);



        return response()->json([
            'success' => true,
            'message' => 'Hủy lịch thành công',
        ]);
    }

    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($bookingId, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,used,cancelled',
        ]);

        $booking = FacilityBooking::with('facility', 'user')->findOrFail($bookingId);
        $oldStatus = $booking->status;

        // Kiểm tra chuyển đổi hợp lệ
        $validTransitions = [
            'pending' => ['approved', 'cancelled'],
            'approved' => ['used', 'cancelled'],
            'used' => [],
            'cancelled' => [],
        ];

        if (!in_array($request->status, $validTransitions[$oldStatus] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể chuyển từ ' . $oldStatus . ' sang ' . $request->status,
            ], 422);
        }

        $booking->update(['status' => $request->status]);



        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'booking' => $this->formatBooking($booking),
        ]);
    }

    /**
     * Format booking response
     */
    private function formatBooking($booking)
    {
        return [
            'id' => $booking->id,
            'user_name' => $booking->user?->name,
            'user_email' => $booking->user?->email,
            'facility_name' => $booking->facility->name,
            'booking_date' => $booking->booking_date->format('Y-m-d'),
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'number_of_people' => $booking->number_of_people,
            'status' => $booking->status,
            'status_label' => $booking->status_label,
            'qr_code' => $booking->qr_code,
            'checked_in_at' => $booking->checked_in_at,
        ];
    }
}
