<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FacilityController extends Controller
{
    /**
     * Danh sách tiện ích chung cư (cư dân xem)
     */
    public function index(): View
    {
        $facilities = Facility::orderBy('name')->get();

        return view('resident.facilities.index', compact('facilities'));
    }

    /**
     * Chi tiết một tiện ích
     */
    public function show(Facility $facility): View
    {
        $slots = $facility->getTimeSlots();

        return view('resident.facilities.show', compact('facility', 'slots'));
    }

    /**
     * Form đặt lịch tiện ích
     */
    public function book(Facility $facility): View
    {
        if ($facility->status !== 'available') {
            return redirect()->route('resident.facilities.show', $facility)
                ->with('error', 'Tiện ích này hiện không mở cửa.');
        }

        $slots = $facility->getTimeSlots();

        return view('resident.facilities.book', compact('facility', 'slots'));
    }

    /**
     * Xử lý đặt lịch – tự động duyệt ngay, kiểm tra bảo trì & trùng lịch theo sức chứa
     */
    public function storeBooking(Request $request, Facility $facility): RedirectResponse
    {
        // Kiểm tra trạng thái tiện ích
        if ($facility->status === 'maintenance') {
            return back()->with('error', '⚠️ Tiện ích "' . $facility->name . '" đang bảo trì, không thể đặt lịch.');
        }

        if ($facility->status !== 'available') {
            return back()->with('error', 'Tiện ích này hiện đã đóng cửa, không thể đặt lịch.');
        }

        $validated = $request->validate([
            'booking_date'     => 'required|date|after_or_equal:today',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'number_of_people' => 'required|integer|min:1|max:' . $facility->capacity,
        ], [
            'booking_date.required'       => 'Vui lòng chọn ngày.',
            'booking_date.after_or_equal' => 'Ngày đặt phải từ hôm nay trở đi.',
            'start_time.required'         => 'Vui lòng chọn khung giờ.',
            'end_time.required'           => 'Thời gian kết thúc không hợp lệ.',
            'end_time.after'              => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'number_of_people.required'   => 'Vui lòng nhập số người.',
            'number_of_people.max'        => 'Vượt quá sức chứa tối đa (' . $facility->capacity . ' người).',
        ]);

        $user = Auth::user();

        // Kiểm tra cư dân chưa có lịch trùng khung giờ (chính họ)
        $ownConflict = FacilityBooking::where('user_id', $user->id)
            ->where('facility_id', $facility->id)
            ->whereDate('booking_date', $validated['booking_date'])
            ->whereIn('status', ['approved', 'used'])
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                  ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($ownConflict) {
            return back()->withInput()->with('error', 'Bạn đã có lịch đặt tiện ích này trong khung giờ này rồi.');
        }

        // Kiểm tra tổng số người đang đặt trong khung giờ đó có vượt sức chứa không
        $bookedPeople = FacilityBooking::where('facility_id', $facility->id)
            ->whereDate('booking_date', $validated['booking_date'])
            ->whereIn('status', ['approved', 'used'])
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                  ->where('end_time', '>', $validated['start_time']);
            })
            ->sum('number_of_people');

        $remaining = $facility->capacity - $bookedPeople;

        if ($remaining <= 0) {
            return back()->withInput()->with('error',
                'Khung giờ này đã đạt sức chứa tối đa (' . $facility->capacity . ' người). Vui lòng chọn khung giờ khác.'
            );
        }

        if ($validated['number_of_people'] > $remaining) {
            return back()->withInput()->with('error',
                'Khung giờ này chỉ còn chỗ cho ' . $remaining . ' người nữa (sức chứa tối đa: ' . $facility->capacity . ' người).'
            );
        }

        // Tạo booking và tự động duyệt ngay
        $booking = FacilityBooking::create([
            'facility_id'      => $facility->id,
            'user_id'          => $user->id,
            'booking_date'     => $validated['booking_date'],
            'start_time'       => $validated['start_time'],
            'end_time'         => $validated['end_time'],
            'number_of_people' => $validated['number_of_people'],
            'status'           => 'approved',   // Tự động duyệt
            'payment_status'   => 'unpaid',
        ]);

        // Tạo QR code ngay lập tức
        $booking->generateQrCode();

        if ($booking->amount > 0) {
            return redirect()->route('resident.facility-bookings.index', ['pay_booking_id' => $booking->id])
                ->with('success', '🎉 Đặt lịch thành công! Vui lòng chọn phương thức thanh toán để nhận mã QR check-in.');
        }

        return redirect()->route('resident.facility-bookings.qr', $booking)
            ->with('success', '🎉 Đặt lịch thành công! Mã QR check-in đã sẵn sàng.');
    }

    /**
     * Lịch sử đặt lịch của cư dân
     */
    public function bookingHistory(Request $request): View
    {
        $user  = Auth::user();
        $query = FacilityBooking::with('facility')
            ->where('user_id', $user->id)
            ->orderByDesc('booking_date')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        $bookings   = $query->paginate(10)->withQueryString();
        $facilities = Facility::orderBy('name')->get();

        $stats = [
            'pending'   => FacilityBooking::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved'  => FacilityBooking::where('user_id', $user->id)->where('status', 'approved')->count(),
            'used'      => FacilityBooking::where('user_id', $user->id)->where('status', 'used')->count(),
            'cancelled' => FacilityBooking::where('user_id', $user->id)->where('status', 'cancelled')->count(),
        ];

        return view('resident.facility-bookings.index', compact('bookings', 'facilities', 'stats'));
    }

    /**
     * Hủy lịch đặt
     */
    public function cancelBooking(FacilityBooking $booking): RedirectResponse
    {
        $user = Auth::user();

        if ($booking->user_id !== $user->id) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'approved'])) {
            return back()->with('error', 'Không thể hủy lịch ở trạng thái "' . $booking->status_label . '".');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Đã hủy lịch đặt thành công.');
    }

    /**
     * Xem QR code check-in
     */
    public function showQr(FacilityBooking $booking): View
    {
        $user = Auth::user();

        if ($booking->user_id !== $user->id) {
            abort(403);
        }

        $booking->load('facility');

        return view('resident.facility-bookings.qr', compact('booking'));
    }

    /**
     * Thanh toán phí sử dụng
     */
    public function pay(Request $request, FacilityBooking $booking): RedirectResponse
    {
        $user = Auth::user();

        if ($booking->user_id !== $user->id) {
            abort(403);
        }

        if ($booking->status !== 'approved') {
            return back()->with('error', 'Chỉ có thể thanh toán lịch đã được duyệt.');
        }

        if ($booking->payment_status === 'paid') {
            return back()->with('error', 'Lịch này đã được thanh toán.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer,vnpay',
        ]);

        $paymentMethod = $validated['payment_method'];
        $paymentStatus = in_array($paymentMethod, ['bank_transfer', 'vnpay']) ? 'paid' : 'unpaid';

        $booking->update([
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentMethod,
        ]);

        if ($paymentStatus === 'paid') {
            return redirect()->route('resident.facility-bookings.qr', $booking)
                ->with('success', '🎉 Thanh toán thành công! Mã QR check-in đã sẵn sàng.');
        }

        return redirect()->route('resident.facility-bookings.index')
            ->with('success', 'Đã ghi nhận phương thức thanh toán tiền mặt. Vui lòng thanh toán trực tiếp tại văn phòng.');
    }
}
