<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TechnicianMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('technician.login');
        }

        if (Auth::user()->role !== 'technician') {
            abort(403, 'Bạn không có quyền truy cập vào chức năng Kỹ thuật viên.');
        }

        $requestRouteName = $request->route()->getName();
        // Since we are using portal routes, the name might be technician.invoices.*
        // Or we can check URL paths.
        if (
            $requestRouteName && (
                str_starts_with($requestRouteName, 'technician.invoices.') ||
                str_starts_with($requestRouteName, 'technician.service-prices.') ||
                $requestRouteName === 'technician.statistics.finance' ||
                str_starts_with($requestRouteName, 'technician.finance-logs.')
            )
        ) {
            return redirect()->route('technician.tickets.my-tasks');
        }

        return $next($request);
    }
}
