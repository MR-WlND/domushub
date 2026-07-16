<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        // Tố cáo liên quan đến tôi (ticket mình bị tố cáo)
        $accusations = Ticket::with(['sender', 'apartment.floor.block'])
            ->where('accused_user_id', $user->id)
            ->latest()
            ->get();

        return view('resident.tickets.index', compact('tickets', 'stats', 'accusations'));
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

        // ── Giới hạn spam: tối đa 3 phản ánh/ngày/user ──
        $todayCount = Ticket::where('sender_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($todayCount >= 3) {
            return back()->withInput()->withErrors([
                'limit' => 'Bạn đã gửi tối đa 3 phản ánh trong ngày hôm nay. Vui lòng thử lại vào ngày mai.'
            ]);
        }

        $ticketType = $request->input('ticket_type', 'complaint');

        $rules = [
            'ticket_type'     => ['required', 'in:complaint,report'],
            'title'           => ['required', 'string', 'max:200'],
            'description'     => ['required', 'string', 'max:2000'],
            'priority'        => ['required', 'in:low,medium,high,urgent'],
            'images'          => ['nullable', 'array', 'max:5'],
            'images.*'        => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,avi,webm', 'max:20480'],
        ];

        // Nếu là tố cáo → bắt buộc đính kèm bằng chứng, tên người không bắt buộc
        if ($ticketType === 'report') {
            $rules['reported_person'] = ['nullable', 'string', 'max:255'];
            $rules['images'] = ['required', 'array', 'min:1', 'max:5'];
        } else {
            $rules['reported_person'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules, [
            'ticket_type.required'       => 'Vui lòng chọn loại phản ánh.',
            'title.required'             => 'Vui lòng nhập tiêu đề phản ánh.',
            'title.max'                  => 'Tiêu đề không được quá 200 ký tự.',
            'description.required'       => 'Vui lòng mô tả chi tiết sự cố.',
            'description.max'            => 'Mô tả không được quá 2000 ký tự.',
            'priority.required'          => 'Vui lòng chọn mức độ ưu tiên.',
            'reported_person.required'   => 'Vui lòng nhập tên người bị tố cáo.',
            'images.required'            => 'Tố cáo bắt buộc phải đính kèm ảnh/video làm bằng chứng.',
            'images.min'                 => 'Tố cáo bắt buộc phải đính kèm ít nhất 1 ảnh/video.',
            'images.max'                 => 'Tối đa 5 file đính kèm.',
            'images.*.file'              => 'File tải lên không hợp lệ.',
            'images.*.mimes'             => 'Định dạng hỗ trợ: JPG, PNG, WEBP, MP4, MOV, AVI, WEBM.',
            'images.*.max'               => 'Dung lượng mỗi file tối đa 20MB.',
        ]);

        // Xử lý nhiều ảnh/video
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('tickets', 'public');
            }
        }

        $ticket = Ticket::create([
            'apartment_id'    => $user->apartment_id,
            'sender_id'       => $user->id,
            'ticket_type'     => $validated['ticket_type'],
            'title'           => $validated['title'],
            'description'     => $validated['description'],
            'reported_person' => $validated['reported_person'] ?? null,
            'priority'        => $validated['priority'],
            'images'          => !empty($imagePaths) ? $imagePaths : null,
            'status'          => 'pending',
        ]);

        // Tạo bản ghi tiến trình đầu tiên
        $progressComment = $ticketType === 'report'
            ? 'Tố cáo đã được gửi thành công. Ban quản lý sẽ xem xét bằng chứng.'
            : 'Phản ánh đã được gửi thành công.';

        TicketProgress::create([
            'ticket_id'  => $ticket->id,
            'status'     => 'pending',
            'comment'    => $progressComment,
            'updated_by' => $user->id,
        ]);

        $successMsg = $ticketType === 'report'
            ? 'Tố cáo của bạn đã được gửi kèm bằng chứng. Ban quản lý sẽ xem xét và xử lý.'
            : 'Phản ánh của bạn đã được gửi thành công. Ban quản lý sẽ xem xét trong thời gian sớm nhất.';

        return redirect()->route('resident.tickets.index')->with('success', $successMsg);
    }

    /**
     * Xem chi tiết phản ánh
     */
    public function show($id)
    {
        $user = Auth::user();

        $ticket = Ticket::with(['sender', 'handler', 'progress.updatedBy', 'apartment.floor.block', 'costs.createdBy'])
            ->where(function ($q) use ($user) {
                // Cho phép xem nếu là cư dân cùng căn hộ HOẶC là người bị tố cáo
                $q->where('apartment_id', $user->apartment_id)
                  ->orWhere('accused_user_id', $user->id);
            })
            ->findOrFail($id);

        // Check xem user hiện tại có phải người bị tố cáo không
        $isAccused = $ticket->accused_user_id === $user->id;

        return view('resident.tickets.show', compact('ticket', 'isAccused'));
    }

    /**
     * Hủy phản ánh (chỉ khi pending)
     */
    public function cancel($id)
    {
        $user = Auth::user();

        $ticket = Ticket::where('apartment_id', $user->apartment_id)
            ->findOrFail($id);

        // Chỉ người gửi phản ánh mới được hủy
        if (!$ticket->canCancelBy($user->id)) {
            return back()->withErrors(['ticket' => 'Chỉ người gửi phản ánh mới có thể hủy, và phản ánh phải đang ở trạng thái chờ xử lý.']);
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

    /**
     * Người bị tố cáo phản hồi (xác nhận / phản đối)
     */
    public function respondAccusation(Request $request, $id)
    {
        $user = Auth::user();

        $ticket = Ticket::where('accused_user_id', $user->id)
            ->where('ticket_type', 'report')
            ->findOrFail($id);

        // Đã phản hồi rồi thì không cho phản hồi lại
        if ($ticket->hasAccusedResponse()) {
            return back()->withErrors(['Bạn đã phản hồi tố cáo này rồi.']);
        }

        $validated = $request->validate([
            'accused_response'         => ['required', 'in:confirmed,denied'],
            'accused_response_comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'accused_response.required' => 'Vui lòng chọn xác nhận hoặc phản đối.',
        ]);

        $ticket->update([
            'accused_response'         => $validated['accused_response'],
            'accused_response_comment' => $validated['accused_response_comment'] ?? null,
            'accused_responded_at'     => now(),
        ]);

        // Thêm tiến trình
        $label = $validated['accused_response'] === 'confirmed' ? 'XÁC NHẬN' : 'PHẢN ĐỐI';
        TicketProgress::create([
            'ticket_id'  => $ticket->id,
            'status'     => $ticket->status,
            'comment'    => 'Người bị tố cáo (' . $user->name . ') đã phản hồi: ' . $label
                         . ($validated['accused_response_comment'] ? ' — "' . $validated['accused_response_comment'] . '"' : ''),
            'updated_by' => $user->id,
        ]);

        $msg = $validated['accused_response'] === 'confirmed'
            ? 'Bạn đã xác nhận tố cáo. Ban quản lý sẽ xử lý tiếp.'
            : 'Bạn đã phản đối tố cáo. Ban quản lý sẽ xem xét lại.';

        return back()->with('success', $msg);
    }
}
