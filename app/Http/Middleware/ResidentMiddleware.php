<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResidentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('resident.login');
        }

        if (Auth::user()->role !== 'resident') {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
