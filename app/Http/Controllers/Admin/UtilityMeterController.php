<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\UtilityMeter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UtilityMeterController extends Controller
{
    /**
     * Danh sách chỉ số điện nước + thống kê tổng quan
     */
    public function index(Request $request): View
    {
        $blockId = $request->query('block_id');
        $floorId = $request->query('floor_id');
        $type    = $request->query('type');
        $month   = $request->query('month', now()->month);
        $year    = $request->query('year', now()->year);

        // Query chính
        $query = UtilityMeter::with(['apartment.floor.block', 'recorder'])
            ->where('record_month', $month)
            ->where('record_year', $year);

        if ($type) {
            $query->where('type', $type);
        }

        if ($floorId) {
            $query->whereHas('apartment', fn ($q) => $q->where('floor_id', $floorId));
        } elseif ($blockId) {
            $query->whereHas('apartment.floor', fn ($q) => $q->where('block_id', $blockId));
        }

        $readings = $query->orderBy('type')
            ->orderBy('apartment_id')
            ->paginate(20)
            ->withQueryString();

        // Thống kê tổng quan cho tháng/năm đã chọn
        $statsQuery = UtilityMeter::where('record_month', $month)->where('record_year', $year);

        $stats = [
            'total_records'       => (clone $statsQuery)->count(),
            'total_electricity'   => (clone $statsQuery)->where('type', 'electricity')->sum('usage_amount'),
            'total_water'         => (clone $statsQuery)->where('type', 'water')->sum('usage_amount'),
            'apartments_recorded' => (clone $statsQuery)->distinct('apartment_id')->count('apartment_id'),
            'apartments_total'    => Apartment::where('status', '!=', 'maintenance')->count(),
        ];

        // Dữ liệu filter
        $blocks = Block::orderBy('name')->get();
        $floors = Floor::with('block')->orderBy('floor_number')->get();

        return view('admin.utility-readings.index', compact(
            'readings', 'blocks', 'floors', 'blockId', 'floorId', 'type', 'month', 'year', 'stats'
        ));
    }

    /**
     * Form ghi chỉ số đơn lẻ
     */
    public function create(): View
    {
        $blocks     = Block::orderBy('name')->get();
        $apartments = Apartment::with('floor.block')
            ->where('status', '!=', 'maintenance')
            ->orderBy('apartment_number')
            ->get();

        return view('admin.utility-readings.create', compact('blocks', 'apartments'));
    }

    /**
     * Lưu chỉ số đơn lẻ
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'apartment_id'  => 'required|exists:apartments,id',
            'type'          => 'required|in:electricity,water',
            'record_month'  => 'required|integer|min:1|max:12',
            'record_year'   => 'required|integer|min:2020|max:2100',
            'new_value'     => 'required|integer|min:0',
        ], [
            'apartment_id.required' => 'Vui lòng chọn căn hộ.',
            'type.required'         => 'Vui lòng chọn loại (điện/nước).',
            'new_value.required'    => 'Vui lòng nhập chỉ số mới.',
            'new_value.min'         => 'Chỉ số mới phải >= 0.',
        ]);

        // Kiểm tra trùng
        $exists = UtilityMeter::where('apartment_id', $validated['apartment_id'])
            ->where('type', $validated['type'])
            ->where('record_month', $validated['record_month'])
            ->where('record_year', $validated['record_year'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'apartment_id' => 'Chỉ số loại này đã được ghi cho căn hộ trong tháng/năm đã chọn.',
            ]);
        }

        // Tự động lấy chỉ số cũ
        $oldValue = UtilityMeter::getPreviousNewValue(
            $validated['apartment_id'],
            $validated['type'],
            $validated['record_month'],
            $validated['record_year']
        ) ?? 0;

        UtilityMeter::create([
            'apartment_id'  => $validated['apartment_id'],
            'type'          => $validated['type'],
            'record_month'  => $validated['record_month'],
            'record_year'   => $validated['record_year'],
            'old_value'     => $oldValue,
            'new_value'     => $validated['new_value'],
            'recorded_by'   => auth()->id(),
        ]);

        return redirect()->route('admin.utility-readings.index', [
            'month' => $validated['record_month'],
            'year'  => $validated['record_year'],
        ])->with('success', 'Đã ghi chỉ số thành công.');
    }

    /**
     * Form ghi hàng loạt
     */
    public function batchCreate(Request $request): View
    {
        $blocks = Block::orderBy('name')->get();
        $floors = Floor::with('block')->orderBy('floor_number')->get();

        $selectedBlockId = $request->query('block_id');
        $selectedFloorId = $request->query('floor_id');
        $selectedMonth   = (int) $request->query('month', now()->month);
        $selectedYear    = (int) $request->query('year', now()->year);

        $apartments = collect();

        if ($selectedFloorId) {
            $apartments = Apartment::where('floor_id', $selectedFloorId)
                ->where('status', '!=', 'maintenance')
                ->orderBy('apartment_number')
                ->get();
        } elseif ($selectedBlockId) {
            $floorIds = Floor::where('block_id', $selectedBlockId)->pluck('id');
            $apartments = Apartment::whereIn('floor_id', $floorIds)
                ->where('status', '!=', 'maintenance')
                ->orderBy('apartment_number')
                ->get();
        }

        // Lấy chỉ số cũ + trạng thái cho CẢ điện và nước mỗi căn hộ
        $apartmentData = [];
        foreach ($apartments as $apt) {
            $elecRecorded = UtilityMeter::where('apartment_id', $apt->id)
                ->where('type', 'electricity')
                ->where('record_month', $selectedMonth)
                ->where('record_year', $selectedYear)
                ->exists();

            $waterRecorded = UtilityMeter::where('apartment_id', $apt->id)
                ->where('type', 'water')
                ->where('record_month', $selectedMonth)
                ->where('record_year', $selectedYear)
                ->exists();

            $elecOld = UtilityMeter::getPreviousNewValue(
                $apt->id, 'electricity', $selectedMonth, $selectedYear
            ) ?? 0;

            $waterOld = UtilityMeter::getPreviousNewValue(
                $apt->id, 'water', $selectedMonth, $selectedYear
            ) ?? 0;

            $apartmentData[] = [
                'apartment'      => $apt,
                'elec_old'       => $elecOld,
                'water_old'      => $waterOld,
                'elec_recorded'  => $elecRecorded,
                'water_recorded' => $waterRecorded,
                'both_recorded'  => $elecRecorded && $waterRecorded,
            ];
        }

        return view('admin.utility-readings.batch', compact(
            'blocks', 'floors', 'apartmentData',
            'selectedBlockId', 'selectedFloorId', 'selectedMonth', 'selectedYear'
        ));
    }

    /**
     * Lưu hàng loạt – cả điện lẫn nước trong một lần submit
     */
    public function batchStore(Request $request): RedirectResponse
    {
        $request->validate([
            'record_month' => 'required|integer|min:1|max:12',
            'record_year'  => 'required|integer|min:2020|max:2100',
            'readings'     => 'required|array|min:1',
            'readings.*.apartment_id' => 'required|exists:apartments,id',
        ], [
            'readings.required' => 'Vui lòng nhập ít nhất 1 chỉ số.',
        ]);

        $month   = (int) $request->record_month;
        $year    = (int) $request->record_year;
        $saved   = 0;
        $skipped = 0;

        foreach ($request->readings as $reading) {
            $aptId = $reading['apartment_id'];

            // ── Điện ────────────────────────────────────
            if (isset($reading['elec_new']) && $reading['elec_new'] !== '') {
                $elecExists = UtilityMeter::where('apartment_id', $aptId)
                    ->where('type', 'electricity')
                    ->where('record_month', $month)
                    ->where('record_year', $year)
                    ->exists();

                if ($elecExists) {
                    $skipped++;
                } else {
                    $elecOld = UtilityMeter::getPreviousNewValue($aptId, 'electricity', $month, $year) ?? 0;
                    UtilityMeter::create([
                        'apartment_id' => $aptId,
                        'type'         => 'electricity',
                        'record_month' => $month,
                        'record_year'  => $year,
                        'old_value'    => $elecOld,
                        'new_value'    => (int) $reading['elec_new'],
                        'recorded_by'  => auth()->id(),
                    ]);
                    $saved++;
                }
            }

            // ── Nước ────────────────────────────────────
            if (isset($reading['water_new']) && $reading['water_new'] !== '') {
                $waterExists = UtilityMeter::where('apartment_id', $aptId)
                    ->where('type', 'water')
                    ->where('record_month', $month)
                    ->where('record_year', $year)
                    ->exists();

                if ($waterExists) {
                    $skipped++;
                } else {
                    $waterOld = UtilityMeter::getPreviousNewValue($aptId, 'water', $month, $year) ?? 0;
                    UtilityMeter::create([
                        'apartment_id' => $aptId,
                        'type'         => 'water',
                        'record_month' => $month,
                        'record_year'  => $year,
                        'old_value'    => $waterOld,
                        'new_value'    => (int) $reading['water_new'],
                        'recorded_by'  => auth()->id(),
                    ]);
                    $saved++;
                }
            }
        }

        $message = "Đã ghi thành công {$saved} chỉ số điện/nước.";
        if ($skipped > 0) {
            $message .= " Bỏ qua {$skipped} mục đã chốt trước đó.";
        }

        return redirect()->route('admin.utility-readings.index', [
            'month' => $month,
            'year'  => $year,
        ])->with('success', $message);
    }

    /**
     * Form chỉnh sửa
     */
    public function edit(int $id): View
    {
        $reading = UtilityMeter::with('apartment.floor.block')->findOrFail($id);

        return view('admin.utility-readings.edit', compact('reading'));
    }

    /**
     * Cập nhật chỉ số
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $reading = UtilityMeter::findOrFail($id);

        $validated = $request->validate([
            'old_value' => 'required|integer|min:0',
            'new_value' => 'required|integer|min:0|gte:old_value',
        ], [
            'old_value.required' => 'Vui lòng nhập chỉ số cũ.',
            'new_value.required' => 'Vui lòng nhập chỉ số mới.',
            'new_value.gte'      => 'Chỉ số mới phải >= chỉ số cũ.',
        ]);

        $reading->update($validated);

        return redirect()->route('admin.utility-readings.index', [
            'month' => $reading->record_month,
            'year'  => $reading->record_year,
        ])->with('success', 'Đã cập nhật chỉ số thành công.');
    }

    /**
     * Xóa chỉ số
     */
    public function destroy(int $id): RedirectResponse
    {
        $reading = UtilityMeter::findOrFail($id);
        $month   = $reading->record_month;
        $year    = $reading->record_year;

        $reading->delete();

        return redirect()->route('admin.utility-readings.index', [
            'month' => $month,
            'year'  => $year,
        ])->with('success', 'Đã xóa chỉ số thành công.');
    }

    /**
     * AJAX – Lấy chỉ số cũ từ kỳ trước
     */
    public function getOldValue(Request $request): JsonResponse
    {
        $request->validate([
            'apartment_id' => 'required|integer',
            'type'         => 'required|in:electricity,water',
            'month'        => 'required|integer|min:1|max:12',
            'year'         => 'required|integer|min:2020|max:2100',
        ]);

        $oldValue = UtilityMeter::getPreviousNewValue(
            $request->apartment_id,
            $request->type,
            $request->month,
            $request->year
        );

        return response()->json(['old_value' => $oldValue ?? 0]);
    }
}
