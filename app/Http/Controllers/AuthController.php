<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Admin Login
    public function showAdminLogin()
    {
        return view('auth.admin.login');
    }

    public function loginAdmin(Request $request): RedirectResponse
    {
        return $this->handleLogin($request, 'admin', 'admin.login');
    }

    // Resident Login
    public function showResidentLogin()
    {
        return view('auth.resident.login');
    }

    public function loginResident(Request $request): RedirectResponse
    {
        return $this->handleLogin($request, 'resident', 'resident.login');
    }

    // Security Login
    public function showSecurityLogin()
    {
        return view('auth.security.login');
    }

    public function loginSecurity(Request $request): RedirectResponse
    {
        return $this->handleLogin($request, 'security', 'security.login');
    }

    private function handleLogin(Request $request, string $role, string $loginRoute): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        // Validate user role matches login route
        if ($user->role !== $role) {
            Auth::logout();
            return back()->withErrors([
                'email' => "Tài khoản này không có quyền truy cập vào {$role}.",
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'security' => redirect()->route('security.dashboard'),
            'resident' => redirect()->route('resident.dashboard'),
            default => abort(403, 'Vai trò người dùng chưa được hỗ trợ.'),
        };
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
