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
        $cleanToken = str_replace([' ', '-'], '', $token);

        // 1. Tìm xe cư dân trước
        $vehicle = Vehicle::with(['apartment.floor.block', 'parkingLot'])
            ->where(function ($q) use ($token, $cleanToken) {
                $q->where('qr_code', $token)
                  ->orWhere('qr_code', 'qr/vehicles/' . $token . '.svg')
                  ->orWhereRaw("REPLACE(REPLACE(license_plate, ' ', ''), '-', '') = ?", [$cleanToken]);
            })
            ->withoutTrashed()
            ->first();

        if ($vehicle) {
            // Xe cư dân — logic cũ
            $lastLog = VehicleLog::where('vehicle_id', $vehicle->id)->latest()->first();

            if (!$lastLog || $lastLog->status !== 'inside') {
                return response()->json([
                    'success' => false,
                    'message' => 'Xe không có trong bãi — không thể check-out.',
                    'vehicle' => $this->vehicleInfo($vehicle),
                ], 409);
            }

            return response()->json([
                'success'     => true,
                'is_guest'    => false,
                'vehicle'     => $this->vehicleInfo($vehicle),
                'check_in_at' => $lastLog->check_in_at?->format('H:i d/m/Y'),
                'duration'    => $this->formatDuration($lastLog->check_in_at),
                'log_id'      => $lastLog->id,
            ]);
        }

        // 2. Tìm xe vãng lai theo biển số
        $guestLog = VehicleLog::where('is_guest', true)
            ->where('status', 'inside')
            ->whereRaw("REPLACE(REPLACE(guest_plate, ' ', ''), '-', '') = ?", [$cleanToken])
            ->latest()
            ->first();

        if ($guestLog) {
            return response()->json([
                'success'     => true,
                'is_guest'    => true,
                'vehicle'     => [
                    'license_plate' => $guestLog->guest_plate,
                    'vehicle_type'  => match($guestLog->guest_vehicle_type) {
                        'car' => 'Ô tô', 'motorbike' => 'Xe máy', 'electric_bike' => 'Xe điện', default => $guestLog->guest_vehicle_type
                    },
                    'brand'         => null,
                    'status_label'  => 'Khách vãng lai',
                    'apartment'     => '—',
                    'block'         => '—',
                    'parking_lot'   => null,
                    'image'         => null,
                    'guest_name'    => $guestLog->guest_name,
                    'guest_phone'   => $guestLog->guest_phone,
                ],
                'check_in_at'  => $guestLog->check_in_at?->format('H:i d/m/Y'),
                'duration'     => $this->formatDuration($guestLog->check_in_at),
                'log_id'       => $guestLog->id,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy phương tiện nào đang ở trong bãi với mã/biển số này.',
        ], 404);
    }

    /**
     * Xác nhận xe ra — update VehicleLog
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
            'log_id'   => ['nullable', 'integer'],
        ]);

        $token = trim($request->qr_token);
        $cleanToken = str_replace([' ', '-'], '', $token);

        // Nếu có log_id cụ thể (xe vãng lai)
        if ($request->log_id) {
            $log = VehicleLog::where('id', $request->log_id)
                ->where('status', 'inside')
                ->first();

            if (!$log) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy lượt gửi xe.'], 404);
            }

            $log->update([
                'checked_out_by' => Auth::id(),
                'check_out_at'   => now(),
                'status'         => 'outside',
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Đã ghi nhận xe ra lúc ' . now()->format('H:i d/m/Y') . '.',
                'duration' => $this->formatDuration($log->check_in_at),
            ]);
        }

        // Xe cư dân — logic cũ
        $vehicle = Vehicle::where(function ($q) use ($token, $cleanToken) {
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
