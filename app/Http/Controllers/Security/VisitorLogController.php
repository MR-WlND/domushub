<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Visitor::with(['apartment.floor.block', 'registeredBy', 'confirmedByResident'])
            ->latest('created_at');

        // Filter theo ngày
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            // Mặc định: hôm nay
            $query->whereDate('created_at', today());
        }

        // Filter theo trạng thái
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Tìm theo tên / biển số
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('guest_name', 'like', "%{$q}%")
                    ->orWhere('guest_phone', 'like', "%{$q}%")
                    ->orWhere('vehicle_plate', 'like', "%{$q}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total'       => Visitor::whereDate('created_at', today())->count(),
            'checked_in'  => Visitor::whereDate('created_at', today())->where('status', 'checked_in')->count(),
            'checked_out' => Visitor::whereDate('created_at', today())->where('status', 'checked_out')->count(),
            'walk_in'     => Visitor::whereDate('created_at', today())->where('walk_in', true)->count(),
        ];

        return view('security.visitor-log.index', compact('logs', 'stats'));
    }
}