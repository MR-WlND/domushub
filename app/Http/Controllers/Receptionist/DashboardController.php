<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use App\Models\Ticket;
use App\Models\FacilityBooking;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingParcels   = Parcel::where('status', 'pending')->count();
        $todayParcels     = Parcel::whereDate('arrived_at', today())->count();
        $pendingTickets   = Ticket::whereIn('status', ['pending', 'assigned'])->count();
        $pendingBookings  = FacilityBooking::where('status', 'pending')->count();
        $visitorsInside   = \App\Models\Visitor::where('status', 'checked_in')->count();
        $todayVisitors    = \App\Models\Visitor::whereDate('check_in_at', today())->count();

        $recentParcels = Parcel::with('apartment')
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = Ticket::with(['apartment.floor.block', 'sender'])
            ->whereIn('status', ['pending', 'assigned'])
            ->latest()
            ->take(5)
            ->get();

        return view('receptionist.dashboard.index', compact(
            'pendingParcels', 'todayParcels',
            'pendingTickets', 'pendingBookings',
            'visitorsInside', 'todayVisitors',
            'recentParcels', 'recentTickets'
        ));
    }
}
