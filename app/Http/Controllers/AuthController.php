<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordCodeMail;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // Login
    public function showAdminLogin(): View
    {
        return view('auth.admin.login');
    }

    public function showSecurityLogin(): View
    {
        return view('auth.security.login');
    }

    public function showResidentLogin(): View
    {
        return view('auth.resident.login');
    }

    public function loginAdmin(Request $request): RedirectResponse
    {
        return $this->handleLogin($request, 'admin');
    }

    public function loginSecurity(Request $request): RedirectResponse
    {
        return $this->handleLogin($request, 'security');
    }

    public function loginResident(Request $request): RedirectResponse
    {
        return $this->handleLogin($request, 'resident');
    }

    private function handleLogin(Request $request, string $role): RedirectResponse
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

    // Đăng ký
    public function showResidentRegister(): View
    {
        return view('auth.resident.register');
    }

    public function registerResident(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+]+$/', 'unique:users,phone'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invite_code' => [
                'required',
                'string',
                'max:50',
                Rule::exists('apartment_invites', 'invite_code')->where(function ($query) {
                    $query->where('status', 'active')
                        ->where('expired_at', '>', now());
                }),
            ],
        ]);

        $invite = DB::table('apartment_invites')
            ->where('invite_code', $validated['invite_code'])
            ->where('status', 'active')
            ->where('expired_at', '>', now())
            ->first();

        if (! $invite) {
            return back()->withErrors([
                'invite_code' => 'Mã mời cư dân không hợp lệ hoặc đã hết hạn.',
            ])->onlyInput(['name', 'phone', 'email', 'invite_code']);
        }

        $currentResidents = DB::table('residents')
            ->where('apartment_id', $invite->apartment_id)
            ->count();

        $maxResidents = (int) ($invite->max_residents ?? 1);

        if ($currentResidents >= $maxResidents) {
            return back()->withErrors([
                'invite_code' => 'Mã mời này đã đạt số người tối đa cho căn hộ này.',
            ])->onlyInput(['name', 'phone', 'email', 'invite_code']);
        }

        DB::transaction(function () use ($validated, $invite, $currentResidents, $maxResidents) {
            $user = User::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'resident',
                'status' => 'active',
            ]);

            DB::table('residents')->insert([
                'user_id' => $user->id,
                'apartment_id' => $invite->apartment_id,
                'invite_id' => $invite->id,
                'relationship' => $invite->intended_relationship,
                'temporary_status' => 'permanent',
                'start_date' => now()->toDateString(),
                'end_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $usedCount = (int) ($invite->used_count ?? 0) + 1;
            $nextStatus = $usedCount >= $maxResidents ? 'used' : 'active';

            DB::table('apartment_invites')
                ->where('id', $invite->id)
                ->update([
                    'used_count' => $usedCount,
                    'status' => $nextStatus,
                    'updated_at' => now(),
                ]);
        });

        return redirect()->route('resident.login')->with('status', 'Đăng ký tài khoản thành công. Vui lòng đăng nhập để tiếp tục.');
    }

    private function createNewInvite(int $apartmentId, int $createdBy, string $relationship, int $maxResidents): void
    {
        DB::table('apartment_invites')->insert([
            'apartment_id' => $apartmentId,
            'created_by' => $createdBy,
            'invite_code' => strtoupper(uniqid('INVITE-')),
            'intended_relationship' => $relationship,
            'status' => 'active',
            'max_residents' => $maxResidents,
            'used_count' => 0,
            'expired_at' => now()->addDays(7),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Quên mật khẩu
    public function showForgotPassword(): View
    {
        return view('auth.resident.forgot-password');
    }

    public function showResetPassword(): View
    {
        return view('auth.resident.reset-password');
    }

    public function sendResetCode(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->email));
        $code = (string) random_int(100000, 999999);

        Cache::put("resident_reset_{$email}", $code, now()->addMinutes(15));

        $user = User::where('email', $email)->first();

        if ($user) {
            Mail::to($user)->send(new ResetPasswordCodeMail($code));
        }

        return redirect()->route('resident.reset-password')->with('status', 'Nếu email của bạn tồn tại trong hệ thống, mã xác nhận đã được gửi. Vui lòng kiểm tra hộp thư của bạn.');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();
        $storedCode = Cache::get("resident_reset_{$email}");

        if (! $user || ! is_string($storedCode) || $storedCode !== $request->code) {
            return back()->withErrors([
                'code' => 'Mã xác nhận không đúng hoặc đã hết hạn.',
            ])->onlyInput('email');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Cache::forget("resident_reset_{$email}");

        return redirect()->route('resident.login')->with('status', 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.');
    }

    // Đăng xuất
    public function logout(Request $request): RedirectResponse
    {
        $role = Auth::user()?->role;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return match ($role) {
            'admin' => redirect()->route('admin.login'),
            'security' => redirect()->route('security.login'),
            default => redirect()->route('resident.login'),
        };
    }
}
