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
     * Danh sách xe
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
     * Lưu đăng ký xe
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // CHECK CĂN HỘ
        if (empty($user->apartment_id)) {
            return back()
                ->withInput()
                ->withErrors([
                    'apartment' => 'Tài khoản chưa được gắn căn hộ.'
                ]);
        }

        // VALIDATE
        $validated = $request->validate([
            'license_plate' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Za-z0-9\-\.]+$/',

                Rule::unique('vehicles', 'license_plate')
                    ->whereNull('deleted_at'),
            ],

            'vehicle_type' => [
                'required',
                'in:xe điện,xe máy,ô tô'
            ],

            'brand' => [
                'nullable',
                'string',
                'max:50'
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
        ]);

        // UPLOAD ẢNH
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request
                ->file('image')
                ->store('vehicles', 'public');
        }

        // CREATE
        Vehicle::create([
            'apartment_id' => $user->apartment_id,

            'license_plate' => strtoupper(
                $validated['license_plate']
            ),

            'vehicle_type' => $validated['vehicle_type'],

            'brand' => $validated['brand'] ?? null,

            'image' => $imagePath,

            'status' => 'pending',

            'qr_code' => null,
        ]);

        return redirect()
            ->route('resident.vehicles.index')
            ->with(
                'success',
                'Đăng ký xe thành công. Vui lòng chờ admin duyệt.'
            );
    }

    /**
     * Hủy đăng ký xe
     */
    public function destroy(Vehicle $vehicle)
    {
        $user = Auth::user();

        // CHECK QUYỀN
        if ($vehicle->apartment_id != $user->apartment_id) {
            abort(403);
        }

        // CHỈ XÓA KHI PENDING
        if ($vehicle->status !== 'pending') {
            return back()->withErrors([
                'vehicle' => 'Chỉ có thể hủy xe đang chờ duyệt.'
            ]);
        }

        $vehicle->delete();

        return back()->with(
            'success',
            'Đã hủy đăng ký xe.'
        );
    }
}
