<?php

namespace App\Http\Controllers\Cleaning;

use App\Http\Controllers\Controller;
use App\Models\CleaningReport;
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

        return view('cleaning.report.index', compact('reports'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'location' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high',
            'description' => 'nullable|string|max:2000',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề sự cố.',
            'location.required' => 'Vui lòng chọn khu vực.',
            'images.*.image' => 'File phải là hình ảnh.',
            'images.*.max' => 'Ảnh không được vượt quá 10MB.',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('cleaning-reports', 'public');
            }
        }

        CleaningReport::create([
            'reported_by' => auth()->id(),
            'title' => $validated['title'],
            'location' => $validated['location'],
            'priority' => $validated['priority'],
            'description' => $validated['description'] ?? null,
            'images' => $imagePaths ?: null,
        ]);

        return redirect()->route('cleaning.report')
            ->with('success', 'Báo cáo sự cố đã được gửi thành công!');
    }
}
