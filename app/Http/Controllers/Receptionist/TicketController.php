<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

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

    public function show($id)
    {
        $ticket = Ticket::with([
            'apartment.floor.block',
            'sender',
            'handler',
            'progresses.user',
        ])->findOrFail($id);

        return view('receptionist.tickets.show', compact('ticket'));
    }
}
