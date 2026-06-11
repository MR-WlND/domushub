<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketProgress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Danh sách tất cả phản ánh (admin/manager) hoặc chỉ được giao (technician)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Ticket::with(['apartment.floor.block', 'sender', 'handler'])->latest();

        // Technician chỉ thấy ticket được giao
        if ($user->role === 'technician') {
            $query->where('handler_id', $user->id);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo mức độ ưu tiên
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhereHas('apartment', function ($aq) use ($search) {
                      $aq->where('apartment_number', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        // Thống kê
        $baseQuery = Ticket::query();
        if ($user->role === 'technician') {
            $baseQuery->where('handler_id', $user->id);
        }

        $stats = [
            'total'       => (clone $baseQuery)->count(),
            'pending'     => (clone $baseQuery)->where('status', 'pending')->count(),
            'assigned'    => (clone $baseQuery)->where('status', 'assigned')->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'completed'   => (clone $baseQuery)->where('status', 'completed')->count(),
        ];

        // Danh sách technician để phân công
        $technicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.tickets.index', compact('tickets', 'stats', 'technicians'));
    }

    /**
     * Xem chi tiết phản ánh
     */
    public function show($id)
    {
        $ticket = Ticket::with(['apartment.floor.block', 'sender', 'handler', 'progress.updatedBy'])
            ->findOrFail($id);

        $technicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.tickets.show', compact('ticket', 'technicians'));
    }

    /**
     * Phân công technician xử lý
     */
    public function assign(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'handler_id' => ['required', 'exists:users,id'],
        ], [
            'handler_id.required' => 'Vui lòng chọn kỹ thuật viên.',
            'handler_id.exists'   => 'Kỹ thuật viên không tồn tại.',
        ]);

        $ticket->update([
            'handler_id' => $validated['handler_id'],
            'status'     => 'assigned',
        ]);

        $handler = User::find($validated['handler_id']);

        TicketProgress::create([
            'ticket_id'  => $ticket->id,
            'status'     => 'assigned',
            'comment'    => 'Đã phân công cho kỹ thuật viên: ' . ($handler->name ?? 'N/A'),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Đã phân công kỹ thuật viên xử lý phản ánh thành công.');
    }

    /**
     * Cập nhật tiến trình xử lý
     */
    public function updateProgress(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'status'      => ['required', 'in:in_progress,completed'],
            'comment'     => ['nullable', 'string', 'max:1000'],
            'image_proof' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'status.required' => 'Vui lòng chọn trạng thái.',
            'comment.max'     => 'Ghi chú tối đa 1000 ký tự.',
            'image_proof.max' => 'Dung lượng ảnh tối đa 2MB.',
        ]);

        // Xử lý ảnh chứng minh
        $imageProof = null;
        if ($request->hasFile('image_proof')) {
            $imageProof = $request->file('image_proof')->store('ticket-progress', 'public');
        }

        $ticket->update(['status' => $validated['status']]);

        TicketProgress::create([
            'ticket_id'   => $ticket->id,
            'status'      => $validated['status'],
            'comment'     => $validated['comment'] ?? null,
            'image_proof' => $imageProof,
            'updated_by'  => Auth::id(),
        ]);

        $message = $validated['status'] === 'completed'
            ? 'Phản ánh đã được đánh dấu hoàn thành.'
            : 'Tiến trình xử lý đã được cập nhật.';

        return back()->with('success', $message);
    }
}
