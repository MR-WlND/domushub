<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ApartmentInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApartmentInviteController extends Controller
{
    /**
     * Store a new apartment-scoped invite. Only owner of the apartment can create.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'intended_relationship' => 'required|in:owner,tenant,family_member',
            'expired_at' => 'nullable|date|after:now',
        ]);

        $apartment = Apartment::findOrFail($validated['apartment_id']);

        // Kiểm tra user hiện tại có phải owner của căn hộ hay không
        $isOwner = $apartment->residents()->where('user_id', Auth::id())->where('relationship', 'owner')->exists();

        if (! $isOwner) {
            return back()->withErrors(['apartment_id' => 'Bạn không phải chủ hộ của căn này.']);
        }

        $code = 'INV' . strtoupper(Str::random(6));

        ApartmentInvite::create([
            'apartment_id' => $apartment->id,
            'created_by' => Auth::id(),
            'invite_code' => $code,
            'intended_relationship' => $validated['intended_relationship'],
            'status' => 'active',
            'expired_at' => $validated['expired_at'] ?? null,
        ]);

        return redirect()->back()->with('success', "Đã tạo mã mời: {$code}");
    }
}
