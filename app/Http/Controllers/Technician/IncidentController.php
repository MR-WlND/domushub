<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    /** Danh sách task được phân công cho kỹ thuật viên */
    public function index()
    {
        $incidents = Incident::where('assigned_to', Auth::id())
            ->with(['resident', 'apartment', 'assignedBy'])
            ->orderByRaw("FIELD(status, 'assigned', 'in_progress', 'resolved', 'confirmed', 'closed')")
            ->orderByDesc('assigned_at')
            ->paginate(15);

        $stats = [
            'assigned'    => Incident::where('assigned_to', Auth::id())->where('status', 'assigned')->count(),
            'in_progress' => Incident::where('assigned_to', Auth::id())->where('status', 'in_progress')->count(),
            'resolved'    => Incident::where('assigned_to', Auth::id())->where('status', 'resolved')->count(),
        ];

        return view('technician.incidents.index', compact('incidents', 'stats'));
    }

    /** Xem chi tiết task */
    public function show(int $id)
    {
        $incident = Incident::where('assigned_to', Auth::id())
            ->with(['resident', 'apartment', 'assignedBy'])
            ->findOrFail($id);

        return view('technician.incidents.show', compact('incident'));
    }

    /** Cập nhật trạng thái xử lý */
    public function updateStatus(Request $request, int $id)
    {
        $incident = Incident::where('assigned_to', Auth::id())->findOrFail($id);

        $request->validate([
            'status'    => 'required|in:in_progress,resolved',
            'note'      => 'nullable|string|max:1000',
            'images.*'  => 'nullable|image|max:5120',
        ]);

        $data = ['status' => $request->status];

        if ($request->filled('note')) {
            $data['technician_note'] = $request->note;
        }

        // Upload ảnh kết quả xử lý (append vào images hiện có)
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('incidents/technician', 'public');
            }
        }

        if (!empty($imagePaths)) {
            $existing = is_array($incident->images)
                ? $incident->images
                : (json_decode($incident->images ?? '[]', true) ?: []);

            $data['images'] = array_values(array_merge($existing, $imagePaths));
        }

        if ($request->status === 'resolved') {
            $data['resolved_at'] = now();
        }

        $incident->update($data);

        $message = $request->status === 'resolved'
            ? 'Đã đánh dấu hoàn thành xử lý. Chờ quản lý xác nhận.'
            : 'Đã cập nhật trạng thái đang xử lý.';

        return redirect()
            ->route('technician.incidents.show', $id)
            ->with('success', $message);
    }
}

