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
        $query = CleaningReport::with('reporter')
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
        $report = CleaningReport::with(['reporter', 'assignees'])->findOrFail($id);

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

    public function assign(Request $request, $id): JsonResponse
    {
        $report = CleaningReport::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if already assigned
        if ($report->assignees()->where('user_id', $request->user_id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Kỹ thuật viên đã được phân công.']);
        }

        $report->assignees()->attach($request->user_id);

        // Auto update status to processing if pending
        if ($report->status === 'pending') {
            $report->update(['status' => 'processing']);
        }

        $user = User::find($request->user_id);

        return response()->json([
            'success' => true,
            'message' => 'Đã phân công thành công.',
            'assignee' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
            ],
        ]);
    }

    public function unassign(Request $request, $id): JsonResponse
    {
        $report = CleaningReport::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $report->assignees()->detach($request->user_id);

        return response()->json(['success' => true, 'message' => 'Đã gỡ phân công.']);
    }
}
