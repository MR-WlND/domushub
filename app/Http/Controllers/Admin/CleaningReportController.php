<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CleaningReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CleaningReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = CleaningReport::with('reporter')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $reports = $query->paginate(15);

        return view('admin.cleaning-reports.index', compact('reports'));
    }

    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $report = CleaningReport::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,resolved',
        ]);

        $report->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Cập nhật trạng thái báo cáo thành công.');
    }
}
