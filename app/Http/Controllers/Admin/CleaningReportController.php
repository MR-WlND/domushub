<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CleaningReport;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CleaningReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = CleaningReport::with(['reporter', 'assignee'])
            ->whereHas('reporter', function ($q) {
                $q->where('role', '!=', 'resident');
            })
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('role')) {
            $query->whereHas('reporter', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        $reports = $query->paginate(15);

        return view('admin.cleaning-reports.index', compact('reports'));
    }

    public function show($id): View
    {
        $report = CleaningReport::with(['reporter', 'assignee'])->findOrFail($id);

        // Lấy danh sách kỹ thuật viên (role = technician) để chọn phân công
        $technicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        return view('admin.cleaning-reports.show', compact('report', 'technicians'));
    }

    public function updateStatus(Request $request, $id): JsonResponse|RedirectResponse
    {
        $report = CleaningReport::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,resolved',
        ]);

        $report->update(['status' => $request->status]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $request->status]);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function assign(Request $request, $id): RedirectResponse
    {
        $report = CleaningReport::findOrFail($id);

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'admin_note'  => 'nullable|string|max:1000',
        ], [
            'assigned_to.required' => 'Vui lòng chọn kỹ thuật viên.',
            'assigned_to.exists'   => 'Kỹ thuật viên không hợp lệ.',
        ]);

        $report->update([
            'assigned_to' => $request->assigned_to,
            'admin_note'  => $request->admin_note,
            'status'      => $report->status === 'pending' ? 'processing' : $report->status,
        ]);

        return redirect()->back()->with('success', 'Đã phân công kỹ thuật viên xử lý sự cố.');
    }
}
