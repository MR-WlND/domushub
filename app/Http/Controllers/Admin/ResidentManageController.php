<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
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
            'floor_id' => 'nullable|integer|exists:floors,id',
            'search'   => 'nullable|string|max:200',
        ]);

        $query = User::where('role', 'resident')
            ->whereHas('residents')
            ->with(['apartment.floor.block', 'residents'])
            ->orderBy('apartment_id', 'asc');

        if ($request->filled('block_id')) {
            $query->whereHas('apartment.floor', fn($q) => $q->where('block_id', $request->block_id));
        }

        if ($request->filled('floor_id')) {
            $query->whereHas('apartment', fn($q) => $q->where('floor_id', $request->floor_id));
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
        $floors = Floor::with('block')->orderBy('floor_number')->get();

        // Stats (tất cả cư dân)
        $baseQuery = User::where('role', 'resident')
            ->whereHas('residents');
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

        return view('admin.residents.index', compact('residents', 'blocks', 'floors', 'stats'));
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

    /**
     * Cập nhật hình thức cư trú (Thường trú / Tạm trú / Tạm vắng).
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'temporary_status' => 'required|in:permanent,temporary,absent',
        ], [
            'temporary_status.required' => 'Vui lòng chọn hình thức cư trú.',
            'temporary_status.in' => 'Hình thức cư trú không hợp lệ.',
        ]);

        $resident = Resident::findOrFail($id);
        $resident->update([
            'temporary_status' => $validated['temporary_status'],
        ]);

        return back()->with('success', 'Trạng thái cư trú được cập nhật thành công.');
    }
}
