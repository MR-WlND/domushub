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
        $query = CleaningReport::with(['reporter', 'assignees'])
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

        // Kỹ thuật viên: view riêng
        if (auth()->user()->role === 'technician') {
            return view('admin.cleaning-reports.show-technician', compact('report'));
        }

        // Admin/Manager: view đầy đủ
        $technicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        return view('admin.cleaning-reports.show', compact('report', 'technicians'));
    }

    /**
     * Cập nhật trạng thái (dùng chung cho cả admin và KTV)
     * - KTV chỉ được: processing, completed_pending
     * - Admin được: pending, processing, resolved + reject (yêu cầu xử lý lại)
     */
    public function updateStatus(Request $request, $id): JsonResponse|RedirectResponse
    {
        $report = CleaningReport::findOrFail($id);
        $user = auth()->user();

        $allowedStatuses = $user->role === 'technician'
            ? ['processing', 'completed_pending']
            : ['pending', 'processing', 'resolved', 'completed_pending'];

        $request->validate([
            'status' => 'required|in:' . implode(',', $allowedStatuses),
            'progress_note' => 'nullable|string|max:2000',
        ]);

        $report->update(['status' => $request->status]);

        // Save progress note if provided
        if ($request->filled('progress_note')) {
            $notes = $report->progress_notes ?? [];
            $notes[] = [
                'note' => $request->progress_note,
                'status' => $request->status,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'created_at' => now()->toISOString(),
            ];
            $report->update(['progress_notes' => $notes]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $request->status]);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công.');
    }

    /**
     * Admin duyệt hoàn thành
     */
    public function approve($id): JsonResponse
    {
        $report = CleaningReport::findOrFail($id);

        if ($report->status !== 'completed_pending') {
            return response()->json(['success' => false, 'message' => 'Báo cáo chưa ở trạng thái chờ duyệt.']);
        }

        $notes = $report->progress_notes ?? [];
        $notes[] = [
            'note' => 'Quản lý đã duyệt hoàn thành.',
            'status' => 'resolved',
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'created_at' => now()->toISOString(),
        ];

        $report->update([
            'status' => 'resolved',
            'progress_notes' => $notes,
        ]);

        return response()->json(['success' => true, 'message' => 'Đã duyệt hoàn thành.']);
    }

    /**
     * Admin yêu cầu xử lý lại
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $report = CleaningReport::findOrFail($id);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ], [
            'reason.required' => 'Vui lòng nhập lý do yêu cầu xử lý lại.',
        ]);

        $notes = $report->progress_notes ?? [];
        $notes[] = [
            'note' => '⚠️ Yêu cầu xử lý lại: ' . $request->reason,
            'status' => 'processing',
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'created_at' => now()->toISOString(),
        ];

        $report->update([
            'status' => 'processing',
            'progress_notes' => $notes,
        ]);

        return response()->json(['success' => true, 'message' => 'Đã yêu cầu xử lý lại.']);
    }

    public function assign(Request $request, $id): JsonResponse
    {
        $report = CleaningReport::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($report->assignees()->where('user_id', $request->user_id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Kỹ thuật viên đã được phân công.']);
        }

        $report->assignees()->attach($request->user_id);

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
