<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\ServicePrice;
use App\Models\UtilityReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UtilityReadingController extends Controller
{
    /**
     * Trang chính - Danh sách chỉ số điện nước.
     */
    public function index(Request $request): View
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $blockId = $request->input('block_id');
        $serviceType = $request->input('service_type', 'electricity');

        // Lấy danh sách blocks cho bộ lọc
        $blocks = Block::orderBy('name')->get();

        // Query căn hộ theo filter
        $apartmentsQuery = Apartment::with(['floor.block'])
            ->where('status', 'occupied');

        if ($blockId) {
            $apartmentsQuery->whereHas('floor', function ($q) use ($blockId) {
                $q->where('block_id', $blockId);
            });
        }

        $apartments = $apartmentsQuery->orderBy('id')->get();

        // Lấy readings hiện tại cho tháng/năm đã chọn
        $readings = UtilityReading::where('service_type', $serviceType)
            ->forMonth($month, $year)
            ->get()
            ->keyBy('apartment_id');

        // Lấy đơn giá hiện hành
        $currentPrice = ServicePrice::getCurrentPrice($serviceType) ?? 0;

        // Chuẩn bị dữ liệu cho view
        $tableData = [];
        foreach ($apartments as $apartment) {
            $reading = $readings->get($apartment->id);
            $previousReading = UtilityReading::getPreviousReading(
                $apartment->id,
                $serviceType,
                $month,
                $year
            );

            $tableData[] = [
                'apartment' => $apartment,
                'block_name' => $apartment->floor->block->name ?? '—',
                'floor_number' => $apartment->floor->floor_number ?? '—',
                'reading' => $reading,
                'previous_reading' => $reading ? (float) $reading->previous_reading : $previousReading,
                'current_reading' => $reading ? (float) $reading->current_reading : 0,
                'consumption' => $reading ? (float) $reading->consumption : 0,
                'unit_price' => $reading ? (float) $reading->unit_price : $currentPrice,
                'total_amount' => $reading ? (float) $reading->total_amount : 0,
                'status' => $reading?->status ?? 'none',
                'reading_id' => $reading?->id,
            ];
        }

        // Thống kê tổng
        $stats = [
            'total_apartments' => count($tableData),
            'finalized' => $readings->where('status', 'finalized')->count(),
            'draft' => $readings->where('status', 'draft')->count(),
            'not_recorded' => count($tableData) - $readings->count(),
            'total_consumption' => $readings->sum('consumption'),
            'total_amount' => $readings->sum('total_amount'),
        ];

        return view('admin.utility-readings.index', compact(
            'month',
            'year',
            'blockId',
            'serviceType',
            'blocks',
            'tableData',
            'stats',
            'currentPrice',
        ));
    }

    /**
     * Lưu chỉ số hàng loạt.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2099',
            'service_type' => 'required|in:electricity,water',
            'readings' => 'required|array',
            'readings.*.apartment_id' => 'required|exists:apartments,id',
            'readings.*.current_reading' => 'required|numeric|min:0',
        ]);

        $month = (int) $request->month;
        $year = (int) $request->year;
        $serviceType = $request->service_type;

        DB::transaction(function () use ($request, $month, $year, $serviceType) {
            foreach ($request->readings as $data) {
                $apartmentId = $data['apartment_id'];
                $currentReading = (float) $data['current_reading'];

                // Lấy chỉ số cũ tự động từ tháng trước
                $previousReading = UtilityReading::getPreviousReading(
                    $apartmentId,
                    $serviceType,
                    $month,
                    $year
                );

                // Lấy đơn giá hiện hành
                $unitPrice = ServicePrice::getCurrentPrice($serviceType) ?? 0;

                $consumption = max(0, $currentReading - $previousReading);
                $totalAmount = $consumption * $unitPrice;

                UtilityReading::updateOrCreate(
                    [
                        'apartment_id' => $apartmentId,
                        'service_type' => $serviceType,
                        'billing_month' => $month,
                        'billing_year' => $year,
                    ],
                    [
                        'previous_reading' => $previousReading,
                        'current_reading' => $currentReading,
                        'consumption' => $consumption,
                        'unit_price' => $unitPrice,
                        'total_amount' => $totalAmount,
                        'status' => 'draft',
                        'recorded_by' => Auth::id(),
                    ]
                );
            }
        });

        return redirect()->route('admin.utility-readings.index', [
            'month' => $month,
            'year' => $year,
            'service_type' => $serviceType,
            'block_id' => $request->block_id,
        ])->with('success', 'Đã lưu chỉ số thành công!');
    }

    /**
     * Cập nhật chỉ số đơn lẻ (chỉ khi draft).
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $reading = UtilityReading::findOrFail($id);

        if ($reading->status === 'finalized') {
            return back()->with('error', 'Không thể sửa chỉ số đã chốt.');
        }

        $request->validate([
            'current_reading' => 'required|numeric|min:0',
        ]);

        $reading->current_reading = (float) $request->current_reading;
        $reading->calculateAmounts();
        $reading->recorded_by = Auth::id();
        $reading->save();

        return back()->with('success', 'Đã cập nhật chỉ số.');
    }

    /**
     * Chốt số tất cả bản ghi draft của tháng được chọn.
     */
    public function finalize(Request $request): RedirectResponse
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2099',
            'service_type' => 'required|in:electricity,water',
        ]);

        $month = (int) $request->month;
        $year = (int) $request->year;
        $serviceType = $request->service_type;

        $count = UtilityReading::where('service_type', $serviceType)
            ->forMonth($month, $year)
            ->draft()
            ->update([
                'status' => 'finalized',
                'finalized_at' => now(),
            ]);

        if ($count === 0) {
            return back()->with('warning', 'Không có bản ghi nào cần chốt.');
        }

        return redirect()->route('admin.utility-readings.index', [
            'month' => $month,
            'year' => $year,
            'service_type' => $serviceType,
            'block_id' => $request->block_id,
        ])->with('success', "Đã chốt thành công {$count} bản ghi!");
    }

    /**
     * API: Lấy danh sách căn hộ theo block (cho AJAX).
     */
    public function getApartments(Request $request): JsonResponse
    {
        $blockId = $request->input('block_id');

        $query = Apartment::with(['floor.block'])->where('status', 'occupied');

        if ($blockId) {
            $query->whereHas('floor', function ($q) use ($blockId) {
                $q->where('block_id', $blockId);
            });
        }

        $apartments = $query->get()->map(function ($apt) {
            return [
                'id' => $apt->id,
                'apartment_number' => $apt->apartment_number,
                'block_name' => $apt->floor->block->name ?? '—',
                'floor_number' => $apt->floor->floor_number ?? '—',
            ];
        });

        return response()->json($apartments);
    }
}
