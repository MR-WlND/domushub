<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\ParkingLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    /**
     * Danh sách xe của cư dân (bao gồm đầy đủ 5 trạng thái)
     */
    public function index()
    {
        $user = Auth::user();

        $vehicles = Vehicle::with('parkingLot')
            ->where('apartment_id', $user->apartment_id)
            ->whereIn('status', ['pending', 'active', 'pending_renewal', 'locked'])
            ->withoutTrashed()
            ->latest()
            ->get();

        return view('resident.vehicles.index', compact('vehicles'));
    }

    /**
     * Form thêm xe
     */
    public function create()
    {
        return view('resident.vehicles.create');
    }

    /**
     * Lưu đăng ký xe mới
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Kiểm tra căn hộ
        if (empty($user->apartment_id)) {
            return back()->withInput()->withErrors([
                'apartment' => 'Tài khoản chưa được gắn căn hộ.'
            ]);
        }

        // 2. Validate
        $validated = $request->validate([
            'license_plate' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\-\.]+$/'],
            'vehicle_type'  => ['required', 'in:motorbike,electric_bike,car'],
            'brand'         => ['nullable', 'string', 'max:50'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $licensePlate = strtoupper($validated['license_plate']);

        // Kiểm tra biển số xe đã tồn tại với trạng thái đang dùng (không tính xe đã xóa)
        $existingVehicle = Vehicle::where('license_plate', $licensePlate)
            ->whereIn('status', ['pending', 'active', 'pending_renewal', 'locked'])
            ->withoutTrashed()
            ->first();

        if ($existingVehicle) {
            return back()->withInput()->withErrors([
                'license_plate' => 'Biển số xe này đã được đăng ký trong hệ thống.'
            ]);
        }

        // 3. Logic theo loại xe
        $status      = 'active';
        $parkingLotId = null;

        if (in_array($validated['vehicle_type'], ['motorbike', 'electric_bike'])) {
            $limit = \App\Models\SystemSetting::get('max_motorbike_per_apartment', 3);

            // Xe máy/điện: Giới hạn theo SystemSetting (đếm cả locked, pending_renewal)
            $currentMotoCount = Vehicle::where('apartment_id', $user->apartment_id)
                ->whereIn('vehicle_type', ['motorbike', 'electric_bike'])
                ->whereIn('status', ['pending', 'active', 'pending_renewal', 'locked'])
                ->count();

            if ($currentMotoCount >= $limit) {
                return back()->withInput()->withErrors([
                    'vehicle_type' => "Căn hộ của bạn đã đăng ký tối đa {$limit} xe máy/xe điện."
                ]);
            }

            // Xe máy mới đăng ký → chờ admin duyệt
            $status = 'pending';

        } elseif ($validated['vehicle_type'] === 'car') {
            $limit = \App\Models\SystemSetting::get('max_car_per_apartment', 1);

            $currentCarCount = Vehicle::where('apartment_id', $user->apartment_id)
                ->where('vehicle_type', 'car')
                ->whereIn('status', ['pending', 'active', 'pending_renewal', 'locked'])
                ->count();

            if ($currentCarCount >= $limit) {
                return back()->withInput()->withErrors([
                    'vehicle_type' => "Căn hộ của bạn đã đăng ký tối đa {$limit} ô tô."
                ]);
            }

            // Ô tô: Kiểm tra còn chỗ đỗ không
            $availableLot = ParkingLot::where('lot_type', 'car')
                ->where('status', 'available')
                ->exists();

            if (!$availableLot) {
                return back()->withInput()->withErrors([
                    'vehicle_type' => 'Hầm hiện tại đã hết chỗ đỗ xe ô tô. Vui lòng liên hệ Ban quản lý.'
                ]);
            }

            // Ô tô phải chờ admin cấp lốt
            $status = 'pending';
        }

        // 4. Xử lý ảnh
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('vehicles', 'public');
            // Validate ảnh được lưu thành công
            if (!$imagePath) {
                return back()->withInput()->withErrors([
                    'image' => 'Không thể lưu ảnh. Vui lòng thử lại.'
                ]);
            }
        }

        // 5. Lưu xe mới
        Vehicle::create([
            'apartment_id'   => $user->apartment_id,
            'license_plate'  => $licensePlate,
            'vehicle_type'   => $validated['vehicle_type'],
            'brand'          => $validated['brand'] ?? null,
            'image'          => $imagePath,
            'status'         => $status,
            'parking_lot_id' => $parkingLotId,
            'qr_code'        => null,
        ]);

        $message = 'Đăng ký phương tiện thành công. Vui lòng chờ Ban quản lý duyệt.';

        return redirect()->route('resident.vehicles.index')->with('success', $message);
    }

    /**
     * Hủy đăng ký xe (chỉ cho phép khi xe không bị khóa)
     */
    public function destroy(Vehicle $vehicle)
    {
        $user = Auth::user();

        // Kiểm tra quyền sở hữu
        if ($vehicle->apartment_id != $user->apartment_id) {
            abort(403);
        }

        // Không cho phép hủy xe đang bị khóa — phải liên hệ admin
        if ($vehicle->isLocked()) {
            return back()->withErrors([
                'vehicle' => 'Xe đang bị khóa, không thể tự hủy. Vui lòng liên hệ Ban quản lý.'
            ]);
        }

        if ($vehicle->isInactive()) {
            return back()->withErrors(['vehicle' => 'Xe này đã được hủy trước đó.']);
        }

        // Hủy xe → inactive
        $vehicle->update(['status' => 'inactive']);

        // Giải phóng lốt đỗ nếu có (trường hợp ô tô active bị cư dân hủy)
        if ($vehicle->parking_lot_id) {
            ParkingLot::where('id', $vehicle->parking_lot_id)->update([
                'status'       => 'available',
                'apartment_id' => null,
            ]);
            $vehicle->update(['parking_lot_id' => null]);
        }

        return back()->with('success', 'Đã hủy đăng ký phương tiện thành công.');
    }

    /**
     * Hiển thị mã QR cho xe (trang riêng dùng cho cư dân scan tại cổng)
     */
    public function showQr(Vehicle $vehicle)
    {
        $user = Auth::user();

        // Kiểm tra quyền sở hữu
        if ($vehicle->apartment_id != $user->apartment_id) {
            abort(403);
        }

        // Chỉ hiện QR cho xe active hoặc pending_renewal (đã có QR)
        if (!in_array($vehicle->status, ['active', 'pending_renewal'])) {
            return back()->withErrors(['vehicle' => 'Xe chưa được cấp mã QR.']);
        }

        if (empty($vehicle->qr_code)) {
            return back()->withErrors(['vehicle' => 'Xe chưa có mã QR. Vui lòng liên hệ Ban quản lý.']);
        }

        return view('resident.vehicles.qr', compact('vehicle'));
    }
}
