<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalkInVisitorController extends Controller
{
    public function index(Request $request)
    {
        $currentVisitors = Visitor::with(['apartment.floor.block', 'registeredBy', 'confirmedByResident'])
            ->where('status', 'checked_in')
            ->latest('check_in_at')
            ->get();

        $apartments = Apartment::with('floor.block')
            ->orderBy('apartment_number')
            ->get();

        return view('security.walk-in.index', compact('currentVisitors', 'apartments'));
    }

    public function getResidents(Request $request)
    {
        $request->validate(['apartment_id' => ['required', 'integer']]);

        $owners = \App\Models\Resident::with('user')
            ->where('apartment_id', $request->apartment_id)
            ->where('relationship', 'owner')
            ->get()
            ->filter(fn ($r) => $r->user && $r->user->status === 'active')
            ->map(fn ($r) => [
                'id'    => $r->user->id,
                'name'  => $r->user->name,
                'phone' => $r->user->phone,
            ])
            ->values();

        return response()->json(['residents' => $owners]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_name'            => ['required', 'string', 'max:100'],
            'guest_phone'           => ['nullable', 'string', 'max:20'],
            'apartment_id'          => ['required', 'exists:apartments,id'],
            'resident_to_meet'      => ['required', 'string', 'max:100'],
            'confirmed_by_resident' => ['nullable', 'exists:users,id'],
            'note'                  => ['nullable', 'string', 'max:500'],
            'vehicle_plate'         => ['nullable', 'string', 'max:20'],
            'vehicle_type'          => ['nullable', 'in:car,motorbike,electric_bike'],
        ]);

        $visitor = Visitor::create([
            'apartment_id'          => $request->apartment_id,
            'registered_by'         => Auth::id(),
            'guest_name'            => $request->guest_name,
            'guest_phone'           => $request->guest_phone,
            'qr_token'              => Visitor::generateToken(),
            'expired_at'            => now()->addDay(),
            'status'                => 'checked_in',
            'check_in_at'           => now(),
            'check_in_by'           => Auth::id(),
            'note'                  => $request->note,
            'vehicle_plate'         => $request->vehicle_plate ? strtoupper(trim($request->vehicle_plate)) : null,
            'vehicle_type'          => $request->vehicle_plate ? $request->vehicle_type : null,
            'walk_in'               => true,
            'resident_to_meet'      => $request->resident_to_meet,
            'confirmed_by_resident' => $request->confirmed_by_resident,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã ghi nhận khách vào lúc ' . now()->format('H:i d/m/Y') . '.',
            'visitor' => $this->visitorInfo($visitor->load(['apartment.floor.block', 'confirmedByResident'])),
        ]);
    }

    /**
     * Ghi nhận khách ra (check-out)
     */
    public function checkout(Request $request)
    {
        $request->validate(['visitor_id' => ['required', 'integer']]);

        $visitor = Visitor::where('id', $request->visitor_id)
            ->where('status', 'checked_in')
            ->firstOrFail();

        $visitor->update([
            'status'       => 'checked_out',
            'check_out_at' => now(),
            'check_out_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã ghi nhận khách ra lúc ' . now()->format('H:i d/m/Y') . '.',
        ]);
    }

    // =========================================================================
    // PRIVATE
    // =========================================================================

    private function visitorInfo(Visitor $visitor): array
    {
        $apartment = $visitor->apartment;
        $block     = $apartment?->floor?->block;

        return [
            'id'                    => $visitor->id,
            'guest_name'            => $visitor->guest_name,
            'guest_phone'           => $visitor->guest_phone,
            'apartment'             => $apartment?->apartment_number ?? '—',
            'block'                 => $block?->name ?? '—',
            'resident_to_meet'      => $visitor->resident_to_meet,
            'confirmed_by_resident' => $visitor->confirmedByResident?->name,
            'note'                  => $visitor->note,
            'status'                => $visitor->status,
            'status_label'          => $visitor->statusLabel(),
            'check_in_at'           => $visitor->check_in_at?->format('H:i d/m/Y'),
            'walk_in'               => $visitor->walk_in,
            'has_vehicle'           => $visitor->hasVehicle(),
            'vehicle_plate'         => $visitor->vehicle_plate,
            'vehicle_type'          => $visitor->hasVehicle() ? $visitor->vehicleTypeLabel() : null,
        ];
    }
}
