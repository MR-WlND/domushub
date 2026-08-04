<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Resident;
use App\Models\User;
use App\Models\Visitor;
use App\Notifications\VisitorWalkInNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VisitorController extends Controller
{
    // =========================================================================
    // WALK-IN — Đăng ký khách tại cổng
    // =========================================================================

    public function walkIn(Request $request)
    {
        $currentVisitors = Visitor::with(['apartment.floor.block', 'registeredBy', 'confirmedByResident'])
            ->where('status', 'checked_in')
            ->latest('check_in_at')
            ->get();

        $apartments = Apartment::with('floor.block')
            ->orderBy('apartment_number')
            ->get();

        return view('receptionist.walk-in.index', compact('currentVisitors', 'apartments'));
    }

    public function getResidents(Request $request)
    {
        $request->validate(['apartment_id' => ['required', 'integer']]);

        $aptId = (int) $request->apartment_id;

        $ownerResident = Resident::with('user')
            ->where('apartment_id', $aptId)
            ->where('relationship', 'owner')
            ->whereNull('deleted_at')
            ->get()
            ->first(fn ($r) => $r->user && $r->user->status === 'active' && is_null($r->user->deleted_at));

        $ownerUser = $ownerResident?->user;

        if (!$ownerUser) {
            $anyResident = Resident::with('user')
                ->where('apartment_id', $aptId)
                ->whereNull('deleted_at')
                ->get()
                ->first(fn ($r) => $r->user && $r->user->status === 'active' && is_null($r->user->deleted_at));
            $ownerUser = $anyResident?->user;
        }

        if (!$ownerUser) {
            $ownerUser = User::where('apartment_id', $aptId)
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->orderBy('id', 'asc')
                ->first();
        }

        $residents = [];
        if ($ownerUser) {
            $residents[] = [
                'id'    => $ownerUser->id,
                'name'  => $ownerUser->name,
                'phone' => $ownerUser->phone ?? null,
            ];
        }

        return response()->json(['residents' => $residents]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_name'            => ['required', 'string', 'max:100'],
            'guest_phone'           => ['nullable', 'string', 'regex:/^(03|05|07|08|09)[0-9]{8}$/'],
            'apartment_id'          => ['required', 'exists:apartments,id'],
            'resident_to_meet'      => ['required', 'string', 'max:100'],
            'confirmed_by_resident' => ['nullable', 'exists:users,id'],
            'note'                  => ['nullable', 'string', 'max:500'],
            'vehicle_plate'         => ['nullable', 'string', 'max:20'],
            'vehicle_type'          => ['nullable', 'in:car,motorbike,electric_bike'],
            'face_image'            => ['nullable', 'string'],
            'notify_resident'       => ['nullable', 'boolean'],
        ], [
            'guest_phone.regex' => 'Số điện thoại phải gồm đúng 10 chữ số, bắt đầu bằng đầu số Việt Nam (03, 05, 07, 08, 09).',
        ]);

        $photoPath = null;
        if ($request->filled('face_image')) {
            $dataUrl = $request->face_image;
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $m)) {
                $ext       = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                $imageData = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1));
                $photoPath = 'visitors/photos/' . uniqid('vi_', true) . '.' . $ext;
                Storage::disk('public')->put($photoPath, $imageData);
            }
        }

        $visitor = Visitor::create([
            'apartment_id'          => $request->apartment_id,
            'registered_by'         => Auth::id(),
            'guest_name'            => $request->guest_name,
            'guest_phone'           => $request->guest_phone,
            'qr_token'              => Visitor::generateToken(),
            'expired_at'            => now()->addDay(),
            'status'                => 'pending',
            'check_in_at'           => null,
            'check_in_by'           => null,
            'note'                  => $request->note,
            'vehicle_plate'         => $request->vehicle_plate
                                        ? strtoupper(trim($request->vehicle_plate)) : null,
            'vehicle_type'          => $request->vehicle_plate ? $request->vehicle_type : null,
            'walk_in'               => true,
            'resident_to_meet'      => $request->resident_to_meet,
            'confirmed_by_resident' => null,
            'face_image'            => $photoPath,
        ]);

        $visitor->load(['apartment.floor.block']);

        $targetResidentId = $request->confirmed_by_resident;
        if ($targetResidentId) {
            $resident = User::find($targetResidentId);
            if ($resident) {
                $resident->notify(new VisitorWalkInNotification($visitor));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi thông báo cho cư dân (' . $request->resident_to_meet . '). Vui lòng chờ cư dân duyệt.',
            'visitor' => $this->visitorInfo($visitor),
        ]);
    }

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
    // VISITOR LOG — Lịch sử ra vào
    // =========================================================================

    public function log(Request $request)
    {
        $query = Visitor::with(['apartment.floor.block', 'registeredBy', 'confirmedByResident'])
            ->latest('created_at');

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            $query->whereDate('created_at', today());
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

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

        return view('receptionist.visitor-log.index', compact('logs', 'stats'));
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
            'face_image_url'        => $visitor->face_image
                                        ? asset('storage/' . $visitor->face_image)
                                        : null,
        ];
    }
}
