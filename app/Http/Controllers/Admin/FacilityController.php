<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    /**
     * Danh sách tiện ích
     */
    public function index(): View
    {
        $facilities = Facility::withCount([
            'bookings',
            'pendingBookings',
        ])->orderBy('name')->get();

        return view('admin.amenities.index', compact('facilities'));
    }

    /**
     * Form tạo tiện ích mới
     */
    public function create(): View
    {
        return view('admin.amenities.create');
    }

    /**
     * Lưu tiện ích mới
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:facilities,name',
            'capacity'      => 'required|integer|min:1',
            'description'   => 'nullable|string|max:500',
            'status'        => 'required|in:available,maintenance,closed',
            'open_time'     => 'nullable|date_format:H:i',
            'close_time'    => 'nullable|date_format:H:i|after:open_time',
            'slot_duration' => 'required|integer|in:30,60,90,120',
            'price_per_slot'=> 'required|numeric|min:0',
            'rules'         => 'nullable|string|max:1000',
        ], [
            'name.required'        => 'Vui lòng nhập tên tiện ích.',
            'name.unique'          => 'Tên tiện ích đã tồn tại.',
            'capacity.required'    => 'Vui lòng nhập sức chứa.',
            'capacity.min'         => 'Sức chứa phải ít nhất 1 người.',
            'status.required'      => 'Vui lòng chọn trạng thái.',
            'close_time.after'     => 'Giờ đóng cửa phải sau giờ mở cửa.',
            'slot_duration.in'     => 'Thời lượng slot không hợp lệ.',
            'price_per_slot.min'   => 'Giá phải lớn hơn hoặc bằng 0.',
        ]);

        Facility::create($validated);

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Đã thêm tiện ích "' . $validated['name'] . '" thành công.');
    }

    /**
     * Chi tiết tiện ích + danh sách booking
     */
    public function show(Facility $facility): View
    {
        $bookings = FacilityBooking::with('user')
            ->where('facility_id', $facility->id)
            ->orderByDesc('booking_date')
            ->orderByDesc('created_at')
            ->paginate(15);

        $stats = [
            'total'     => FacilityBooking::where('facility_id', $facility->id)->count(),
            'pending'   => FacilityBooking::where('facility_id', $facility->id)->where('status', 'pending')->count(),
            'approved'  => FacilityBooking::where('facility_id', $facility->id)->where('status', 'approved')->count(),
            'completed' => FacilityBooking::where('facility_id', $facility->id)->where('status', 'completed')->count(),
        ];

        return view('admin.amenities.show', compact('facility', 'bookings', 'stats'));
    }

    /**
     * Form chỉnh sửa tiện ích
     */
    public function edit(Facility $facility): View
    {
        return view('admin.amenities.edit', compact('facility'));
    }

    /**
     * Cập nhật tiện ích
     */
    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:facilities,name,' . $facility->id,
            'capacity'      => 'required|integer|min:1',
            'description'   => 'nullable|string|max:500',
            'status'        => 'required|in:available,maintenance,closed',
            'open_time'     => 'nullable|date_format:H:i',
            'close_time'    => 'nullable|date_format:H:i|after:open_time',
            'slot_duration' => 'required|integer|in:30,60,90,120',
            'price_per_slot'=> 'required|numeric|min:0',
            'rules'         => 'nullable|string|max:1000',
        ], [
            'name.required'        => 'Vui lòng nhập tên tiện ích.',
            'name.unique'          => 'Tên tiện ích đã tồn tại.',
            'capacity.required'    => 'Vui lòng nhập sức chứa.',
            'status.required'      => 'Vui lòng chọn trạng thái.',
            'close_time.after'     => 'Giờ đóng cửa phải sau giờ mở cửa.',
            'slot_duration.in'     => 'Thời lượng slot không hợp lệ.',
            'price_per_slot.min'   => 'Giá phải lớn hơn hoặc bằng 0.',
        ]);

        $facility->update($validated);

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Đã cập nhật tiện ích thành công.');
    }

    /**
     * Xóa tiện ích
     */
    public function destroy(Facility $facility): RedirectResponse
    {
        if ($facility->bookings()->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->route('admin.amenities.index')
                ->with('error', 'Không thể xóa tiện ích đang có lịch đặt chờ xử lý.');
        }

        $facility->delete();

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Đã xóa tiện ích thành công.');
    }

    /**
     * Duyệt booking
     */
    public function approveBooking(FacilityBooking $booking): RedirectResponse
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể duyệt booking đang ở trạng thái chờ.');
        }

        $booking->update(['status' => 'approved']);

        return back()->with('success', 'Đã duyệt lịch đặt tiện ích thành công.');
    }

    /**
     * Từ chối booking
     */
    public function rejectBooking(Request $request, FacilityBooking $booking): RedirectResponse
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể từ chối booking đang ở trạng thái chờ.');
        }

        $booking->update(['status' => 'rejected']);

        return back()->with('success', 'Đã từ chối lịch đặt.');
    }

    /**
     * Danh sách tất cả booking (toàn hệ thống)
     */
    public function bookings(Request $request): View
    {
        $query = FacilityBooking::with(['facility', 'user'])
            ->orderByDesc('booking_date')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        $bookings   = $query->paginate(20)->withQueryString();
        $facilities = Facility::orderBy('name')->get();

        return view('admin.amenities.bookings', compact('bookings', 'facilities'));
    }
}
