<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use App\Models\Apartment;
use App\Models\Block;
use App\Notifications\ParcelNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParcelController extends Controller
{
    public function index(Request $request)
    {
        $query = Parcel::with('apartment')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sender_name', 'like', "%$search%")
                  ->orWhere('tracking_code', 'like', "%$search%")
                  ->orWhereHas('apartment', fn($q2) => $q2->where('apartment_number', 'like', "%$search%"));
            });
        }

        // Thống kê nhanh
        $stats = [
            'pending'  => Parcel::where('status', 'pending')->count(),
            'notified' => Parcel::where('status', 'notified')->count(),
            'received' => Parcel::where('status', 'received')->count(),
            'returned' => Parcel::where('status', 'returned')->count(),
        ];

        $parcels = $query->paginate(15)->withQueryString();

        return view('receptionist.parcels.index', compact('parcels', 'stats'));
    }

    public function create()
    {
        $blocks = Block::with(['floors' => fn($q) => $q->orderBy('floor_number'), 'floors.apartments' => fn($q) => $q->orderBy('apartment_number')])->orderBy('name')->get();
        return view('receptionist.parcels.create', compact('blocks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id'  => 'required|exists:apartments,id',
            'sender_name'   => 'required|string|max:255',
            'tracking_code' => 'nullable|string|max:100',
            'carrier'       => 'nullable|string|max:100',
            'description'   => 'nullable|string|max:500',
            'note'          => 'nullable|string|max:500',
        ], [
            'apartment_id.required' => 'Vui lòng chọn căn hộ.',
            'sender_name.required'  => 'Vui lòng nhập tên người gửi.',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status']     = 'pending';
        $validated['arrived_at'] = now();

        $parcel = Parcel::create($validated);

        // Thông báo cho tất cả cư dân thuộc căn hộ
        foreach ($parcel->apartment->users as $user) {
            $user->notify(new ParcelNotification($parcel, 'arrived'));
        }

        return redirect()->route('receptionist.parcels.index')
            ->with('success', 'Đã ghi nhận bưu phẩm thành công.');
    }

    public function show($id)
    {
        $parcel = Parcel::with(['apartment.floor.block', 'creator'])->findOrFail($id);
        return view('receptionist.parcels.show', compact('parcel'));
    }

    public function markReceived(Request $request, $id)
    {
        $parcel = Parcel::findOrFail($id);
        $parcel->update([
            'status'      => 'received',
            'received_at' => now(),
        ]);

        // Thông báo cho tất cả cư dân thuộc căn hộ
        foreach ($parcel->apartment->users as $user) {
            $user->notify(new ParcelNotification($parcel, 'received'));
        }

        return back()->with('success', 'Đã xác nhận cư dân nhận bưu phẩm.');
    }

    public function markReturned(Request $request, $id)
    {
        $parcel = Parcel::findOrFail($id);
        $parcel->update([
            'status'      => 'returned',
            'returned_at' => now(),
            'note'        => $request->note ?? $parcel->note,
        ]);

        return back()->with('success', 'Đã đánh dấu hoàn trả bưu phẩm.');
    }

    public function markNotified($id)
    {
        $parcel = Parcel::findOrFail($id);
        $parcel->update(['status' => 'notified']);

        // Gửi lại thông báo cho tất cả cư dân thuộc căn hộ
        foreach ($parcel->apartment->users as $user) {
            $user->notify(new ParcelNotification($parcel, 'arrived'));
        }

        return back()->with('success', 'Đã đánh dấu đã thông báo cư dân.');
    }
}
