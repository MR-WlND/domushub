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

        $request->validate([
            'status'   => 'nullable|in:pending,assigned,in_progress,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'block_id' => 'nullable|integer|exists:blocks,id',
            'search'   => 'nullable|string|max:200',
        ]);

        $priorityOrder = ['urgent' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
        $statusOrder   = ['pending' => 0, 'assigned' => 1, 'in_progress' => 2, 'completed' => 3, 'cancelled' => 4];

        $query = Ticket::with(['apartment.floor.block', 'sender', 'handler'])
            ->orderByRaw("FIELD(priority, 'urgent','high','medium','low')")
            ->orderByRaw("FIELD(status, 'pending','assigned','in_progress','completed','cancelled')")
            ->orderBy('created_at', 'asc');

        // Technician chỉ thấy ticket được giao
        if ($user->role === 'technician') {
            $query->where('handler_id', $user->id);
        }

        // Lọc theo tòa nhà
        if ($request->filled('block_id')) {
            $query->whereHas('apartment.floor', function ($q) use ($request) {
                $q->where('block_id', $request->block_id);
            });
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

        $blocks = \App\Models\Block::orderBy('name')->get();

        return view('admin.tickets.index', compact('tickets', 'stats', 'technicians', 'blocks'));
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Đã phân công cho ' . ($handler->name ?? 'N/A'),
                'handler_name' => $handler->name ?? 'N/A',
                'status'       => 'assigned',
                'status_label' => 'Đã phân công',
            ]);
        }

        return back()->with('success', 'Đã phân công kỹ thuật viên xử lý phản ánh thành công.');
    }

    /**
     * Cập nhật tiến trình xử lý
     */
    public function updateProgress(Request $request, $id)
    {
        $user   = Auth::user();
        $ticket = Ticket::findOrFail($id);

        if (in_array($ticket->status, ['completed', 'cancelled'], true)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Không thể cập nhật tiến độ cho phản ánh đã đóng.'], 422);
            }
            return back()->withErrors(['ticket' => 'Không thể cập nhật tiến độ cho phản ánh đã đóng.']);
        }

        if ($user->role === 'technician' && $ticket->handler_id !== $user->id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền cập nhật tiến độ cho phản ánh này.'], 403);
            }
            return back()->withErrors(['ticket' => 'Bạn không có quyền cập nhật tiến độ cho phản ánh này.']);
        }

        $rules = [
            'status'      => ['required', 'in:in_progress,completed'],
            'comment'     => ['nullable', 'string', 'max:1000'],
            'image_proof' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        if ($request->input('status') === 'completed') {
            $rules['comment']     = ['required', 'string', 'max:1000'];
            $rules['image_proof'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        }

        $validated = $request->validate($rules, [
            'status.required'     => 'Vui lòng chọn trạng thái.',
            'comment.required'    => 'Vui lòng nhập báo cáo hoàn thành.',
            'comment.max'         => 'Báo cáo hoàn thành tối đa 1000 ký tự.',
            'image_proof.required'=> 'Vui lòng tải lên ảnh nghiệm thu khi hoàn thành.',
            'image_proof.max'     => 'Dung lượng ảnh tối đa 2MB.',
        ]);

        // Xử lý ảnh nghiệm thu
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

        $statusLabels = [
            'in_progress' => 'Đang xử lý',
            'completed'   => 'Hoàn thành',
        ];
        $message = $validated['status'] === 'completed'
            ? 'Phản ánh đã được đánh dấu hoàn thành.'
            : 'Tiến trình xử lý đã được cập nhật.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => $message,
                'status'       => $validated['status'],
                'status_label' => $statusLabels[$validated['status']] ?? $validated['status'],
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Technician tự nhận nhiệm vụ (assigned → in_progress)
     */
    public function acceptTask(Request $request, $id)
    {
        $user   = Auth::user();
        $ticket = Ticket::findOrFail($id);

        // Chỉ technician được giao mới có thể nhận
        if ($ticket->handler_id !== $user->id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không được phân công phản ánh này.'], 403);
            }
            return back()->withErrors(['ticket' => 'Bạn không được phân công phản ánh này.']);
        }

        if ($ticket->status !== 'assigned') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Phản ánh không ở trạng thái chờ nhận.'], 422);
            }
            return back()->withErrors(['ticket' => 'Phản ánh không ở trạng thái chờ nhận.']);
        }

        $ticket->update(['status' => 'in_progress']);

        TicketProgress::create([
            'ticket_id'  => $ticket->id,
            'status'     => 'in_progress',
            'comment'    => 'Kỹ thuật viên đã nhận và bắt đầu xử lý.',
            'updated_by' => $user->id,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Đã nhận nhiệm vụ thành công!',
                'status'       => 'in_progress',
                'status_label' => 'Đang xử lý',
            ]);
        }

        return back()->with('success', 'Đã nhận nhiệm vụ! Chúc bạn xử lý thành công.');
    }

    /**
     * Dashboard nhiệm vụ riêng của Technician
     */
    public function myTasks(Request $request)
    {
        $user = Auth::user();

        // Danh sách nhiệm vụ mới được giao (assigned) - chưa nhận
        $newTasks = Ticket::with(['apartment.floor.block', 'sender'])
            ->where('handler_id', $user->id)
            ->where('status', 'assigned')
            ->orderByRaw("FIELD(priority, 'urgent','high','medium','low')")
            ->orderBy('created_at', 'asc')
            ->get();

        // Danh sách nhiệm vụ đang xử lý (in_progress)
        $activeTasks = Ticket::with(['apartment.floor.block', 'sender', 'progress'])
            ->where('handler_id', $user->id)
            ->where('status', 'in_progress')
            ->orderByRaw("FIELD(priority, 'urgent','high','medium','low')")
            ->orderBy('created_at', 'asc')
            ->get();

        // Danh sách nhiệm vụ hoàn thành gần đây (10 cái gần nhất)
        $completedTasks = Ticket::with(['apartment.floor.block'])
            ->where('handler_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Thống kê cá nhân
        $stats = [
            'new'       => $newTasks->count(),
            'active'    => $activeTasks->count(),
            'completed' => Ticket::where('handler_id', $user->id)->where('status', 'completed')->count(),
            'total'     => Ticket::where('handler_id', $user->id)->count(),
        ];

        return view('admin.tickets.technician', compact(
            'newTasks', 'activeTasks', 'completedTasks', 'stats'
        ));
    }

    /**
     * Giao diện điều phối kỹ thuật (admin/manager)
     */
    public function dispatchIndex(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'technician') {
            abort(403, 'Bạn không có quyền truy cập trang điều phối kỹ thuật.');
        }

        // Lấy danh sách kỹ thuật viên đang hoạt động cùng số công việc đang phụ trách
        $technicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->withCount(['handledTickets as active_tickets_count' => function ($query) {
                $query->whereIn('status', ['assigned', 'in_progress']);
            }])
            ->orderBy('name')
            ->get();

        // Lấy danh sách các phản ánh chưa phân công (chờ điều phối)
        $pendingTickets = Ticket::with(['apartment.floor.block', 'sender'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Lấy danh sách các phản ánh đang xử lý/đã phân công
        $activeTickets = Ticket::with(['apartment.floor.block', 'sender', 'handler'])
            ->whereIn('status', ['assigned', 'in_progress'])
            ->latest()
            ->get();

        return view('admin.tickets.dispatch', compact('technicians', 'pendingTickets', 'activeTickets'));
    }
}
