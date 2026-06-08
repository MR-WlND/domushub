<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Các role được phép truy cập admin panel.
     * Mỗi role sẽ thấy menu khác nhau tuỳ theo phân quyền trong sidebar.
     */
    protected array $allowedRoles = ['admin', 'manager', 'staff', 'technician'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! in_array(Auth::user()->role, $this->allowedRoles, true)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
