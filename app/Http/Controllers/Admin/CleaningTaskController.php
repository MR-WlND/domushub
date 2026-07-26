<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CleaningTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CleaningTaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = CleaningTask::with(['assignedUser', 'assigner']);

        // Filter by date (only if provided)
        $date = $request->get('date');
        if ($date) {
            $query->where('task_date', $date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by staff
        if ($request->filled('staff_id')) {
            $query->where('assigned_to', $request->staff_id);
        }

        $tasks = $query->orderByRaw("FIELD(status, 'pending', 'progress', 'done')")->orderBy('start_time')->get();
        $cleaningStaff = User::where('role', 'cleaning')->where('status', 'active')->get();

        return view('admin.cleaning-tasks.index', compact('tasks', 'cleaningStaff', 'date'));
    }

    public function create(): View
    {
        $cleaningStaff = User::where('role', 'cleaning')->where('status', 'active')->get();
        return view('admin.cleaning-tasks.create', compact('cleaningStaff'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'area' => 'required|string|max:100',
            'area_group' => 'nullable|string|max:100',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'priority' => 'required|in:low,medium,high',
            'task_date' => 'required|date',
            'checklist' => 'nullable|string',
        ]);

        // Parse checklist from textarea (one item per line)
        $checklistItems = [];
        if (!empty($validated['checklist'])) {
            $lines = array_filter(explode("\n", $validated['checklist']));
            foreach ($lines as $line) {
                $checklistItems[] = ['text' => trim($line), 'done' => false];
            }
        }

        CleaningTask::create([
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'area' => $validated['area'],
            'area_group' => $validated['area_group'] ?? $validated['area'],
            'start_time' => $validated['start_time'] ?? '00:00',
            'end_time' => $validated['end_time'] ?? '23:59',
            'priority' => $validated['priority'],
            'task_date' => $validated['task_date'],
            'checklist' => $checklistItems ?: null,
        ]);

        return redirect()->route('admin.cleaning-tasks.index')
            ->with('success', 'Đã giao công việc vệ sinh thành công!');
    }

    public function destroy($id): RedirectResponse
    {
        CleaningTask::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Đã xóa công việc.');
    }
}
