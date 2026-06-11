<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Danh sách phản ánh của cư dân (theo căn hộ)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Ticket::with(['sender', 'handler'])
            ->where('apartment_id', $user->apartment_id)
            ->latest();

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->paginate(10)->withQueryString();

        // Thống kê
        $stats = [
            'total'       => Ticket::where('apartment_id', $user->apartment_id)->count(),
            'pending'     => Ticket::where('apartment_id', $user->apartment_id)->where('status', 'pending')->count(),
            'in_progress' => Ticket::where('apartment_id', $user->apartment_id)->whereIn('status', ['assigned', 'in_progress'])->count(),
            'completed'   => Ticket::where('apartment_id', $user->apartment_id)->where('status', 'completed')->count(),
        ];

        return view('resident.tickets.index', compact('tickets', 'stats'));
    }

    /**
     * Form tạo phản ánh mới
     */
    public function create()
    {
        return view('resident.tickets.create');
    }

    /**
     * Lưu phản ánh mới
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (empty($user->apartment_id)) {
            return back()->withInput()->withErrors([
                'apartment' => 'Tài khoản chưa được gắn căn hộ. Vui lòng liên hệ Ban quản lý.'
            ]);
        }

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:2000'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'title.required'       => 'Vui lòng nhập tiêu đề phản ánh.',
            'title.max'            => 'Tiêu đề không được quá 200 ký tự.',
            'description.required' => 'Vui lòng mô tả chi tiết sự cố.',
            'description.max'      => 'Mô tả không được quá 2000 ký tự.',
            'priority.required'    => 'Vui lòng chọn mức độ ưu tiên.',
            'image.image'          => 'File tải lên phải là hình ảnh.',
            'image.max'            => 'Dung lượng ảnh tối đa 2MB.',
        ]);

        // Xử lý ảnh
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
        }

        $ticket = Ticket::create([
            'apartment_id' => $user->apartment_id,
            'sender_id'    => $user->id,
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'priority'     => $validated['priority'],
            'image'        => $imagePath,
            'status'       => 'pending',
        ]);

        // Tạo bản ghi tiến trình đầu tiên
        TicketProgress::create([
            'ticket_id'  => $ticket->id,
            'status'     => 'pending',
            'comment'    => 'Phản ánh đã được gửi thành công.',
            'updated_by' => $user->id,
        ]);

        return redirect()->route('resident.tickets.index')
            ->with('success', 'Phản ánh của bạn đã được gửi thành công. Ban quản lý sẽ xem xét trong thời gian sớm nhất.');
    }

    /**
     * Xem chi tiết phản ánh
     */
    public function show($id)
    {
        $user = Auth::user();

        $ticket = Ticket::with(['sender', 'handler', 'progress.updatedBy', 'apartment.floor.block'])
            ->where('apartment_id', $user->apartment_id)
            ->findOrFail($id);

        return view('resident.tickets.show', compact('ticket'));
    }

    /**
     * Hủy phản ánh (chỉ khi pending)
     */
    public function cancel($id)
    {
        $user = Auth::user();

        $ticket = Ticket::where('apartment_id', $user->apartment_id)
            ->findOrFail($id);

        if (!$ticket->canCancel()) {
            return back()->withErrors(['ticket' => 'Chỉ có thể hủy phản ánh đang ở trạng thái chờ xử lý.']);
        }

        $ticket->update(['status' => 'cancelled']);

        TicketProgress::create([
            'ticket_id'  => $ticket->id,
            'status'     => 'cancelled',
            'comment'    => 'Cư dân đã hủy phản ánh.',
            'updated_by' => $user->id,
        ]);

        return back()->with('success', 'Phản ánh đã được hủy thành công.');
    }

    /**
     * Đánh giá phản ánh sau khi hoàn thành
     */
    public function feedback(Request $request, $id)
    {
        $user = Auth::user();

        $ticket = Ticket::where('apartment_id', $user->apartment_id)
            ->findOrFail($id);

        if (!$ticket->canFeedback()) {
            return back()->withErrors(['ticket' => 'Không thể đánh giá phản ánh này.']);
        }

        $validated = $request->validate([
            'rating'           => ['required', 'integer', 'min:1', 'max:5'],
            'feedback_comment' => ['nullable', 'string', 'max:500'],
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.min'      => 'Đánh giá ít nhất 1 sao.',
            'rating.max'      => 'Đánh giá tối đa 5 sao.',
        ]);

        $ticket->update([
            'rating'           => $validated['rating'],
            'feedback_comment' => $validated['feedback_comment'] ?? null,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá! Phản hồi của bạn giúp chúng tôi cải thiện dịch vụ.');
    }
}
