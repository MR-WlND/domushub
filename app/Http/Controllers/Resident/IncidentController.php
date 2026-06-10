<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    /** Danh sách phản ánh của cư dân đang đăng nhập */
    public function index()
    {
        $incidents = Incident::where('resident_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('resident.incidents.index', compact('incidents'));
    }

    /** Form gửi phản ánh mới */
    public function create()
    {
        return view('resident.incidents.create');
    }

    /** Lưu phản ánh mới */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:200',
            'category'    => 'required|in:electrical,plumbing,elevator,cleaning,security,infrastructure,other',
            'priority'    => 'required|in:low,medium,high,urgent',
            'description' => 'required|string|max:2000',
            'images.*'    => 'nullable|image|max:5120',
        ], [
            'title.required'       => 'Vui lòng nhập tiêu đề phản ánh.',
            'category.required'    => 'Vui lòng chọn loại sự cố.',
            'description.required' => 'Vui lòng mô tả chi tiết sự cố.',
        ]);

        // Upload ảnh
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('incidents', 'public');
            }
        }

        $incident = Incident::create([
            'resident_id' => Auth::id(),
            'title'       => $validated['title'],
            'category'    => $validated['category'],
            'priority'    => $validated['priority'],
            'description' => $validated['description'],
            'status'      => 'pending',
            'images'      => $imagePaths ?: null,
        ]);

        return redirect()
            ->route('resident.incidents.show', $incident->id)
            ->with('success', 'Phản ánh của bạn đã được gửi thành công. Nhóm quản lý sẽ liên hệ sớm nhất có thể.');
    }

    /** Xem chi tiết một phản ánh (chỉ của chính mình) */
    public function show(int $id)
    {
        $incident = Incident::where('resident_id', Auth::id())
            ->with(['assignedTo', 'confirmedBy'])
            ->findOrFail($id);

        return view('resident.incidents.show', compact('incident'));
    }
}
