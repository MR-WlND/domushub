<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\UtilityMeter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UtilityMeterController extends Controller
{
    /**
     * Danh sách chỉ số điện nước
     */
    public function index(Request $request): View
    {
        $blockId = $request->query('block_id');
        $floorId = $request->query('floor_id');
        $type = $request->query('type');
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

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

        $readings = $query->latest()->paginate(15)->withQueryString();
        $blocks = Block::orderBy('name')->get();
        $floors = Floor::with('block')->orderBy('floor_number')->get();

        return view('admin.utility-readings.index', compact(
            'readings', 'blocks', 'floors', 'blockId', 'floorId', 'type', 'month', 'year'
        ));
    }

    /**
     * Form tạo mới chỉ số
     */
    public function create(Request $request): View
    {
        $blocks = Block::orderBy('name')->get();
        $apartments = Apartment::with('floor.block')->where('status', '!=', 'maintenance')->orderBy('apartment_number')->get();

        return view('admin.utility-readings.create', compact('blocks', 'apartments'));
    }

    /**
     * Lưu chỉ số mới
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'type' => 'required|in:electricity,water',
            'record_month' => 'required|integer|min:1|max:12',
            'record_year' => 'required|integer|min:2020|max:2100',
            'old_value' => 'required|integer|min:0',
            'new_value' => 'required|integer|min:0|gte:old_value',
        ], [
            'apartment_id.required' => 'Vui lòng chọn căn hộ.',
            'type.required' => 'Vui lòng chọn loại (điện/nước).',
            'new_value.gte' => 'Chỉ số mới phải lớn hơn hoặc bằng chỉ số cũ.',
        ]);

        // Kiểm tra trùng (unique constraint trong DB)
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

        UtilityMeter::create(array_merge($validated, [
            'recorded_by' => auth()->id(),
        ]));

        return redirect()->route('admin.utility-readings.index', [
            'month' => $validated['record_month'],
            'year' => $validated['record_year'],
        ])->with('success', 'Đã ghi chỉ số thành công.');
    }
}
