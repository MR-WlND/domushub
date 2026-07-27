<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityBooking;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function index(Request $request)
    {
        $query = FacilityBooking::with(['facility', 'user'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        $bookings  = $query->paginate(15)->withQueryString();
        $facilities = Facility::where('status', 'active')->orderBy('name')->get();

        return view('receptionist.amenities.index', compact('bookings', 'facilities'));
    }

    public function approveBooking($id)
    {
        $booking = FacilityBooking::findOrFail($id);
        $booking->update(['status' => 'approved']);

        return back()->with('success', 'Đã duyệt đặt lịch tiện ích.');
    }

    public function rejectBooking(Request $request, $id)
    {
        $booking = FacilityBooking::findOrFail($id);
        $booking->update([
            'status'          => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return back()->with('success', 'Đã từ chối đặt lịch tiện ích.');
    }
}
