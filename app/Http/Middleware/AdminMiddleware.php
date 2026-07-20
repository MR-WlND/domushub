<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    protected array $allowedRoles = ['admin', 'manager', 'staff', 'technician'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! in_array(Auth::user()->role, $this->allowedRoles, true)) {
            abort(403, 'Ban khong co quyen truy cap.');
        }

        $user = Auth::user();

        if (
            $user->role === 'technician'
            && $request->routeIs(
                'admin.invoices.*',
                'admin.service-prices.*',
                'admin.statistics.finance',
                'admin.finance-logs.*'
            )
        ) {
            return redirect()->route('admin.tickets.my-tasks');
        }

        return $next($request);
    }
}
