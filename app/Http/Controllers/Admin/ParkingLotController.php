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
            
            // Validate single
            'lot_number'    => 'required_if:creation_mode,single|nullable|string|max:50|unique:parking_lots,lot_number',
            'capacity'      => 'required_if:lot_type,motorbike|nullable|integer|min:1',
            
            // Validate bulk (chỉ dùng cho ô tô)
            'lot_prefix'    => 'required_if:creation_mode,bulk|nullable|string|max:10',
            'start_num'     => 'required_if:creation_mode,bulk|nullable|integer|min:1',
            'end_num'       => 'required_if:creation_mode,bulk|nullable|integer|min:1|gte:start_num',
        ]);

        if ($request->creation_mode === 'single') {
            $capacity = $request->lot_type === 'motorbike' ? ($request->capacity ?? 1) : 1;

            ParkingLot::create([
                'lot_number' => strtoupper($request->lot_number),
                'lot_type'   => $request->lot_type,
                'status'     => 'available',
                'capacity'   => $capacity,
            ]);

            return back()->with('success', 'Đã thêm lốt/khu vực đỗ xe mới.');
        } else {
            // Bulk create for Cars
            if ($request->lot_type !== 'car') {
                return back()->withErrors(['creation_mode' => 'Tạo hàng loạt chỉ áp dụng cho lốt Ô tô.']);
            }

            $prefix = strtoupper($request->lot_prefix);
            $start = $request->start_num;
            $end = $request->end_num;

            DB::transaction(function () use ($prefix, $start, $end) {
                for ($i = $start; $i <= $end; $i++) {
                    // Ví dụ: B1-01, B1-02... B1-10
                    $numStr = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $lotNumber = "{$prefix}-{$numStr}";
                    
                    // Nếu đã tồn tại thì bỏ qua để không lỗi
                    if (!ParkingLot::where('lot_number', $lotNumber)->exists()) {
                        ParkingLot::create([
                            'lot_number' => $lotNumber,
                            'lot_type'   => 'car',
                            'status'     => 'available',
                            'capacity'   => 1,
                        ]);
                    }
                }
            });

            return back()->with('success', "Đã tạo hàng loạt các lốt ô tô từ {$prefix}-" . str_pad($start, 2, '0', STR_PAD_LEFT) . " đến {$prefix}-" . str_pad($end, 2, '0', STR_PAD_LEFT));
        }
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
