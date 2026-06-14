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
    public function index(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'status'   => 'nullable|in:pending,assigned,in_progress,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'block_id' => 'nullable|integer|exists:blocks,id',
            'search'   => 'nullable|string|max:200',
        ]);

        $query = Ticket::with(['apartment.floor.block', 'sender', 'handler'])
            ->orderByRaw("FIELD(priority, 'urgent','high','medium','low')")
            ->orderByRaw("FIELD(status, 'pending','assigned','in_progress','completed','cancelled')")
            ->orderBy('created_at', 'asc');

        if ($user->role === 'technician') {
            $query->where('handler_id', $user->id);
        }

        if ($request->filled('block_id')) {
            $query->whereHas('apartment.floor', fn($q) => $q->where('block_id', $request->block_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhereHas('apartment', fn($aq) => $aq->where('apartment_number', 'like', "%{$search}%"));
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

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

        $technicians = User::where('role', 'technician')->where('status', 'active')->orderBy('name')->get();
        $blocks = \App\Models\Block::orderBy('name')->get();

        return view('admin.tickets.index', compact('tickets', 'stats', 'technicians', 'blocks'));
    }

    public function report(Request $request)
    {
        $request->validate([
            'status'        => 'nullable|in:pending_review,approved,rework',
            'block_id'      => 'nullable|integer|exists:blocks,id',
            'technician_id' => 'nullable|integer|exists:users,id',
            'from'          => 'nullable|date',
            'to'            => 'nullable|date',
            'search'        => 'nullable|string|max:200',
        ]);

        $query = Ticket::with(['apartment.floor.block', 'sender', 'handler', 'progress.updatedBy'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('block_id')) {
            $query->whereHas('apartment.floor', fn ($q) => $q->where('block_id', $request->block_id));
        }

        if ($request->filled('technician_id')) {
            $query->where('handler_id', $request->technician_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhereHas('apartment', fn ($aq) => $aq->where('apartment_number', 'like', "%{$search}%"))
                  ->orWhereHas('handler', fn ($hq) => $hq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('sender', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $tickets = $query->get();

        if ($request->filled('status')) {
            $status = $request->status;
            $tickets = $tickets->filter(function ($ticket) use ($status) {
                $last = $ticket->progress->last();
                return match ($status) {
                    'pending_review' => $ticket->status === 'completed' && $last?->status === 'completed',
                    'approved'       => $last?->status === 'approved',
                    'rework'         => ($ticket->status === 'in_progress' && $ticket->reopened_count > 0) || $last?->status === 'rejected',
                    default          => true,
                };
            })->values();
        }

        $totalReports = $tickets->count();

        $pendingReview = $tickets->filter(function ($ticket) {
            $last = $ticket->progress->last();
            return $ticket->status === 'completed' && $last?->status === 'completed';
        });

        $approvedReports = $tickets->filter(function ($ticket) {
            $last = $ticket->progress->last();
            return $last?->status === 'approved';
        });

        $reworkReports = $tickets->filter(function ($ticket) {
            $last = $ticket->progress->last();
            return ($ticket->status === 'in_progress' && $ticket->reopened_count > 0)
                || $last?->status === 'rejected';
        });

        $technicians = User::where('role', 'technician')->where('status', 'active')->orderBy('name')->get();
        $blocks = \App\Models\Block::orderBy('name')->get();

        return view('admin.tickets.report', compact(
            'pendingReview',
            'approvedReports',
            'reworkReports',
            'technicians',
            'blocks',
            'totalReports'
        ));
    }

    public function approveReview(Request $request, $id)
    {
        $ticket = Ticket::with('progress')->findOrFail($id);

        if (!in_array(Auth::user()->role, ['admin', 'manager'], true)) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $lastProgress = $ticket->progress->last();
        if ($ticket->status !== 'completed' || $lastProgress?->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket không ở trạng thái chờ nghiệm thu.',
            ], 422);
        }

        TicketProgress::create([
            'ticket_id'  => $ticket->id,
            'status'     => 'approved',
            'comment'    => 'Admin đã nghiệm thu hoàn thành.',
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Báo cáo đã được xác nhận nghiệm thu.',
        ]);
    }

    public function rejectReview(Request $request, $id)
    {
        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'max:500'],
        ], [
            'reject_reason.required' => 'Vui lòng nhập lý do yêu cầu làm lại.',
            'reject_reason.max'      => 'Lý do tối đa 500 ký tự.',
        ]);

        $ticket = Ticket::with('progress')->findOrFail($id);

        if (!in_array(Auth::user()->role, ['admin', 'manager'], true)) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $lastProgress = $ticket->progress->last();
        if ($ticket->status !== 'completed' || $lastProgress?->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket không ở trạng thái chờ nghiệm thu.',
            ], 422);
        }

        $ticket->update([
            'status'         => 'in_progress',
            'reopened_count' => $ticket->reopened_count + 1,
        ]);

        TicketProgress::create([
            'ticket_id'  => $ticket->id,
            'status'     => 'rejected',
            'comment'    => 'Admin yêu cầu làm lại. Lý do: ' . $validated['reject_reason'],
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Yêu cầu làm lại đã được gửi đến kỹ thuật viên.',
        ]);
    }

    public function show($id)
    {
        $ticket = Ticket::with(['apartment.floor.block', 'sender', 'handler', 'progress.updatedBy'])
            ->findOrFail($id);

        $technicians = User::where('role', 'technician')->where('status', 'active')->orderBy('name')->get();

        return view('admin.tickets.show', compact('ticket', 'technicians'));
    }

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
     * Cập nhật tiến trình — chỉ KTV được giao mới dùng
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

        // KTV báo hoàn thành: bắt buộc comment + ảnh nghiệm thu
        if ($request->input('status') === 'completed' && $user->role === 'technician') {
            $rules['comment']     = ['required', 'string', 'max:1000'];
            $rules['image_proof'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        }

        $validated = $request->validate($rules, [
            'status.required'      => 'Vui lòng chọn trạng thái.',
            'comment.required'     => 'Vui lòng nhập báo cáo hoàn thành.',
            'image_proof.required' => 'Vui lòng tải lên ảnh nghiệm thu khi hoàn thành.',
            'image_proof.max'      => 'Dung lượng ảnh tối đa 2MB.',
        ]);

        $imageProof = null;
        if ($request->hasFile('image_proof')) {
            $imageProof = $request->file('image_proof')->store('ticket-progress', 'public');
        }

        $updateData = ['status' => $validated['status']];

        // Nếu KTV hoàn thành lại sau khi bị reopen → reset rating cũ để cư dân đánh giá lại
        if ($validated['status'] === 'completed' && $ticket->reopened_count > 0) {
            $updateData['rating']           = null;
            $updateData['feedback_comment'] = null;
        }

        $ticket->update($updateData);

        $comment = $validated['comment'] ?? null;
        if ($validated['status'] === 'completed' && $ticket->reopened_count > 0 && !$comment) {
            $comment = 'KTV đã kiểm tra và xử lý lại sau phản hồi của cư dân.';
        }

        TicketProgress::create([
            'ticket_id'   => $ticket->id,
            'status'      => $validated['status'],
            'comment'     => $comment,
            'image_proof' => $imageProof,
            'updated_by'  => Auth::id(),
        ]);

        $message = $validated['status'] === 'completed'
            ? 'Phản ánh đã được đánh dấu hoàn thành.'
            : 'Tiến trình xử lý đã được cập nhật.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => $message,
                'status'       => $validated['status'],
                'status_label' => $validated['status'] === 'completed' ? 'Hoàn thành' : 'Đang xử lý',
            ]);
        }

        return back()->with('success', $message);
    }

    public function acceptTask(Request $request, $id)
    {
        $user   = Auth::user();
        $ticket = Ticket::findOrFail($id);

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

    public function myTasks(Request $request)
    {
        $user = Auth::user();

        $newTasks = Ticket::with(['apartment.floor.block', 'sender'])
            ->where('handler_id', $user->id)
            ->where('status', 'assigned')
            ->orderByRaw("FIELD(priority, 'urgent','high','medium','low')")
            ->orderBy('created_at', 'asc')
            ->get();

        // Ưu tiên hiện ticket cần kiểm tra lại (reopened_count > 0) lên đầu
        $activeTasks = Ticket::with(['apartment.floor.block', 'sender', 'progress'])
            ->where('handler_id', $user->id)
            ->where('status', 'in_progress')
            ->orderByRaw("FIELD(priority, 'urgent','high','medium','low')")
            ->orderByDesc('reopened_count')
            ->orderBy('created_at', 'asc')
            ->get();

        $completedTasks = Ticket::with(['apartment.floor.block'])
            ->where('handler_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'new'       => $newTasks->count(),
            'active'    => $activeTasks->count(),
            'recheck'   => $activeTasks->where('reopened_count', '>', 0)->count(),
            'completed' => Ticket::where('handler_id', $user->id)->where('status', 'completed')->count(),
            'total'     => Ticket::where('handler_id', $user->id)->count(),
        ];

        return view('admin.tickets.technician', compact(
            'newTasks', 'activeTasks', 'completedTasks', 'stats'
        ));
    }

    public function dispatchIndex(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'technician') {
            abort(403, 'Bạn không có quyền truy cập trang điều phối kỹ thuật.');
        }

        $technicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->withCount(['handledTickets as active_tickets_count' => function ($query) {
                $query->whereIn('status', ['assigned', 'in_progress']);
            }])
            ->orderBy('name')
            ->get();

        $pendingTickets = Ticket::with(['apartment.floor.block', 'sender'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $activeTickets = Ticket::with(['apartment.floor.block', 'sender', 'handler'])
            ->whereIn('status', ['assigned', 'in_progress'])
            ->latest()
            ->get();

        return view('admin.tickets.dispatch', compact('technicians', 'pendingTickets', 'activeTickets'));
    }
}
