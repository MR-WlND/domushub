<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    /**
     * Đăng nhập API (Sanctum Token)
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email hoặc mật khẩu không đúng.'], 401);
        }

        if ($user->status === 'banned') {
            return response()->json(['message' => 'Tài khoản đã bị khóa. Liên hệ Ban quản lý.'], 403);
        }

        if ($user->role !== 'resident') {
            return response()->json(['message' => 'Tài khoản không phải cư dân.'], 403);
        }

        $token = $user->createToken('resident-app')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Đăng xuất API (thu hồi token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Đăng xuất thành công.']);
    }
}
