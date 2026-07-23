<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CleaningMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('cleaning.login');
        }

        if (Auth::user()->role !== 'cleaning') {
            abort(403, 'Bạn không có quyền truy cập vào chức năng Vệ sinh.');
        }

        return $next($request);
    }
}
