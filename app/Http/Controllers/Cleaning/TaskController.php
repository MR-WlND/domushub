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

            // Tick all checklist items when marking as done
            if ($task->checklist) {
                $checklist = collect($task->checklist)->map(function ($item) {
                    $item['done'] = true;
                    return $item;
                })->toArray();
                $task->checklist = $checklist;
            }
        } else {
            $task->completed_at = null;
        }

        // Lưu ghi chú của nhân viên vào staff_note (không được ghi vào manager_note)
        if ($request->has('note')) {
            $task->staff_note = $request->input('note');
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

        $checklist = $request->input('checklist');
        $task->checklist = $checklist;

        // If all checklist items are done, auto-mark task as done
        $allDone = collect($checklist)->every(fn($item) => $item['done'] === true);
        if ($allDone && $task->status !== 'done') {
            $task->status = 'done';
            $task->completed_at = now();
        }
        // If not all done but task was marked done, revert to progress
        if (!$allDone && $task->status === 'done') {
            $task->status = 'progress';
            $task->completed_at = null;
        }

        $task->save();

        return response()->json([
            'success' => true,
            'status' => $task->status,
            'completed_at' => $task->completed_at?->format('H:i'),
        ]);
    }
}
