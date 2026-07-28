<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCost;
use App\Models\TicketProgress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccusationNotification;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'status'      => 'nullable|in:pending,assigned,in_progress,completed,cancelled',
            'priority'    => 'nullable|in:low,medium,high,urgent',
            'ticket_type' => 'nullable|in:complaint,report',
            'block_id'    => 'nullable|integer|exists:blocks,id',
            'search'      => 'nullable|string|max:200',

            // Tab 2: Điều phối (Dispatch)
            'dispatch_block_id' => 'nullable|integer|exists:blocks,id',
            'dispatch_priority' => 'nullable|in:low,medium,high,urgent',
            'dispatch_search'   => 'nullable|string|max:200',

            // Tab 3: Nghiệm thu (Report)
            'report_block_id'      => 'nullable|integer|exists:blocks,id',
            'report_technician_id' => 'nullable|integer|exists:users,id',
            'report_search'        => 'nullable|string|max:200',
        ]);

        // ── Tab 1: Tickets list ──
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

        // Lọc theo loại phản ánh
        if ($request->filled('ticket_type')) {
            $query->where('ticket_type', $request->ticket_type);
        }

        // Lọc theo ngày
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanSearch = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $cleanSearch) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhereHas('apartment', fn($aq) => $aq->where('apartment_number', 'like', "%{$search}%"));
                if (!empty($cleanSearch)) {
                    $q->orWhere('id', intval($cleanSearch));
                }
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
            'reports'     => (clone $baseQuery)->where('ticket_type', 'report')->count(),
        ];

        $technicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->withCount(['handledTickets as active_tickets_count' => fn($q) => $q->whereIn('status', ['assigned', 'in_progress'])])
            ->orderBy('name')
            ->get();

        $blocks = \App\Models\Block::orderBy('name')->get();

        // ── AJAX: return partial HTML for ticket table only ──
        if ($request->ajax() || $request->wantsJson()) {
            $html = view('admin.tickets._ticket_table', compact('tickets', 'blocks'))->render();
            return response()->json([
                'success' => true,
                'html'    => $html,
                'stats'   => $stats,
            ]);
        }

        // ── Tab 2: Dispatch data (admin/manager only) ──
        $pendingTickets = collect();
        $activeTickets  = collect();
        $dispatchStats  = ['pending' => 0, 'active' => 0];

        if (in_array($user->role, ['admin', 'manager'])) {
            $pendingQuery = Ticket::with(['apartment.floor.block', 'sender'])
                ->where('status', 'pending')
                ->latest();

            if ($request->filled('dispatch_block_id')) {
                $pendingQuery->whereHas('apartment.floor', fn($q) => $q->where('block_id', $request->dispatch_block_id));
            }
            if ($request->filled('dispatch_priority')) {
                $pendingQuery->where('priority', $request->dispatch_priority);
            }
            if ($request->filled('dispatch_search')) {
                $search = $request->dispatch_search;
                $pendingQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('id', $search)
                      ->orWhereHas('apartment', fn($aq) => $aq->where('apartment_number', 'like', "%{$search}%"));
                });
            }

            $pendingTickets = $pendingQuery->get();

            $activeTickets = Ticket::with(['apartment.floor.block', 'sender', 'handler'])
                ->whereIn('status', ['assigned', 'in_progress'])->latest()->get();

            $dispatchStats = [
                'pending' => $pendingTickets->count(),
                'active'  => $activeTickets->count(),
            ];
        }

        // ── Tab 3: Report data (admin/manager only) ──
        $pendingReview   = collect();
        $approvedReports = collect();
        $reworkReports   = collect();
        $reportStats     = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rework' => 0];

        if (in_array($user->role, ['admin', 'manager'])) {
            $allTicketsQuery = Ticket::with(['apartment.floor.block', 'handler', 'progress.updatedBy']);

            if ($request->filled('report_block_id')) {
                $allTicketsQuery->whereHas('apartment.floor', fn($q) => $q->where('block_id', $request->report_block_id));
            }
            if ($request->filled('report_technician_id')) {
                $allTicketsQuery->where('handler_id', $request->report_technician_id);
            }
            if ($request->filled('report_search')) {
                $search = $request->report_search;
                $allTicketsQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('id', $search)
                      ->orWhereHas('apartment', fn($aq) => $aq->where('apartment_number', 'like', "%{$search}%"));
                });
            }

            $allTickets = $allTicketsQuery->get();

            $pendingReview = $allTickets->filter(function ($t) {
                $last = $t->progress->last();
                return $t->status === 'completed' && $last?->status === 'completed';
            });

            $approvedReports = $allTickets->filter(fn($t) => $t->progress->last()?->status === 'approved');

            $reworkReports = $allTickets->filter(function ($t) {
                $last = $t->progress->last();
                return ($t->status === 'in_progress' && $t->reopened_count > 0) || $last?->status === 'rejected';
            });

            $reportStats = [
                'total'    => $pendingReview->count() + $approvedReports->count() + $reworkReports->count(),
                'pending'  => $pendingReview->count(),
                'approved' => $approvedReports->count(),
                'rework'   => $reworkReports->count(),
            ];
        }

        return view('admin.tickets.index', compact(
            'tickets', 'stats', 'technicians', 'blocks',
            'pendingTickets', 'activeTickets', 'dispatchStats',
            'pendingReview', 'approvedReports', 'reworkReports', 'reportStats'
        ));
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
            'completed_at'   => null,
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
        $ticket = Ticket::with(['apartment.floor.block', 'sender', 'handler', 'progress.updatedBy', 'costs.createdBy', 'costs.responsibleUser', 'accusedUser.apartment'])
            ->findOrFail($id);

        $technicians = User::where('role', 'technician')->where('status', 'active')->orderBy('name')->get();

        // Danh sách cư dân để chọn "người chịu trách nhiệm" cho chi phí đền bù
        $residents = User::with('apartment')
            ->where('role', 'resident')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.tickets.show', compact('ticket', 'technicians', 'residents'));
    }

    /**
     * Admin chọn người bị tố cáo và gửi thông báo
     */
    public function assignAccused(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->ticket_type !== 'report') {
            return back()->withErrors(['Chỉ ticket loại tố cáo mới có thể chọn người bị tố cáo.']);
        }

        $validated = $request->validate([
            'accused_user_id' => ['required', 'exists:users,id'],
        ], [
            'accused_user_id.required' => 'Vui lòng chọn cư dân bị tố cáo.',
            'accused_user_id.exists'   => 'Cư dân không tồn tại.',
        ]);

        if ($validated['accused_user_id'] == $ticket->sender_id) {
            return back()->withErrors(['Không thể chọn chính người gửi tố cáo làm người bị tố cáo.']);
        }

        $accusedUser = User::findOrFail($validated['accused_user_id']);

        $ticket->update([
            'accused_user_id' => $accusedUser->id,
            'accused_response' => null,
            'accused_response_comment' => null,
            'accused_responded_at' => null,
        ]);

        TicketProgress::create([
            'ticket_id'     => $ticket->id,
            'status'        => $ticket->status,
            'comment'       => 'Đã gửi thông báo tố cáo đến cư dân: ' . $accusedUser->name,
            'updated_by'    => Auth::id(),
        ]);

        $emailSent = false;
        if ($accusedUser->email) {
            try {
                Mail::to($accusedUser->email)->send(new AccusationNotification($ticket, $accusedUser));
                $emailSent = true;
            } catch (\Exception $e) {
                \Log::error('Gửi email tố cáo thất bại: ' . $e->getMessage());
            }
        }

        $msg = 'Đã gửi thông báo tố cáo đến ' . $accusedUser->name;
        if ($emailSent) {
            $msg .= ' (email đã gửi đến ' . $accusedUser->email . ')';
        }
        return back()->with('success', $msg . '.');
    }

    /**
     * Thêm chi phí phát sinh cho phản ánh
     */
    public function addCost(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if (!in_array(Auth::user()->role, ['admin', 'manager'], true)) {
            abort(403, 'Bạn không có quyền thêm chi phí.');
        }

        $validated = $request->validate([
            'cost_type'            => ['required', 'in:repair,compensation'],
            'description'          => ['required', 'string', 'max:255'],
            'amount'               => ['required', 'numeric', 'min:1000'],
            'note'                 => ['nullable', 'string', 'max:1000'],
            'responsible_user_id'  => ['nullable', 'required_if:cost_type,compensation', 'exists:users,id'],
        ], [
            'cost_type.required'             => 'Vui lòng chọn loại chi phí.',
            'description.required'           => 'Vui lòng nhập mô tả chi phí.',
            'amount.required'                => 'Vui lòng nhập số tiền.',
            'amount.min'                     => 'Số tiền tối thiểu là 1,000đ.',
            'responsible_user_id.required_if' => 'Vui lòng chọn người chịu trách nhiệm đền bù.',
        ]);

        TicketCost::create([
            'ticket_id'            => $ticket->id,
            'cost_type'            => $validated['cost_type'],
            'description'          => $validated['description'],
            'amount'               => $validated['amount'],
            'note'                 => $validated['note'] ?? null,
            'responsible_user_id'  => $validated['cost_type'] === 'compensation' ? $validated['responsible_user_id'] : null,
            'created_by'           => Auth::id(),
        ]);

        $typeLabel = $validated['cost_type'] === 'repair' ? 'sửa chữa' : 'đền bù';
        return back()->with('success', "Đã thêm chi phí {$typeLabel} thành công.");
    }

    /**
     * Xóa chi phí phát sinh
     */
    public function deleteCost($id, $costId)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'], true)) {
            abort(403, 'Bạn không có quyền xóa chi phí.');
        }

        $cost = TicketCost::where('ticket_id', $id)->findOrFail($costId);
        $cost->delete();

        return back()->with('success', 'Đã xóa chi phí phát sinh.');
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

        if ($validated['status'] === 'completed') {
            $updateData['completed_at'] = now();
            // Nếu KTV hoàn thành lại sau khi bị reopen → reset rating cũ để cư dân đánh giá lại
            if ($ticket->reopened_count > 0) {
                $updateData['rating']           = null;
                $updateData['feedback_comment'] = null;
            }
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

        // Main table query for technician's tickets
        $query = Ticket::with(['apartment.floor.block', 'sender', 'progress', 'costs'])
            ->where('handler_id', $user->id);

        // 1. Lọc theo Block (Tòa)
        if ($request->filled('block_id')) {
            $blockId = $request->input('block_id');
            $query->whereHas('apartment.floor', fn($q) => $q->where('block_id', $blockId));
        }

        // 2. Lọc theo Trạng thái
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'assigned') {
                $query->where('status', 'assigned');
            } elseif ($status === 'in_progress') {
                $query->where('status', 'in_progress');
            } elseif ($status === 'completed') {
                $query->where('status', 'completed');
            } elseif ($status === 'recheck') {
                $query->where('status', 'in_progress')->where('reopened_count', '>', 0);
            }
        }

        // 3. Lọc theo Độ ưu tiên
        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // 4. Tìm kiếm từ khóa (ID, tiêu đề, mã căn hộ)
        if ($request->filled('search')) {
            $search = trim($request->search);
            // Chuẩn hóa nếu tìm kiếm dạng #REQ-2024-089 hoặc REQ-089
            $cleanSearch = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $cleanSearch) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhereHas('apartment', fn($aq) => $aq->where('apartment_number', 'like', "%{$search}%"));
                if (!empty($cleanSearch)) {
                    $q->orWhere('id', intval($cleanSearch));
                }
            });
        }

        // 5. Lọc theo Tháng
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // 6. Lọc theo Năm
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // 7. Sắp xếp (Sorting)
        $sort = $request->input('sort', 'newest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'priority_desc') {
            $query->orderByRaw("FIELD(priority, 'urgent','high','medium','low')")->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $tickets = $query->paginate(10)->withQueryString();

        // Statistical Cards
        $totalCount = Ticket::where('handler_id', $user->id)->count();
        $inProgressCount = Ticket::where('handler_id', $user->id)->where('status', 'in_progress')->count();
        $assignedCount = Ticket::where('handler_id', $user->id)->where('status', 'assigned')->count();
        $completedThisMonthCount = Ticket::where('handler_id', $user->id)
            ->where('status', 'completed')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
        $completedThisWeekCount = Ticket::where('handler_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $totalCompleted = Ticket::where('handler_id', $user->id)->where('status', 'completed')->count();
        $recheckCount = Ticket::where('handler_id', $user->id)->where('status', 'in_progress')->where('reopened_count', '>', 0)->count();

        $avgRatingVal = Ticket::where('handler_id', $user->id)->whereNotNull('rating')->avg('rating');
        $avgRatingFormatted = $avgRatingVal ? number_format($avgRatingVal, 1) . '/5' : '4.9/5';

        $stats = [
            'total'                => $totalCount,
            'active'               => $inProgressCount,
            'new'                  => $assignedCount,
            'completed_this_month' => $completedThisMonthCount,
            'completed_this_week'  => $completedThisWeekCount > 0 ? $completedThisWeekCount : ($completedThisMonthCount > 0 ? $completedThisMonthCount : $totalCompleted),
            'avg_rating'           => $avgRatingFormatted,
            'completed'            => $totalCompleted,
            'recheck'              => $recheckCount,
        ];

        // Legacy arrays for compatibility
        $newTasks = Ticket::with(['apartment.floor.block', 'sender'])
            ->where('handler_id', $user->id)
            ->where('status', 'assigned')
            ->orderBy('created_at', 'desc')
            ->get();

        $activeTasks = Ticket::with(['apartment.floor.block', 'sender', 'progress'])
            ->where('handler_id', $user->id)
            ->where('status', 'in_progress')
            ->orderBy('created_at', 'desc')
            ->get();

        $completedTasks = Ticket::with(['apartment.floor.block'])
            ->where('handler_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $blocks = \App\Models\Block::orderBy('name')->get();

        return view('admin.tickets.technician', compact(
            'tickets', 'newTasks', 'activeTasks', 'completedTasks', 'stats', 'blocks'
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

    /**
     * Báo cáo đánh giá tổng hợp
     */
    public function ratingReport(Request $request)
    {
        // Thống kê chung về đánh giá
        $totalRated = Ticket::whereNotNull('rating')->count();
        $totalCompleted = Ticket::where('status', 'completed')->count();
        $avgRating = Ticket::whereNotNull('rating')->avg('rating');

        // Phân bố theo số sao (1-5)
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Ticket::where('rating', $i)->count();
            $ratingDistribution[$i] = [
                'count'   => $count,
                'percent' => $totalRated > 0 ? round($count / $totalRated * 100) : 0,
            ];
        }

        // Top KTV được đánh giá (trung bình cao nhất, tối thiểu 3 đánh giá)
        $topTechnicians = User::where('role', 'technician')
            ->withCount(['handledTickets as rated_count' => function ($q) {
                $q->whereNotNull('rating');
            }])
            ->withAvg(['handledTickets as avg_rating' => function ($q) {
                $q->whereNotNull('rating');
            }], 'rating')
            ->having('rated_count', '>=', 1)
            ->orderByDesc('avg_rating')
            ->orderByDesc('rated_count')
            ->limit(10)
            ->get();

        // Danh sách đánh giá gần đây
        $recentRatings = Ticket::with(['sender', 'handler', 'apartment.floor.block'])
            ->whereNotNull('rating')
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get();

        // Thống kê theo tháng (6 tháng gần nhất)
        $monthlyStats = [];
        for ($m = 5; $m >= 0; $m--) {
            $date = now()->subMonths($m);
            $monthlyStats[] = [
                'label'      => $date->format('m/Y'),
                'avg_rating' => round(Ticket::whereNotNull('rating')
                    ->whereYear('updated_at', $date->year)
                    ->whereMonth('updated_at', $date->month)
                    ->avg('rating') ?? 0, 1),
                'count'      => Ticket::whereNotNull('rating')
                    ->whereYear('updated_at', $date->year)
                    ->whereMonth('updated_at', $date->month)
                    ->count(),
            ];
        }

        return view('admin.tickets.report', compact(
            'totalRated', 'totalCompleted', 'avgRating',
            'ratingDistribution', 'topTechnicians', 'recentRatings', 'monthlyStats'
        ));
    }
}
