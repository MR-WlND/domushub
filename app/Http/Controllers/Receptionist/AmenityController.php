<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityBooking;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status'      => 'nullable|string|in:pending,approved,used,cancelled,rejected',
            'facility_id' => 'nullable|integer|exists:facilities,id',
        ]);

        $query = FacilityBooking::with(['facility', 'user'])
            ->latest();

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['facility_id'])) {
            $query->where('facility_id', $validated['facility_id']);
        }

        $bookings  = $query->paginate(15)->withQueryString();
        $facilities = Facility::where('status', 'active')->orderBy('name')->get();

        return view('receptionist.amenities.index', compact('bookings', 'facilities'));
    }

    public function approveBooking($id)
    {
        $booking = FacilityBooking::with('facility')->findOrFail($id);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể duyệt lịch đặt đang ở trạng thái chờ.');
        }

        if ($booking->facility && !in_array($booking->facility->status, ['available', 'active'], true)) {
            return back()->with('error', 'Không thể duyệt lịch do tiện ích hiện đang bảo trì hoặc tạm đóng.');
        }

        $booking->update(['status' => 'approved']);

        return back()->with('success', 'Đã duyệt đặt lịch tiện ích.');
    }

    public function rejectBooking(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ], [
            'reason.max' => 'Lý do từ chối không được vượt quá 500 ký tự.',
        ]);

        $booking = FacilityBooking::findOrFail($id);
        $booking->update([
            'status'          => 'rejected',
            'rejection_reason' => $validated['reason'] ?? null,
        ]);

        return back()->with('success', 'Đã từ chối đặt lịch tiện ích.');
    }

    /**
     * View quét QR code check-in tiện ích cho lễ tân
     */
    public function scanQr()
    {
        return view('receptionist.amenities.scan-qr');
    }

    /**
     * Xử lý quét/nhập mã QR check-in
     */
    public function processQrScan(Request $request)
    {
        $request->validate([
            'qr_code' => ['required', 'string'],
        ]);

        $code = trim($request->qr_code);

        $booking = FacilityBooking::with(['facility', 'user'])
            ->where('qr_code', $code)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Mã QR không tồn tại hoặc không hợp lệ.',
            ], 404);
        }

        $info = [
            'id'             => $booking->id,
            'facility_name'  => $booking->facility->name ?? '—',
            'user_name'      => $booking->user->name ?? '—',
            'user_email'     => $booking->user->email ?? '—',
            'booking_date'   => \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y'),
            'start_time'     => substr($booking->start_time, 0, 5),
            'end_time'       => substr($booking->end_time, 0, 5),
            'number_of_people' => $booking->number_of_people ?? 1,
            'status'         => $booking->status,
            'status_label'   => $booking->status_label ?? $booking->status,
            'amount'         => $booking->amount,
            'payment_status' => $booking->payment_status,
            'checked_in_at'  => $booking->checked_in_at ? $booking->checked_in_at->format('H:i d/m/Y') : null,
        ];

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Lịch đặt này đã bị hủy bởi cư dân.',
                'booking' => $info,
            ], 400);
        }

        if ($booking->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Lịch đặt này đã bị từ chối.',
                'booking' => $info,
            ], 400);
        }

        if ($booking->status === 'used') {
            return response()->json([
                'success' => false,
                'message' => 'Lịch đặt đã được Check-in vào lúc ' . ($info['checked_in_at'] ?? '') . '.',
                'booking' => $info,
            ], 409);
        }

        if ($booking->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Lịch đặt này đang chờ duyệt. Vui lòng duyệt lịch trước khi Check-in.',
                'booking' => $info,
            ], 400);
        }

        // status === 'approved' -> hợp lệ
        return response()->json([
            'success' => true,
            'message' => 'Mã QR hợp lệ — Xác nhận Check-in cho cư dân.',
            'booking' => $info,
        ]);
    }

    /**
     * Xác nhận Check-in cho lịch đặt
     */
    public function checkin(Request $request)
    {
        $request->validate(['qr_code' => ['required', 'string']]);

        $booking = FacilityBooking::where('qr_code', trim($request->qr_code))->firstOrFail();

        if ($booking->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Lịch đặt không ở trạng thái chờ Check-in.',
            ], 422);
        }

        $booking->update([
            'status'        => 'used',
            'checked_in_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã xác nhận Check-in thành công cho cư dân lúc ' . now()->format('H:i d/m/Y') . '.',
        ]);
    }
}
