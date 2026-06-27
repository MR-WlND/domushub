<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleCheckoutController extends Controller
{
    /**
     * View quét QR xe ra
     */
    public function index()
    {
        return view('security.vehicle-checkout.index');
    }

    /**
     * Scan QR xe ra — trả JSON
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $token = trim($request->qr_token);

        $vehicle = Vehicle::with(['apartment.floor.block', 'parkingLot'])
            ->where(function ($q) use ($token) {
                $cleanToken = str_replace([' ', '-'], '', $token);
                $q->where('qr_code', $token)
                  ->orWhere('qr_code', 'qr/vehicles/' . $token . '.svg')
                  ->orWhereRaw("REPLACE(REPLACE(license_plate, ' ', ''), '-', '') = ?", [$cleanToken]);
            })
            ->withoutTrashed()
            ->first();

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phương tiện với mã QR này.',
            ], 404);
        }

        // Kiểm tra xe có đang ở trong không
        $lastLog = VehicleLog::where('vehicle_id', $vehicle->id)->latest()->first();

        if (!$lastLog || $lastLog->status !== 'inside') {
            return response()->json([
                'success' => false,
                'message' => 'Xe không có trong bãi — không thể check-out.',
                'vehicle' => $this->vehicleInfo($vehicle),
            ], 409);
        }

        return response()->json([
            'success'        => true,
            'vehicle'        => $this->vehicleInfo($vehicle),
            'check_in_at'    => $lastLog->check_in_at?->format('H:i d/m/Y'),
            'duration'       => $this->formatDuration($lastLog->check_in_at),
            'log_id'         => $lastLog->id,
        ]);
    }

    /**
     * Xác nhận xe ra — update VehicleLog
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $vehicle = Vehicle::where(function ($q) use ($request) {
                $token = trim($request->qr_token);
                $cleanToken = str_replace([' ', '-'], '', $token);
                $q->where('qr_code', $token)
                  ->orWhere('qr_code', 'qr/vehicles/' . $token . '.svg')
                  ->orWhereRaw("REPLACE(REPLACE(license_plate, ' ', ''), '-', '') = ?", [$cleanToken]);
            })
            ->withoutTrashed()
            ->firstOrFail();

        $lastLog = VehicleLog::where('vehicle_id', $vehicle->id)
            ->where('status', 'inside')
            ->latest()
            ->first();

        if (!$lastLog) {
            return response()->json(['success' => false, 'message' => 'Xe không ở trong bãi.'], 409);
        }

        $lastLog->update([
            'checked_out_by' => Auth::id(),
            'check_out_at'   => now(),
            'status'         => 'outside',
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Đã ghi nhận xe ra lúc ' . now()->format('H:i d/m/Y') . '.',
            'duration' => $this->formatDuration($lastLog->check_in_at),
        ]);
    }

    // =========================================================================
    // PRIVATE
    // =========================================================================

    private function vehicleInfo(Vehicle $vehicle): array
    {
        $apartment = $vehicle->apartment;
        $block     = $apartment?->floor?->block;

        return [
            'id'            => $vehicle->id,
            'license_plate' => $vehicle->license_plate,
            'vehicle_type'  => $vehicle->typeLabel(),
            'brand'         => $vehicle->brand,
            'status_label'  => $vehicle->statusLabel(),
            'apartment'     => $apartment ? $apartment->apartment_number : '—',
            'block'         => $block ? $block->name : '—',
            'parking_lot'   => $vehicle->parkingLot?->lot_number,
            'image'         => $vehicle->image ? asset('storage/' . $vehicle->image) : null,
        ];
    }

    private function formatDuration(?\Carbon\Carbon $from): string
    {
        if (!$from) return '—';
        $diff = now()->diffAsCarbonInterval($from);
        $h = $diff->hours + ($diff->days * 24);
        $m = $diff->minutes;
        return "{$h} giờ {$m} phút";
    }
}
