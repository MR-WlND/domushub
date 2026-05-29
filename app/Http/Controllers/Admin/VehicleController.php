<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('apartment')->latest()->get();
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function approve(Vehicle $vehicle)
    {
        $vehicle->update(['status' => 'approved']);
        return back()->with('success', 'Đã duyệt xe ' . $vehicle->license_plate);
    }

    public function reject(Vehicle $vehicle)
    {
        $vehicle->update(['status' => 'rejected']);
        return back()->with('success', 'Đã từ chối xe ' . $vehicle->license_plate);
    }
}
