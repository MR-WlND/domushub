<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\ServicePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Nếu tiện ích có phí: tạo hóa đơn và chuyển sang trang thanh toán hóa đơn
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

        // 1. Lấy danh sách booking đã duyệt hoặc sử dụng trong ngày
        $bookedSlots = FacilityBooking::where('facility_id', $facility->id)
            ->whereDate('booking_date', $validated['booking_date'])
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
            $requestedStart = substr($validated['start_time'], 0, 5);
            $requestedEnd = substr($validated['end_time'], 0, 5);
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
                        return back()->withInput()->with('error', 'Khung giờ ' . $slot['label'] . ' đã đạt sức chứa tối đa (' . $facility->capacity . ' người). Vui lòng chọn khung giờ khác.');
                    }
                    if ($validated['number_of_people'] > $remaining) {
                        return back()->withInput()->with('error', 'Khung giờ ' . $slot['label'] . ' chỉ còn chỗ cho ' . $remaining . ' người nữa.');
                    }
                }
            }

            if ($overlapSlotsCount === 0) {
                return back()->withInput()->with('error', 'Khung giờ chọn không khớp với bất kỳ slot hoạt động nào.');
            }
        } else {
            // Trường hợp slot_duration = 0 (cả ngày hoặc không chia slot)
            $bookedPeople = 0;
            foreach ($bookedSlots as $booking) {
                if ($booking['start_time'] < $validated['end_time'] && $booking['end_time'] > $validated['start_time']) {
                    $bookedPeople += $booking['number_of_people'];
                }
            }
            $remaining = $facility->capacity - $bookedPeople;
            if ($remaining <= 0) {
                return back()->withInput()->with('error', 'Tiện ích đã đạt sức chứa tối đa trong khung giờ này.');
            }
            if ($validated['number_of_people'] > $remaining) {
                return back()->withInput()->with('error', 'Tiện ích chỉ còn chỗ cho ' . $remaining . ' người nữa.');
            }
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

        // Nếu có phí sử dụng, tạo hóa đơn và chuyển sang trang thanh toán hóa đơn
        if ($booking->amount > 0) {
            $invoice = $this->createInvoiceForBooking($booking, $facility, $user);
            if ($invoice) {
                return redirect()->route('resident.invoices.index')
                    ->with('success', '🎉 Đặt lịch thành công! Vui lòng thanh toán hóa đơn phí sử dụng tiện ích để nhận mã QR check-in.');
            }
        }

        return redirect()->route('resident.facility-bookings.qr', $booking)
            ->with('success', '🎉 Đặt lịch thành công! Mã QR check-in đã sẵn sàng.');
    }

    /**
     * Tạo hóa đơn cho lịch đặt tiện ích có phí
     */
    private function createInvoiceForBooking(FacilityBooking $booking, Facility $facility, $user): ?Invoice
    {
        try {
            // Lấy căn hộ từ bảng residents (giống InvoiceController)
            $resident = $user->residents()
                ->whereNull('deleted_at')
                ->latest()
                ->first();

            // Fallback: thử cột apartment_id trực tiếp trên users
            $apartmentId = $resident?->apartment_id ?? $user->apartment_id;

            if (!$apartmentId) {
                logger()->warning('Booking #' . $booking->id . ': Cư dân không có căn hộ, bỏ qua tạo hóa đơn.');
                return null;
            }

            // Tìm ServicePrice loại 'service' để gắn vào chi tiết hóa đơn
            $servicePrice = ServicePrice::where('status', 'active')->first();

            $amount = $booking->amount;
            $title  = 'Phí sử dụng tiện ích: ' . $facility->name
                . ' (' . \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y')
                . ', ' . substr($booking->start_time, 0, 5) . '–' . substr($booking->end_time, 0, 5) . ')';

            $invoice = DB::transaction(function () use ($booking, $apartmentId, $amount, $title, $servicePrice) {
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

                // Liên kết hóa đơn vào lịch đặt
                $booking->update(['bill_id' => $inv->id]);

                return $inv;
            });

            return $invoice;
        } catch (\Throwable $e) {
            logger()->error('Lỗi tạo hóa đơn cho booking #' . $booking->id . ': ' . $e->getMessage());
            return null;
        }
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
}
