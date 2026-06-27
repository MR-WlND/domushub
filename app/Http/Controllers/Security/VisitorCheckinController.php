<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitorCheckinController extends Controller
{
    /**
     * View quét QR khách
     */
    public function index()
    {
        return view('security.visitor-check.index');
    }

    /**
     * Scan QR khách — xác thực token, trả JSON thông tin
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $token = trim($request->qr_token);

        $visitor = Visitor::with(['apartment.floor.block', 'registeredBy'])
            ->where('qr_token', $token)
            ->first();

        if (!$visitor) {
            return response()->json([
                'success' => false,
                'message' => 'Mã QR không tồn tại hoặc không hợp lệ.',
            ], 404);
        }

        // Tự động hết hạn
        if ($visitor->status === 'pending' && $visitor->expired_at->isPast()) {
            $visitor->update(['status' => 'expired']);
            $visitor->refresh();
        }

        $info = $this->visitorInfo($visitor);

        if ($visitor->status === 'expired') {
            return response()->json([
                'success'  => false,
                'message'  => 'QR đã hết hạn lúc ' . $visitor->expired_at->format('H:i d/m/Y') . '.',
                'visitor'  => $info,
            ], 410);
        }

        if ($visitor->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'QR đã bị hủy bởi cư dân.',
                'visitor' => $info,
            ], 403);
        }

        if ($visitor->status === 'checked_out') {
            return response()->json([
                'success' => false,
                'message' => 'Khách đã ra lúc ' . $visitor->check_out_at?->format('H:i d/m/Y') . '. QR chỉ sử dụng được 1 lần.',
                'visitor' => $info,
            ], 409);
        }

        if ($visitor->status === 'checked_in') {
            return response()->json([
                'success'       => true,
                'action'        => 'checkout',
                'message'       => 'Khách đang ở trong tòa nhà — xác nhận cho ra?',
                'visitor'       => $info,
                'check_in_at'   => $visitor->check_in_at?->format('H:i d/m/Y'),
            ]);
        }

        // pending → hợp lệ → cho vào
        return response()->json([
            'success'  => true,
            'action'   => 'checkin',
            'message'  => 'QR hợp lệ — xác nhận cho vào.',
            'visitor'  => $info,
        ]);
    }

    /**
     * Ghi nhận khách vào
     */
    public function checkin(Request $request)
    {
        $request->validate(['qr_token' => ['required', 'string']]);

        $visitor = Visitor::where('qr_token', trim($request->qr_token))->firstOrFail();

        if (!$visitor->isPending() || $visitor->isExpired()) {
            return response()->json(['success' => false, 'message' => 'QR không còn hợp lệ.'], 422);
        }

        $visitor->update([
            'status'       => 'checked_in',
            'check_in_at'  => now(),
            'check_in_by'  => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã ghi nhận khách vào lúc ' . now()->format('H:i d/m/Y') . '.',
        ]);
    }

    /**
     * Ghi nhận khách ra
     */
    public function checkout(Request $request)
    {
        $request->validate(['qr_token' => ['required', 'string']]);

        $visitor = Visitor::where('qr_token', trim($request->qr_token))->firstOrFail();

        if ($visitor->status !== 'checked_in') {
            return response()->json(['success' => false, 'message' => 'Khách không đang ở trong tòa nhà.'], 422);
        }

        $visitor->update([
            'status'        => 'checked_out',
            'check_out_at'  => now(),
            'check_out_by'  => Auth::id(),
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
            'id'             => $visitor->id,
            'guest_name'     => $visitor->guest_name,
            'guest_phone'    => $visitor->guest_phone,
            'apartment'      => $apartment ? $apartment->apartment_number : '—',
            'block'          => $block ? $block->name : '—',
            'registered_by'  => $visitor->registeredBy?->name,
            'expired_at'     => $visitor->expired_at->format('H:i d/m/Y'),
            'note'           => $visitor->note,
            'status'         => $visitor->status,
            'status_label'   => $visitor->statusLabel(),
            'has_vehicle'    => $visitor->hasVehicle(),
            'vehicle_plate'  => $visitor->vehicle_plate,
            'vehicle_type'   => $visitor->hasVehicle() ? $visitor->vehicleTypeLabel() : null,
        ];
    }
}
