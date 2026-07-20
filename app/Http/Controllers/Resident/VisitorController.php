<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitorController extends Controller
{
    /**
     * Danh sách khách ghé thăm căn hộ
     */
    public function index()
    {
        $user = Auth::user();

        if (empty($user->apartment_id)) {
            return view('resident.visitors.index', ['visitors' => collect()]);
        }

        $visitors = Visitor::where('apartment_id', $user->apartment_id)
            ->orderByDesc('created_at')
            ->get();

        // Đánh dấu hết hạn tự động
        $visitors->each(function ($v) {
            if ($v->status === 'pending' && $v->expired_at->isPast()) {
                $v->update(['status' => 'expired']);
            }
        });

        return view('resident.visitors.index', compact('visitors'));
    }

    /**
     * Cư dân đồng ý cho khách vào
     */
    public function approve($id)
    {
        $user    = Auth::user();
        $visitor = Visitor::where('id', $id)
            ->where('apartment_id', $user->apartment_id)
            ->where('status', 'pending')
            ->where('walk_in', true)
            ->firstOrFail();

        $visitor->update([
            'status'                => 'checked_in',
            'check_in_at'           => now(),
            'confirmed_by_resident' => $user->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Đã đồng ý cho khách vào.']);
    }

    /**
     * Cư dân từ chối khách
     */
    public function reject($id)
    {
        $user    = Auth::user();
        $visitor = Visitor::where('id', $id)
            ->where('apartment_id', $user->apartment_id)
            ->where('status', 'pending')
            ->where('walk_in', true)
            ->firstOrFail();

        $visitor->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Đã từ chối khách.']);
    }
}
