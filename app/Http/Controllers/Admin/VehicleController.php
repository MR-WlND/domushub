<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Vehicle;
use App\Models\ParkingLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    /**
     * Danh sách toàn bộ phương tiện — hỗ trợ filter + pagination
     */
    public function index(Request $request)
    {
        $query = Vehicle::with([
            'apartment.floor.block',
            'apartment.residents.user',
            'parkingLot',
        ])->withoutTrashed();

        // Filter: Tòa nhà
        if ($request->filled('block_id')) {
            $query->whereHas('apartment.floor', function ($q) use ($request) {
                $q->where('block_id', $request->block_id);
            });
        }

        // Filter: Loại xe
        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        // Filter: Trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vehicles      = $query->latest()->paginate(15)->withQueryString();
        $blocks        = Block::orderBy('name')->get();
        $availableLots = ParkingLot::where('status', 'available')->where('lot_type', 'car')->get();

        return view('admin.vehicles.index', compact('vehicles', 'blocks', 'availableLots'));
    }

    // =========================================================================
    // QUẢN LÝ LỐT ĐỖ (chỉ dành cho ô tô)
    // =========================================================================

    public function assignLot(Request $request, Vehicle $vehicle)
    {
        // Chỉ gán lốt cho ô tô
        if (!$vehicle->isCar()) {
            return back()->withErrors(['vehicle' => 'Chỉ có thể gán lốt cho ô tô.']);
        }

        $request->validate([
            'parking_lot_id' => 'required|exists:parking_lots,id'
        ]);

        $lot = ParkingLot::findOrFail($request->parking_lot_id);

        if ($lot->status !== 'available') {
            return back()->withErrors(['parking_lot_id' => 'Lốt đỗ này đã có người sử dụng.']);
        }

        DB::transaction(function () use ($lot, $vehicle) {
            $lot->update(['status' => 'occupied', 'apartment_id' => $vehicle->apartment_id]);
            $vehicle->update(['parking_lot_id' => $lot->id, 'status' => 'active']);
        });

        return back()->with('success', 'Đã gán lốt ' . $lot->lot_number . ' cho xe ' . $vehicle->license_plate);
    }

    public function releaseLot(Vehicle $vehicle)
    {
        if (!$vehicle->parking_lot_id) {
            return back()->withErrors(['vehicle' => 'Xe này không có lốt đỗ để giải phóng.']);
        }

        if ($vehicle->isInside()) {
            return back()->withErrors(['vehicle' => 'Không thể thu hồi lốt khi xe đang ở trong hầm.']);
        }

        DB::transaction(function () use ($vehicle) {
            $lot = ParkingLot::find($vehicle->parking_lot_id);
            if ($lot) {
                $lot->update(['status' => 'available', 'apartment_id' => null]);
            }
            $vehicle->update(['parking_lot_id' => null, 'status' => 'pending']);
        });

        return back()->with('success', 'Đã thu hồi lốt đỗ của xe ' . $vehicle->license_plate);
    }

    // =========================================================================
    // DUYỆT XE MÁY / XE ĐIỆN
    // =========================================================================

    public function approve(Vehicle $vehicle)
    {
        if (!$vehicle->isPending()) {
            return back()->withErrors(['vehicle' => 'Chỉ có thể duyệt xe đang ở trạng thái chờ duyệt.']);
        }

        if ($vehicle->isCar()) {
            return back()->withErrors(['vehicle' => 'Ô tô cần được gán lốt đỗ trước khi duyệt.']);
        }

        $vehicle->update(['status' => 'active']);

        return back()->with('success', 'Đã duyệt xe ' . $vehicle->license_plate . '. Phương tiện hiện đang hoạt động.');
    }

    // =========================================================================
    // KHÓA / MỞ KHÓA PHƯƠNG TIỆN
    // =========================================================================

    public function lock(Vehicle $vehicle)
    {
        if ($vehicle->isInactive()) {
            return back()->withErrors(['vehicle' => 'Xe này đã ngừng sử dụng, không thể khóa.']);
        }

        if ($vehicle->isLocked()) {
            return back()->withErrors(['vehicle' => 'Xe này đã bị khóa rồi.']);
        }

        $vehicle->update(['status' => 'locked']);

        return back()->with('success', 'Đã khóa xe ' . $vehicle->license_plate . '.');
    }

    public function unlock(Vehicle $vehicle)
    {
        if (!$vehicle->isLocked()) {
            return back()->withErrors(['vehicle' => 'Xe này không đang bị khóa.']);
        }

        $vehicle->update(['status' => 'active']);

        return back()->with('success', 'Đã mở khóa xe ' . $vehicle->license_plate . '.');
    }
}
