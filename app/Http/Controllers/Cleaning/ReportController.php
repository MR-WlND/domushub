<?php

namespace App\Http\Controllers\Cleaning;

use App\Http\Controllers\Controller;
use App\Models\CleaningReport;
use App\Models\StaffSchedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = CleaningReport::where('reported_by', auth()->id())
            ->latest()
            ->get();

        // Lấy khu vực phân công hôm nay từ lịch phân ca
        $user = auth()->user();
        $todaySchedule = null;

        if ($user->staff_id) {
            $todaySchedule = StaffSchedule::with(['block', 'floor'])
                ->where('staff_id', $user->staff_id)
                ->whereDate('work_date', Carbon::today())
                ->whereNotNull('block_id')
                ->first();
        }

        return view('cleaning.report.index', compact('reports', 'todaySchedule'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:200',
            'block_id'    => 'nullable|integer',
            'floor_id'    => 'nullable|integer',
            'location'    => 'nullable|string|max:200',
            'priority'    => 'required|in:low,medium,high',
            'description' => 'nullable|string|max:2000',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi,webm|max:51200',
        ], [
            'title.required'    => 'Vui lòng nhập tiêu đề sự cố.',
            'title.max'         => 'Tiêu đề không được vượt quá 200 ký tự.',
            'priority.required' => 'Vui lòng chọn mức độ ưu tiên.',
            'images.max'        => 'Tải tối đa 5 file.',
            'images.*.file'     => 'File không hợp lệ.',
            'images.*.mimes'    => 'Chỉ hỗ trợ ảnh (JPG, PNG, WebP) hoặc video (MP4, MOV, AVI, WebM).',
            'images.*.max'      => 'Mỗi file không được vượt quá 50MB.',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('cleaning-reports', 'public');
            }
        }

        // Xây dựng chuỗi location từ block + floor + chi tiết
        $user = auth()->user();
        $blockId = $validated['block_id'] ?? null;
        $floorId = $validated['floor_id'] ?? null;
        $locationDetail = $validated['location'] ?? '';

        $locationParts = [];
        if ($blockId) {
            $block = \App\Models\Block::find($blockId);
            if ($block) $locationParts[] = $block->name;
        }
        if ($floorId) {
            $floor = \App\Models\Floor::find($floorId);
            if ($floor) $locationParts[] = $floor->name ?? 'Tầng ' . $floor->floor_number;
        }
        if ($locationDetail) {
            $locationParts[] = $locationDetail;
        }

        $locationStr = implode(' – ', $locationParts) ?: 'Không xác định';

        CleaningReport::create([
            'reported_by'  => $user->id,
            'block_id'     => $blockId,
            'floor_id'     => $floorId,
            'title'        => $validated['title'],
            'location'     => $locationStr,
            'priority'     => $validated['priority'],
            'description'  => $validated['description'] ?? null,
            'images'       => $imagePaths ?: null,
        ]);

        return redirect()->route('cleaning.report')
            ->with('success', 'Báo cáo sự cố đã được gửi thành công!');
    }
}
