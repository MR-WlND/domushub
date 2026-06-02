<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedUser
{
    /**
     * Kiểm tra nếu user bị banned → thu hồi Token Sanctum, hủy session, đá ra.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status === 'banned') {
            $user = Auth::user();

            // Thu hồi tất cả Sanctum tokens
            $user->tokens()->delete();

            // Logout khỏi web session
            Auth::logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            // Nếu là API request → trả JSON 403
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Ban quản lý.',
                ], 403);
            }

            return redirect()->route('resident.login')
                ->withErrors(['email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Ban quản lý.']);
        }

        return $next($request);
    }
}
