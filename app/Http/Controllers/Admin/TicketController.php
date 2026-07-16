<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCost;
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
            'status'      => 'nullable|in:pending,assigned,in_progress,completed,cancelled',
            'priority'    => 'nullable|in:low,medium,high,urgent',
            'ticket_type' => 'nullable|in:complaint,report',
            'block_id'    => 'nullable|integer|exists:blocks,id',
            'search'      => 'nullable|string|max:200',
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

        // Lọc theo loại phản ánh
        if ($request->filled('ticket_type')) {
            $query->where('ticket_type', $request->ticket_type);
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
            'reports'     => (clone $baseQuery)->where('ticket_type', 'report')->count(),
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
        $ticket = Ticket::with(['apartment.floor.block', 'sender', 'handler', 'progress.updatedBy', 'costs.createdBy', 'costs.responsibleUser', 'accusedUser'])
            ->findOrFail($id);

        $technicians = User::where('role', 'technician')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Danh sách cư dân để chọn "người chịu trách nhiệm" cho chi phí đền bù
        $residents = User::where('role', 'resident')
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

        // Không cho phép tố cáo chính mình
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

        // Thêm tiến trình
        TicketProgress::create([
            'ticket_id'     => $ticket->id,
            'status'        => $ticket->status,
            'comment'       => 'Đã gửi thông báo tố cáo đến cư dân: ' . $accusedUser->name,
            'updated_by'    => Auth::id(),
        ]);

        return back()->with('success', 'Đã gửi thông báo tố cáo đến ' . $accusedUser->name . '.');
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

    /**
     * Báo cáo đánh giá tổng hợp
     */
    public function report(Request $request)
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
