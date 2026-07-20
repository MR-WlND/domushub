<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorLogController extends Controller
{
    public function index(Request $request)
    {
        // Only show visitors who have actually checked in
        $query = Visitor::with(['apartment.floor.block', 'checkedInBy', 'checkedOutBy'])
            ->whereNotNull('check_in_at')
            ->orderByDesc('check_in_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhere('vehicle_plate', 'like', "%{$search}%");
            })->orWhereHas('apartment', function($aq) use ($search) {
                $aq->where('apartment_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('check_in_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('check_in_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('admin.visitor-logs.index', compact('logs'));
    }
}
