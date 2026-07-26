<?php

namespace App\Http\Controllers\Cleaning;

use App\Http\Controllers\Controller;
use App\Models\CleaningTask;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $today = Carbon::today();

        $allTasks = CleaningTask::where('assigned_to', $user->id)
            ->where('task_date', $today)
            ->orderBy('start_time')
            ->get();

        // Only show pending + progress tasks on dashboard
        $activeTasks = $allTasks->whereIn('status', ['pending', 'progress']);

        // Done tasks
        $doneTasks = $allTasks->where('status', 'done');

        // Stats
        $total = $allTasks->count();
        $done = $allTasks->where('status', 'done')->count();
        $progress = $allTasks->where('status', 'progress')->count();
        $pending = $allTasks->where('status', 'pending')->count();

        return view('cleaning.dashboard.index', compact(
            'activeTasks', 'doneTasks', 'total', 'done', 'progress', 'pending'
        ));
    }
}
