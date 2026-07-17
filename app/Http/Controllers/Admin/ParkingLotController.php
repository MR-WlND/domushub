<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkingLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkingLotController extends Controller
{
    public function index(Request $request)
    {
        // Danh sách lốt ô tô (hiển thị grid/table)
        $carLots = ParkingLot::with(['apartment.residents.user', 'vehicle'])
            ->where('lot_type', 'car')
            ->orderBy('lot_number')
            ->get();

        // Danh sách khu vực xe máy (hiển thị list/zones)
        $motorbikeLots = ParkingLot::where('lot_type', 'motorbike')
            ->orderBy('lot_number')
            ->get();

        return view('admin.parking-lots.index', compact('carLots', 'motorbikeLots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'creation_mode' => 'required|in:single,bulk',
            'lot_type'      => 'required|in:motorbike,car',

            // Single
            'lot_number'    => 'required_if:creation_mode,single|nullable|string|max:50|unique:parking_lots,lot_number',
            'zone'          => 'nullable|string|max:50',
            'capacity'      => 'required_if:lot_type,motorbike|nullable|integer|min:1',

            // Bulk (chỉ ô tô)
            'bulk_zone'     => 'required_if:creation_mode,bulk|nullable|string|max:50',
            'lot_prefix'    => 'required_if:creation_mode,bulk|nullable|string|max:10',
            'start_num'     => 'required_if:creation_mode,bulk|nullable|integer|min:1',
            'end_num'       => 'required_if:creation_mode,bulk|nullable|integer|min:1|gte:start_num',
        ], [
            'bulk_zone.required_if'  => 'Vui lòng nhập tên tầng/khu vực.',
            'lot_prefix.required_if' => 'Vui lòng nhập tiền tố mã lốt.',
            'start_num.required_if'  => 'Vui lòng nhập số bắt đầu.',
            'end_num.required_if'    => 'Vui lòng nhập số kết thúc.',
            'end_num.gte'            => 'Số kết thúc phải lớn hơn hoặc bằng số bắt đầu.',
        ]);

        if ($request->creation_mode === 'single') {
            $capacity = $request->lot_type === 'motorbike' ? ($request->capacity ?? 1) : 1;

            ParkingLot::create([
                'lot_number' => strtoupper($request->lot_number),
                'zone'       => $request->zone ?: null,
                'lot_type'   => $request->lot_type,
                'status'     => 'available',
                'capacity'   => $capacity,
            ]);

            return back()->with('success', 'Đã thêm lốt/khu vực đỗ xe mới.');
        }

        // ── Bulk: chỉ ô tô ──
        if ($request->lot_type !== 'car') {
            return back()->withErrors(['creation_mode' => 'Tạo hàng loạt chỉ áp dụng cho lốt Ô tô.']);
        }

        $zone   = trim($request->bulk_zone);
        $prefix = strtoupper(trim($request->lot_prefix));
        $start  = (int) $request->start_num;
        $end    = (int) $request->end_num;

        if (($end - $start + 1) > 200) {
            return back()->withErrors(['end_num' => 'Tối đa tạo 200 lốt mỗi lần.']);
        }

        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($zone, $prefix, $start, $end, &$created, &$skipped) {
            for ($i = $start; $i <= $end; $i++) {
                $numStr    = str_pad($i, 2, '0', STR_PAD_LEFT);
                $lotNumber = "{$prefix}-{$numStr}";

                if (ParkingLot::where('lot_number', $lotNumber)->exists()) {
                    $skipped++;
                    continue;
                }

                ParkingLot::create([
                    'lot_number' => $lotNumber,
                    'zone'       => $zone,
                    'lot_type'   => 'car',
                    'status'     => 'available',
                    'capacity'   => 1,
                ]);
                $created++;
            }
        });

        $msg = "Đã tạo {$created} lốt ô tô tại \"{$zone}\" ({$prefix}-" . str_pad($start, 2, '0', STR_PAD_LEFT) . " → {$prefix}-" . str_pad($end, 2, '0', STR_PAD_LEFT) . ")";
        if ($skipped > 0) {
            $msg .= ". Bỏ qua {$skipped} lốt đã tồn tại.";
        }

        return back()->with('success', $msg);
    }

    public function update(Request $request, ParkingLot $parkingLot)
    {
        $request->validate([
            'lot_number' => 'required|string|max:50|unique:parking_lots,lot_number,' . $parkingLot->id,
            'capacity'   => 'nullable|integer|min:1',
        ]);

        $data = [
            'lot_number' => strtoupper($request->lot_number),
        ];

        if ($parkingLot->lot_type === 'motorbike') {
            $data['capacity'] = $request->capacity ?? $parkingLot->capacity;
        }

        $parkingLot->update($data);

        return back()->with('success', 'Đã cập nhật thông tin lốt đỗ.');
    }

    public function destroy(ParkingLot $parkingLot)
    {
        if ($parkingLot->status === 'occupied') {
            return back()->withErrors(['parking_lot' => 'Không thể xóa lốt đỗ đang có xe sử dụng.']);
        }

        $parkingLot->delete();

        return back()->with('success', 'Đã xóa lốt đỗ xe.');
    }
}
