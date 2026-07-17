<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleLog;
use Illuminate\Http\Request;

class VehicleLogController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleLog::with(['vehicle.apartment.floor.block', 'checkedInBy', 'checkedOutBy'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Tìm xe cư dân
                $q->whereHas('vehicle', function($vq) use ($search) {
                    $vq->where('license_plate', 'like', "%{$search}%")
                       ->orWhereHas('apartment', function($aq) use ($search) {
                           $aq->where('apartment_number', 'like', "%{$search}%");
                       });
                })
                // Tìm xe vãng lai
                ->orWhere('guest_plate', 'like', "%{$search}%")
                ->orWhere('guest_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('check_in_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('check_in_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('admin.vehicle-logs.index', compact('logs'));
    }
}
