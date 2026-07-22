<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManagerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('manager.login');
        }

        if (Auth::user()->role !== 'manager') {
            abort(403, 'Bạn không có quyền truy cập vào chức năng Quản lý.');
        }

        return $next($request);
    }
}
