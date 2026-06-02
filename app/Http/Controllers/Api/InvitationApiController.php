<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInviteRequest;
use App\Models\Apartment;
use App\Models\ApartmentInvite;
use App\Models\Resident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvitationApiController extends Controller
{
    /**
     * Lấy danh sách căn hộ mà user hiện tại là owner.
     */
    public function myApartments(Request $request): JsonResponse
    {
        $apartments = Apartment::whereHas('residents', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id)
              ->where('relationship', 'owner');
        })->with('floor.block')->get();

        return response()->json([
            'apartments' => $apartments->map(fn ($apt) => [
                'id' => $apt->id,
                'apartment_number' => $apt->apartment_number,
                'floor' => $apt->floor->name ?? 'Tầng ' . $apt->floor->floor_number,
                'block' => $apt->floor->block->name,
                'status' => $apt->status,
            ]),
        ]);
    }

    /**
     * Chủ hộ tạo mã mời – sử dụng StoreInviteRequest để check quyền owner
     */
    public function store(StoreInviteRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Sinh mã dạng INV + 5 chữ số ngẫu nhiên
        $code = 'INV' . str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);

        $invite = ApartmentInvite::create([
            'apartment_id' => $validated['apartment_id'],
            'created_by' => $request->user()->id,
            'invite_code' => $code,
            'intended_relationship' => $validated['intended_relationship'],
            'status' => 'active',
            'expired_at' => $validated['expired_at'] ?? null,
        ]);

        return response()->json([
            'message' => "Đã tạo mã mời: {$code}",
            'invite' => [
                'id' => $invite->id,
                'code' => $code,
                'apartment_id' => $invite->apartment_id,
                'intended_relationship' => $invite->intended_relationship,
                'status' => $invite->status,
                'expired_at' => $invite->expired_at,
            ],
        ], 201);
    }

    /**
     * Thành viên mới nhập mã và vào đúng phòng.
     */
    public function join(Request $request): JsonResponse
    {
        $request->validate([
            'invite_code' => 'required|string|max:50',
        ]);

        $invite = ApartmentInvite::where('invite_code', $request->invite_code)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
            })
            ->first();

        if (! $invite) {
            return response()->json(['message' => 'Mã mời không hợp lệ hoặc đã hết hạn.'], 422);
        }

        $userId = $request->user()->id;

        // Kiểm tra user đã là cư dân phòng này chưa
        $alreadyResident = Resident::where('user_id', $userId)
            ->where('apartment_id', $invite->apartment_id)
            ->whereNull('deleted_at')
            ->exists();

        if ($alreadyResident) {
            return response()->json(['message' => 'Bạn đã là cư dân của căn hộ này.'], 422);
        }

        DB::transaction(function () use ($invite, $userId) {
            // Insert vào bảng residents
            Resident::create([
                'user_id' => $userId,
                'apartment_id' => $invite->apartment_id,
                'invite_id' => $invite->id,
                'relationship' => $invite->intended_relationship,
                'temporary_status' => 'permanent',
                'start_date' => now()->toDateString(),
            ]);

            // Đổi trạng thái mã mời thành used
            $invite->update(['status' => 'used']);
        });

        return response()->json([
            'message' => 'Đã tham gia căn hộ thành công.',
            'apartment_id' => $invite->apartment_id,
        ]);
    }
}
