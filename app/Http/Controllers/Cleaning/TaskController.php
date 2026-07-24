<?php

namespace App\Http\Controllers\Cleaning;

use App\Http\Controllers\Controller;
use App\Models\CleaningTask;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $today = Carbon::today();

        $tasks = CleaningTask::where('assigned_to', $user->id)
            ->where('task_date', $today)
            ->orderBy('start_time')
            ->get();

        // Group by area_group
        $grouped = $tasks->groupBy('area_group');

        // Stats
        $total = $tasks->count();
        $done = $tasks->where('status', 'done')->count();
        $progress = $tasks->where('status', 'progress')->count();
        $pending = $tasks->where('status', 'pending')->count();
        $percentage = $total > 0 ? round(($done / $total) * 100) : 0;

        // Manager note (get from first task that has one, or null)
        $managerNote = $tasks->whereNotNull('manager_note')->first()?->manager_note;

        return view('cleaning.tasks.index', compact(
            'tasks', 'grouped', 'total', 'done', 'progress', 'pending', 'percentage', 'managerNote'
        ));
    }

    public function show($id): View
    {
        $task = CleaningTask::with('assigner')->findOrFail($id);

        // Ensure user can only see their own tasks
        if ($task->assigned_to !== auth()->id()) {
            abort(403);
        }

        return view('cleaning.tasks.show', compact('task'));
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $task = CleaningTask::findOrFail($id);

        if ($task->assigned_to !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,progress,done',
        ]);

        $task->status = $request->status;

        if ($request->status === 'done') {
            $task->completed_at = now();
        } elseif ($request->status !== 'done') {
            $task->completed_at = null;
        }

        $task->save();

        return response()->json([
            'success' => true,
            'task' => $task,
        ]);
    }

    public function updateChecklist(Request $request, $id): JsonResponse
    {
        $task = CleaningTask::findOrFail($id);

        if ($task->assigned_to !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->checklist = $request->input('checklist');
        $task->save();

        return response()->json(['success' => true]);
    }
}
