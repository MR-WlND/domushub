<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInviteRequest;
use App\Models\Apartment;
use App\Models\ApartmentInvite;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ResidentInvitationController extends Controller
{
    public function index(): View
    {
        $apartments = Apartment::whereHas('residents', function ($query) {
            $query->where('user_id', Auth::id())
                ->where('relationship', 'owner')
                ->whereNull('deleted_at');
        })->with(['floor.block'])
            ->orderBy('apartment_number')
            ->get();

        $invites = ApartmentInvite::with(['apartment.floor.block'])
            ->where('created_by', Auth::id())
            ->latest()
            ->paginate(10);

        return view('resident.invitations.index', compact('apartments', 'invites'));
    }

    public function store(StoreInviteRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $apartment = Apartment::findOrFail($validated['apartment_id']);

        $code = 'MEM-' . strtoupper(Str::random(6));

        ApartmentInvite::create([
            'block_id' => $apartment->floor->block_id,
            'apartment_id' => $apartment->id,
            'created_by' => Auth::id(),
            'invite_code' => $code,
            'intended_relationship' => $validated['intended_relationship'],
            'status' => 'active',
            'max_uses' => 1,
            'uses_count' => 0,
            'expired_at' => $validated['expired_at'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Đã tạo mã mời thành viên: ' . $code);
    }
}
