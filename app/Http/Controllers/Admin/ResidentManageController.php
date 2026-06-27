<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResidentManageController extends Controller
{
    /**
     * Danh sách cư dân (có lọc theo tòa, trạng thái, tìm kiếm).
     */
    public function index(Request $request)
    {
        $request->validate([
            'block_id' => 'nullable|integer|exists:blocks,id',
            'status'   => 'nullable|in:active,inactive,banned',
            'search'   => 'nullable|string|max:200',
        ]);

        $query = User::where('role', 'resident')
            ->with(['apartment.floor.block'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('block_id')) {
            $query->whereHas('apartment.floor', fn($q) => $q->where('block_id', $request->block_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cccd', 'like', "%{$search}%")
                  ->orWhereHas('apartment', fn($aq) => $aq->where('apartment_number', 'like', "%{$search}%"));
            });
        }

        $residents = $query->paginate(20)->withQueryString();

        $blocks = Block::orderBy('name')->get();

        // Stats
        $baseQuery = User::where('role', 'resident');
        $stats = [
            'total'    => (clone $baseQuery)->count(),
            'active'   => (clone $baseQuery)->where('status', 'active')->count(),
            'inactive' => (clone $baseQuery)->where('status', 'inactive')->count(),
            'banned'   => (clone $baseQuery)->where('status', 'banned')->count(),
        ];

        // AJAX: return partial
        if ($request->ajax() || $request->wantsJson()) {
            $html = view('admin.residents._table', compact('residents'))->render();
            return response()->json([
                'success' => true,
                'html'    => $html,
                'stats'   => $stats,
            ]);
        }

        return view('admin.residents.index', compact('residents', 'blocks', 'stats'));
    }

    /**
     * Xóa mềm cư dân khỏi phòng (Admin thực hiện).
     * Kích hoạt SoftDeletes trên bảng residents.
     * Apartment model tự động cập nhật trạng thái thông qua Resident boot event.
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $resident = Resident::findOrFail($id);
        $apartmentId = $resident->apartment_id;

        $resident->delete(); // SoftDeletes → cột deleted_at được set

        return redirect()
            ->route('admin.apartments.show', $apartmentId)
            ->with('success', 'Đã gỡ cư dân khỏi phòng thành công. Lịch sử vẫn được giữ lại trong hệ thống.');
    }
}
