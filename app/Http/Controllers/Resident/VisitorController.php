<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitorController extends Controller
{
    /**
     * Danh sách khách đến thăm căn hộ (do bảo vệ đăng ký walk-in)
     */
    public function index()
    {
        $user = Auth::user();

        if (empty($user->apartment_id)) {
            return view('resident.visitors.index', ['visitors' => collect()]);
        }

        $visitors = Visitor::with(['registeredBy', 'confirmedByResident'])
            ->where('apartment_id', $user->apartment_id)
            ->orderByDesc('created_at')
            ->get();

        return view('resident.visitors.index', compact('visitors'));
    }

    /**
     * Chi tiết khách: ảnh + thông tin + nút chấp nhận / từ chối
     */
    public function show($id)
    {
        $user    = Auth::user();
        $visitor = Visitor::with(['apartment.floor.block', 'registeredBy', 'confirmedByResident'])
            ->where('id', $id)
            ->where('apartment_id', $user->apartment_id)
            ->firstOrFail();

        return view('resident.visitors.show', compact('visitor'));
    }

    /**
     * Cư dân chấp nhận khách vào (ghi nhận confirmed_by_resident + cho phép vào)
     */
    public function approve($id)
    {
        $user    = Auth::user();
        $visitor = Visitor::where('id', $id)
            ->where('apartment_id', $user->apartment_id)
            ->firstOrFail();

        if (!in_array($visitor->status, ['checked_in', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể chấp nhận khách ở trạng thái này.',
            ], 422);
        }

        $visitor->update([
            'confirmed_by_resident' => $user->id,
            'status'                => 'checked_in',
            'check_in_at'           => now(),
            'check_in_by'           => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã chấp nhận khách. Bảo vệ sẽ cho khách vào.',
        ]);
    }

    /**
     * Cư dân từ chối / hủy khách
     */
    public function reject($id)
    {
        $user    = Auth::user();
        $visitor = Visitor::where('id', $id)
            ->where('apartment_id', $user->apartment_id)
            ->firstOrFail();

        if ($visitor->status === 'checked_out') {
            return response()->json([
                'success' => false,
                'message' => 'Khách đã ra khỏi tòa nhà rồi.',
            ], 422);
        }

        if ($visitor->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Khách đã bị từ chối trước đó.',
            ], 422);
        }

        $visitor->update([
            'status'               => 'cancelled',
            'confirmed_by_resident' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối khách. Bảo vệ sẽ được thông báo.',
        ]);
    }
}