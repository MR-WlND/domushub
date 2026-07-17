<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'name'             => 'required|string|max:100|unique:facilities,name',
            'capacity'         => 'required|integer|min:1',
            'description'      => 'nullable|string|max:500',
            'status'           => 'required|in:available,maintenance,closed',
            'open_time'        => 'nullable|date_format:H:i',
            'close_time'       => 'nullable|date_format:H:i|after:open_time',
            'slot_duration'    => 'required|integer|in:0,30,60,90,120',
            'booking_type'     => 'required|in:slot,person',
            'price_per_slot'   => 'required|numeric|min:0',
            'price_per_person' => 'required|numeric|min:0',
            'rules'            => 'nullable|string|max:1000',
            'images'           => 'nullable|array|max:5',
            'images.*'         => 'image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'name.required'           => 'Vui lòng nhập tên tiện ích.',
            'name.unique'             => 'Tên tiện ích đã tồn tại.',
            'capacity.required'       => 'Vui lòng nhập sức chứa.',
            'capacity.min'            => 'Sức chứa phải ít nhất 1 người.',
            'status.required'         => 'Vui lòng chọn trạng thái.',
            'close_time.after'        => 'Giờ đóng cửa phải sau giờ mở cửa.',
            'slot_duration.in'        => 'Thời lượng slot không hợp lệ.',
            'booking_type.required'   => 'Vui lòng chọn kiểu đặt chỗ.',
            'price_per_slot.min'      => 'Giá phải lớn hơn hoặc bằng 0.',
            'price_per_person.min'    => 'Giá theo người phải lớn hơn hoặc bằng 0.',
            'images.max'              => 'Tải tối đa 5 ảnh.',
            'images.*.image'          => 'File phải là ảnh.',
            'images.*.max'            => 'Mỗi ảnh tối đa 3MB.',
        ]);

        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('facilities', 'public');
                $uploadedImages[] = $path;
            }
        }

        $data = $validated;
        $data['images'] = $uploadedImages;

        Facility::create($data);

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
            'name'             => 'required|string|max:100|unique:facilities,name,' . $facility->id,
            'capacity'         => 'required|integer|min:1',
            'description'      => 'nullable|string|max:500',
            'status'           => 'required|in:available,maintenance,closed',
            'open_time'        => 'nullable|date_format:H:i',
            'close_time'       => 'nullable|date_format:H:i|after:open_time',
            'slot_duration'    => 'required|integer|in:0,30,60,90,120',
            'booking_type'     => 'required|in:slot,person',
            'price_per_slot'   => 'required|numeric|min:0',
            'price_per_person' => 'required|numeric|min:0',
            'rules'            => 'nullable|string|max:1000',
        ], [
            'name.required'           => 'Vui lòng nhập tên tiện ích.',
            'name.unique'             => 'Tên tiện ích đã tồn tại.',
            'capacity.required'       => 'Vui lòng nhập sức chứa.',
            'status.required'         => 'Vui lòng chọn trạng thái.',
            'close_time.after'        => 'Giờ đóng cửa phải sau giờ mở cửa.',
            'slot_duration.in'        => 'Thời lượng slot không hợp lệ.',
            'booking_type.required'   => 'Vui lòng chọn kiểu đặt chỗ.',
            'price_per_slot.min'      => 'Giá phải lớn hơn hoặc bằng 0.',
            'price_per_person.min'    => 'Giá theo người phải lớn hơn hoặc bằng 0.',
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
     * Hủy booking (admin)
     */
    public function cancelBooking(FacilityBooking $booking): RedirectResponse
    {
        if (in_array($booking->status, ['used', 'cancelled'])) {
            return back()->with('error', 'Không thể hủy lịch ở trạng thái này.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Đã hủy lịch đặt thành công.');
    }

    /**
     * Cập nhật trạng thái booking (admin linh hoạt)
     */
    public function updateBookingStatus(Request $request, FacilityBooking $booking): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,approved,used,cancelled,rejected',
        ]);

        $allowedTransitions = [
            'pending'   => ['approved', 'rejected', 'cancelled'],
            'approved'  => ['used', 'cancelled'],
            'used'      => [],
            'cancelled' => [],
            'rejected'  => [],
        ];

        $currentStatus = $booking->status;
        $newStatus     = $request->status;

        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            return back()->with('error', 'Không thể chuyển từ "' . $booking->status_label . '" sang trạng thái này.');
        }

        $updateData = ['status' => $newStatus];

        // Nếu chuyển sang 'used' thì ghi thời điểm check-in
        if ($newStatus === 'used' && !$booking->checked_in_at) {
            $updateData['checked_in_at'] = now();
        }

        $booking->update($updateData);

        return back()->with('success', 'Cập nhật trạng thái thành công.');
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

        if ($request->filled('date')) {
            $query->where('booking_date', $request->date);
        }

        $bookings   = $query->paginate(20)->withQueryString();
        $facilities = Facility::orderBy('name')->get();

        $stats = [
            'pending'   => FacilityBooking::where('status', 'pending')->count(),
            'approved'  => FacilityBooking::where('status', 'approved')->count(),
            'used'      => FacilityBooking::where('status', 'used')->count(),
            'cancelled' => FacilityBooking::where('status', 'cancelled')->count(),
        ];

        return view('admin.amenities.bookings', compact('bookings', 'facilities', 'stats'));
    }

    /**
     * Upload ảnh cho tiện ích
     */
    public function storeImage(Request $request, Facility $facility): RedirectResponse
    {
        $request->validate([
            'images'   => 'required|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'images.required'   => 'Vui lòng chọn ít nhất một ảnh.',
            'images.*.image'    => 'File phải là ảnh.',
            'images.*.max'      => 'Mỗi ảnh tối đa 3MB.',
        ]);

        $existing = $facility->images ?? [];

        foreach ($request->file('images') as $file) {
            $path = $file->store('facilities', 'public');
            $existing[] = $path;
        }

        $facility->update(['images' => $existing]);

        return back()->with('success', 'Đã tải lên ' . count($request->file('images')) . ' ảnh.');
    }

    /**
     * Xóa ảnh theo index
     */
    public function destroyImage(Facility $facility, int $index): RedirectResponse
    {
        $images = $facility->images ?? [];

        if (!isset($images[$index])) {
            return back()->with('error', 'Ảnh không tồn tại.');
        }

        Storage::disk('public')->delete($images[$index]);
        array_splice($images, $index, 1);
        $facility->update(['images' => array_values($images)]);

        return back()->with('success', 'Đã xóa ảnh.');
    }

    /**
     * Thay đổi trạng thái nhanh
     */
    public function updateStatus(Request $request, Facility $facility): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:available,maintenance,closed',
        ]);

        $facility->update(['status' => $request->status]);

        return back()->with('success', 'Đã cập nhật trạng thái tiện ích.');
    }

    /**
     * Báo cáo thống kê tiện ích
     */
    public function statistics(Request $request): View
    {
        // Lấy danh sách tòa nhà/block
        $blocks = \App\Models\Block::orderBy('name')->get();
        $selectedBlock = $request->get('block_id');

        // Lấy danh sách các năm từ facility_bookings để hiện trong bộ lọc
        $driver = \Illuminate\Support\Facades\DB::getDriverName();
        $yearExpression = $driver === 'sqlite' ? "strftime('%Y', booking_date)" : "YEAR(booking_date)";
        
        $availableYears = \App\Models\FacilityBooking::selectRaw("DISTINCT $yearExpression as year")
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(fn($y) => (int)$y)
            ->filter()
            ->toArray();
            
        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        $selectedYear = $request->get('year', date('Y'));
        if (!in_array($selectedYear, $availableYears) && count($availableYears) > 0) {
            $selectedYear = $availableYears[0];
        }

        $selectedMonth = $request->get('month');

        // Callback filter function for bookings query
        $applyFilters = function ($query) use ($selectedBlock, $selectedYear, $selectedMonth, $driver) {
            if ($selectedBlock) {
                $query->whereHas('user.apartment.floor', function ($q) use ($selectedBlock) {
                    $q->where('block_id', $selectedBlock);
                });
            }
            if ($selectedYear) {
                if ($driver === 'sqlite') {
                    $query->whereRaw("strftime('%Y', booking_date) = ?", [$selectedYear]);
                } else {
                    $query->whereYear('booking_date', $selectedYear);
                }
            }
            if ($selectedMonth) {
                if ($driver === 'sqlite') {
                    $query->whereRaw("cast(strftime('%m', booking_date) as integer) = ?", [(int)$selectedMonth]);
                } else {
                    $query->whereMonth('booking_date', $selectedMonth);
                }
            }
        };

        $facilities = Facility::withCount([
            'bookings' => function ($q) use ($applyFilters) {
                $applyFilters($q);
            },
            'bookings as approved_count' => function ($q) use ($applyFilters) {
                $q->where('status', 'approved');
                $applyFilters($q);
            },
            'bookings as completed_count' => function ($q) use ($applyFilters) {
                $q->where('status', 'completed');
                $applyFilters($q);
            },
            'bookings as rejected_count' => function ($q) use ($applyFilters) {
                $q->where('status', 'rejected');
                $applyFilters($q);
            },
            'pendingBookings as pending_bookings_count' => function ($q) use ($applyFilters) {
                $applyFilters($q);
            },
        ])->get();

        // Tính doanh thu (approved + completed × price_per_slot)
        foreach ($facilities as $f) {
            $paid = ($f->approved_count + $f->completed_count);
            $f->revenue = $paid * ($f->price_per_slot ?? 0);
        }

        $summary = [
            'total_facilities' => $facilities->count(),
            'total_bookings'   => $facilities->sum('bookings_count'),
            'total_revenue'    => $facilities->sum('revenue'),
            'pending_total'    => $facilities->sum('pending_bookings_count'),
        ];

        return view('admin.amenities.statistics', compact(
            'facilities', 'summary', 'blocks', 'selectedBlock',
            'availableYears', 'selectedYear', 'selectedMonth'
        ));
    }

    /**
     * Xuất báo cáo thống kê Tiện ích thành file Excel.
     */
    public function exportExcel(Request $request)
    {
        $selectedBlock = $request->get('block_id');

        $driver = \Illuminate\Support\Facades\DB::getDriverName();
        $yearExpression = $driver === 'sqlite' ? "strftime('%Y', booking_date)" : "YEAR(booking_date)";
        
        $availableYears = \App\Models\FacilityBooking::selectRaw("DISTINCT $yearExpression as year")
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(fn($y) => (int)$y)
            ->filter()
            ->toArray();
            
        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        $selectedYear = $request->get('year', date('Y'));
        if (!in_array($selectedYear, $availableYears) && count($availableYears) > 0) {
            $selectedYear = $availableYears[0];
        }

        $selectedMonth = $request->get('month');

        // Callback filter function for bookings query
        $applyFilters = function ($query) use ($selectedBlock, $selectedYear, $selectedMonth, $driver) {
            if ($selectedBlock) {
                $query->whereHas('user.apartment.floor', function ($q) use ($selectedBlock) {
                    $q->where('block_id', $selectedBlock);
                });
            }
            if ($selectedYear) {
                if ($driver === 'sqlite') {
                    $query->whereRaw("strftime('%Y', booking_date) = ?", [$selectedYear]);
                } else {
                    $query->whereYear('booking_date', $selectedYear);
                }
            }
            if ($selectedMonth) {
                if ($driver === 'sqlite') {
                    $query->whereRaw("cast(strftime('%m', booking_date) as integer) = ?", [(int)$selectedMonth]);
                } else {
                    $query->whereMonth('booking_date', $selectedMonth);
                }
            }
        };

        $facilities = Facility::withCount([
            'bookings' => function ($q) use ($applyFilters) {
                $applyFilters($q);
            },
            'bookings as approved_count' => function ($q) use ($applyFilters) {
                $q->where('status', 'approved');
                $applyFilters($q);
            },
            'bookings as completed_count' => function ($q) use ($applyFilters) {
                $q->where('status', 'completed');
                $applyFilters($q);
            },
            'bookings as rejected_count' => function ($q) use ($applyFilters) {
                $q->where('status', 'rejected');
                $applyFilters($q);
            },
            'pendingBookings as pending_bookings_count' => function ($q) use ($applyFilters) {
                $applyFilters($q);
            },
        ])->get();

        // Tính doanh thu (approved + completed × price_per_slot)
        foreach ($facilities as $f) {
            $paid = ($f->approved_count + $f->completed_count);
            $f->revenue = $paid * ($f->price_per_slot ?? 0);
        }

        try {
            $filePath = \App\Helpers\SimpleXlsx::exportAmenitiesReport($selectedYear, $selectedMonth, $facilities);
            $filename = "Bao-cao-thong-ke-tien-ich-nam-{$selectedYear}" . ($selectedMonth ? "-thang-{$selectedMonth}" : "") . ".xlsx";
            return response()->download($filePath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xuất file báo cáo tiện ích: ' . $e->getMessage());
        }
    }
}
