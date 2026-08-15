<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketProgress;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['apartment.floor.block', 'sender', 'handler'])
            ->orderByRaw("FIELD(priority, 'urgent','high','medium','low')")
            ->orderByRaw("FIELD(status, 'pending','assigned','in_progress','completed','cancelled')")
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('sender', fn($q2) => $q2->where('name', 'like', "%$search%"));
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        return view('receptionist.tickets.index', compact('tickets'));
    }

    public function create()
    {
        $apartments = Apartment::with('floor')->orderBy('apartment_number')->get();
        return view('receptionist.tickets.create', compact('apartments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => ['required', 'exists:apartments,id'],
            'ticket_type'  => ['required', 'in:complaint,report'],
            'title'        => ['required', 'string', 'max:200'],
            'description'  => ['required', 'string', 'max:2000'],
            'priority'     => ['required', 'in:low,medium,high,urgent'],
        ], [
            'apartment_id.required' => 'Vui lòng chọn căn hộ.',
            'title.required'        => 'Vui lòng nhập tiêu đề phản ánh.',
            'description.required'  => 'Vui lòng mô tả chi tiết sự cố.',
        ]);

        $ticket = Ticket::create([
            'apartment_id' => $validated['apartment_id'],
            'sender_id'    => null, // Người gửi là ẩn danh hoặc người đại diện (Lễ tân ghi hộ)
            'ticket_type'  => $validated['ticket_type'],
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'priority'     => $validated['priority'],
            'status'       => 'pending',
            'created_by'   => Auth::id(), // Đánh dấu Lễ tân tạo
        ]);

        TicketProgress::create([
            'ticket_id'  => $ticket->id,
            'status'     => 'pending',
            'comment'    => 'Phản ánh được tạo mới bởi bộ phận Lễ tân thay mặt cư dân.',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('receptionist.tickets.index')->with('success', 'Đã tạo phản ánh thành công.');
    }

    public function show($id)
    {
        $ticket = Ticket::with([
            'apartment.floor.block',
            'sender',
            'handler',
            'progress.updatedBy',
        ])->findOrFail($id);

        return view('receptionist.tickets.show', compact('ticket'));
    }
}
