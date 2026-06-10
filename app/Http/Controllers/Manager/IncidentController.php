<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    /** Danh sách tất cả phản ánh với filter */
    public function index(Request $request)
    {
        $query = Incident::with(['resident', 'apartment', 'assignedTo'])
            ->orderByRaw("FIELD(status, 'pending', 'assigned', 'in_progress', 'resolved', 'confirmed', 'closed')")
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $incidents = $query->paginate(15)->withQueryString();

        // Thống kê nhanh
        $stats = [
            'pending'     => Incident::where('status', 'pending')->count(),
            'in_progress' => Incident::whereIn('status', ['assigned', 'in_progress'])->count(),
            'resolved'    => Incident::where('status', 'resolved')->count(),
            'confirmed'   => Incident::where('status', 'confirmed')->count(),
        ];

        return view('manager.incidents.index', compact('incidents', 'stats'));
    }

    /** Xem chi tiết một phản ánh */
    public function show(int $id)
    {
        $incident = Incident::with([
            'resident', 'apartment',
            'assignedTo', 'assignedBy',
            'confirmedBy',
        ])->findOrFail($id);

        // Danh sách kỹ thuật viên để phân công
        $technicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('manager.incidents.show', compact('incident', 'technicians'));
    }

    /** Phân công kỹ thuật viên */
    public function assign(Request $request, int $id)
    {
        $incident = Incident::findOrFail($id);

        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ], [
            'technician_id.required' => 'Vui lòng chọn kỹ thuật viên.',
        ]);

        $incident->update([
            'assigned_to' => $request->technician_id,
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
            'status'      => 'assigned',
        ]);

        return redirect()
            ->route('manager.incidents.show', $id)
            ->with('success', 'Đã phân công kỹ thuật viên thành công.');
    }

    /** Quản lý xác nhận hoàn thành */
    public function confirm(int $id)
    {
        $incident = Incident::findOrFail($id);

        if ($incident->status !== 'resolved') {
            return back()->with('error', 'Phản ánh chưa được xử lý bởi kỹ thuật viên.');
        }

        $incident->update([
            'status'       => 'confirmed',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        return redirect()
            ->route('manager.incidents.show', $id)
            ->with('success', 'Đã xác nhận hoàn thành phản ánh.');
    }

    /** Đóng / từ chối phản ánh */
    public function close(Request $request, int $id)
    {
        $incident = Incident::findOrFail($id);

        $incident->update([
            'status'          => 'closed',
            'technician_note' => $incident->technician_note
                . ($request->filled('reason') ? "\n[Lý do đóng]: " . $request->reason : ''),
        ]);

        return redirect()
            ->route('manager.incidents.show', $id)
            ->with('success', 'Phản ánh đã được đóng.');
    }
}
