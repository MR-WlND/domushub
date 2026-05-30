<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /**
     * Danh sách xe của cư dân
     */
    public function index()
    {
        $user = Auth::user();

        $vehicles = Vehicle::where('apartment_id', $user->apartment_id)
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
     * Lưu đăng ký xe (Đã xử lý lỗi trùng lặp)
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
            'vehicle_type'  => ['required', 'in:xe điện,xe máy,ô tô'],
            'brand'         => ['nullable', 'string', 'max:50'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $licensePlate = strtoupper($validated['license_plate']);

        // 3. Xử lý ảnh
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('vehicles', 'public');
        }

        // 4. Kiểm tra xem biển số đã tồn tại hay chưa
        // Tìm xe đã từng bị từ chối (rejected) hoặc đã xóa (soft deleted)
        $existingVehicle = Vehicle::withTrashed()
            ->where('license_plate', $licensePlate)
            ->first();

        if ($existingVehicle) {
            // Nếu xe đã tồn tại (dù bị từ chối hay đã xóa), ta cập nhật lại trạng thái thành pending
            $existingVehicle->update([
                'status'       => 'pending',
                'vehicle_type' => $validated['vehicle_type'],
                'brand'        => $validated['brand'] ?? $existingVehicle->brand,
                'image'        => $imagePath ?? $existingVehicle->image,
                'deleted_at'   => null, // Khôi phục nếu trước đó đã bị xóa mềm
            ]);
        } else {
            // Nếu chưa tồn tại, tạo mới hoàn toàn
            Vehicle::create([
                'apartment_id'  => $user->apartment_id,
                'license_plate' => $licensePlate,
                'vehicle_type'  => $validated['vehicle_type'],
                'brand'         => $validated['brand'] ?? null,
                'image'         => $imagePath,
                'status'        => 'pending',
                'qr_code'       => null,
            ]);
        }

        return redirect()->route('resident.vehicles.index')
            ->with('success', 'Đăng ký xe thành công. Vui lòng chờ Admin duyệt.');
    }

    /**
     * Hủy đăng ký xe
     */
    public function destroy(Vehicle $vehicle)
    {
        $user = Auth::user();

        if ($vehicle->apartment_id != $user->apartment_id) {
            abort(403);
        }

        if ($vehicle->status !== 'pending') {
            return back()->withErrors(['vehicle' => 'Chỉ có thể hủy xe đang chờ duyệt.']);
        }

        $vehicle->delete();

        return back()->with('success', 'Đã hủy đăng ký xe.');
    }
}
