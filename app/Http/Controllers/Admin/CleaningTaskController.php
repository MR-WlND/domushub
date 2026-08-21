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
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'area'        => 'required|string|max:100',
            'area_group'  => 'nullable|string|max:100',
            'start_time'  => 'nullable|date_format:H:i',
            'end_time'    => 'nullable|date_format:H:i|after:start_time',
            'priority'    => 'required|in:low,medium,high',
            'task_date'   => 'required|date',
            'checklist'   => 'nullable|string',
        ], [
            'assigned_to.required'      => 'Vui lòng chọn nhân viên được giao.',
            'assigned_to.exists'        => 'Nhân viên được chọn không tồn tại.',
            'title.required'            => 'Vui lòng nhập tên công việc.',
            'title.max'                 => 'Tên công việc không được vượt quá 200 ký tự.',
            'description.max'           => 'Mô tả không được vượt quá 2000 ký tự.',
            'area.required'             => 'Vui lòng nhập khu vực vệ sinh.',
            'area.max'                  => 'Tên khu vực không được vượt quá 100 ký tự.',
            'start_time.date_format'    => 'Giờ bắt đầu phải đúng định dạng HH:MM.',
            'end_time.date_format'      => 'Giờ kết thúc phải đúng định dạng HH:MM.',
            'end_time.after'            => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'priority.required'         => 'Vui lòng chọn mức độ ưu tiên.',
            'priority.in'               => 'Mức độ ưu tiên không hợp lệ.',
            'task_date.required'        => 'Vui lòng chọn ngày thực hiện.',
            'task_date.date'            => 'Ngày thực hiện không đúng định dạng.',
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
