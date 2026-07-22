<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('staff.login');
        }

        if (Auth::user()->role !== 'staff') {
            abort(403, 'Bạn không có quyền truy cập vào chức năng Kế toán.');
        }

        return $next($request);
    }
}
