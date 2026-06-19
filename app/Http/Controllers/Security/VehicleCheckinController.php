<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleCheckinController extends Controller
{
    /**
     * View quét QR xe vào
     */
    public function index()
    {
        return view('security.vehicle-checkin.index');
    }

    /**
     * Xử lý scan QR xe vào — trả JSON
     * Payload: { qr_token: "VH-{id}-{plate}" }
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $token = trim($request->qr_token);

        // Tìm xe theo qr_code lưu trong DB hoặc theo biển số xe
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

        // Kiểm tra trạng thái
        if (!$vehicle->canEnter()) {
            return response()->json([
                'success' => false,
                'message' => match ($vehicle->status) {
                    'pending'  => 'Xe chưa được Ban quản lý phê duyệt.',
                    'locked'   => 'Xe đang bị khóa — không thể vào bãi.',
                    'inactive' => 'Xe đã ngừng sử dụng.',
                    default    => 'Phương tiện không hợp lệ.',
                },
                'vehicle' => $this->vehicleInfo($vehicle),
            ], 403);
        }

        // Kiểm tra xe đã ở trong chưa?
        $lastLog = VehicleLog::where('vehicle_id', $vehicle->id)->latest()->first();
        if ($lastLog && $lastLog->status === 'inside') {
            return response()->json([
                'success' => false,
                'message' => 'Xe đang ở trong bãi — không thể check-in lần nữa.',
                'vehicle' => $this->vehicleInfo($vehicle),
                'last_checkin' => $lastLog->check_in_at?->format('d/m/Y H:i'),
            ], 409);
        }

        return response()->json([
            'success'        => true,
            'can_enter'      => true,
            'warning'        => $vehicle->status === 'pending_renewal' ? 'Xe chờ gia hạn phí — cần nhắc nhở cư dân.' : null,
            'vehicle'        => $this->vehicleInfo($vehicle),
        ]);
    }

    /**
     * Xác nhận xe vào — tạo VehicleLog
     */
    public function checkin(Request $request)
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

        if (!$vehicle->canEnter()) {
            return response()->json(['success' => false, 'message' => 'Xe không được phép vào.'], 403);
        }

        $lastLog = VehicleLog::where('vehicle_id', $vehicle->id)->latest()->first();
        if ($lastLog && $lastLog->status === 'inside') {
            return response()->json(['success' => false, 'message' => 'Xe đang ở trong bãi.'], 409);
        }

        VehicleLog::create([
            'vehicle_id'    => $vehicle->id,
            'checked_in_by' => Auth::id(),
            'check_in_at'   => now(),
            'qr_code'       => $request->qr_token,
            'status'        => 'inside',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã ghi nhận xe vào lúc ' . now()->format('H:i d/m/Y') . '.',
        ]);
    }

    // =========================================================================
    // PRIVATE
    // =========================================================================

    private function vehicleInfo(Vehicle $vehicle): array
    {
        $apartment  = $vehicle->apartment;
        $block      = $apartment?->floor?->block;

        return [
            'id'            => $vehicle->id,
            'license_plate' => $vehicle->license_plate,
            'vehicle_type'  => $vehicle->typeLabel(),
            'brand'         => $vehicle->brand,
            'status'        => $vehicle->status,
            'status_label'  => $vehicle->statusLabel(),
            'apartment'     => $apartment ? $apartment->unit_number : '—',
            'block'         => $block ? $block->name : '—',
            'parking_lot'   => $vehicle->parkingLot?->lot_number,
            'image'         => $vehicle->image ? asset('storage/' . $vehicle->image) : null,
        ];
    }
}
