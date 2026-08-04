<?php

namespace App\Mail\ResetPasswordCodeMail;
namespace App\Http\Controllers;

use App\Mail\ResetPasswordCodeMail;
use App\Models\Apartment;
use App\Models\ApartmentInvite;
use App\Models\ApartmentMember;
use App\Models\Resident;
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

    public function showManagerLogin(): View
    {
        return view('auth.manager.login');
    }

    public function showStaffLogin(): View
    {
        return view('auth.staff.login');
    }

    public function showTechnicianLogin(): View
    {
        return view('auth.technician.login');
    }

    public function showSecurityLogin(): View
    {
        return view('auth.security.login');
    }

    public function showCleaningLogin(): View
    {
        return view('auth.cleaning.login');
    }

    public function showReceptionistLogin(): View
    {
        return view('auth.receptionist.login');
    }

    public function showResidentLogin(): View
    {
        return view('auth.resident.login');
    }

    public function loginAdmin(Request $request): RedirectResponse
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

        if ($user->status !== 'active') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ BQL.',
            ])->onlyInput('email');
        }

        if ($user->role !== 'admin') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản này không có quyền truy cập vào admin portal.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        \App\Helpers\SystemLogger::log('Đăng nhập', 'Hệ thống');

        return redirect()->route($this->adminHomeRouteFor($user->role));
    }

    public function loginManager(Request $request): RedirectResponse
    {
        return $this->processLogin($request, 'manager', 'Quản lý');
    }

    public function loginStaff(Request $request): RedirectResponse
    {
        return $this->processLogin($request, 'staff', 'Kế toán');
    }

    public function loginTechnician(Request $request): RedirectResponse
    {
        return $this->processLogin($request, 'technician', 'Kỹ thuật viên');
    }

    private function processLogin(Request $request, string $role, string $roleName): RedirectResponse
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

        if ($user->status !== 'active') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ BQL.',
            ])->onlyInput('email');
        }

        if ($user->role !== $role) {
            Auth::logout();

            return back()->withErrors([
                'email' => "Tài khoản này không có quyền truy cập vào cổng $roleName.",
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        \App\Helpers\SystemLogger::log('Đăng nhập', 'Hệ thống');

        return redirect()->route($this->adminHomeRouteFor($user->role));
    }

    public function loginSecurity(Request $request): RedirectResponse
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

        if ($user->status !== 'active') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ BQL.',
            ])->onlyInput('email');
        }

        if ($user->role !== 'security') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản này không có quyền truy cập vào security.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        \App\Helpers\SystemLogger::log('Đăng nhập', 'Hệ thống');

        return redirect()->route('security.dashboard');
    }

    public function loginCleaning(Request $request): RedirectResponse
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

        if ($user->status !== 'active') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ BQL.',
            ])->onlyInput('email');
        }

        if ($user->role !== 'cleaning') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản này không có quyền truy cập vào cổng Vệ sinh.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        \App\Helpers\SystemLogger::log('Đăng nhập', 'Hệ thống');

        return redirect()->route('cleaning.dashboard');
    }

    public function loginReceptionist(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ BQL.',
            ])->onlyInput('email');
        }

        if ($user->role !== 'receptionist') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản này không có quyền truy cập vào cổng Lễ tân.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        \App\Helpers\SystemLogger::log('Đăng nhập', 'Hệ thống');

        return redirect()->route('receptionist.dashboard');
    }

    public function loginResident(Request $request): RedirectResponse
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

        if ($user->status !== 'active') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ BQL.',
            ])->onlyInput('email');
        }

        if ($user->role !== 'resident') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản này không có quyền truy cập vào resident.',
            ])->onlyInput('email');
        }

        // Chặn đăng nhập nếu cư dân chỉ có trạng thái pending
        $hasActiveResident = \App\Models\Resident::where('user_id', $user->id)->where('status', 'active')->exists();
        $hasPendingResident = \App\Models\Resident::where('user_id', $user->id)->where('status', 'pending')->exists();
        
        if (!$hasActiveResident && $hasPendingResident) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản đang chờ Ban quản lý phê duyệt.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        \App\Helpers\SystemLogger::log('Đăng nhập', 'Hệ thống');

        return redirect()->route('resident.dashboard');
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
            'phone' => ['required', 'string', 'regex:/^(03|05|07|08|09)[0-9]{8}$/', 'unique:users,phone'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invite_code' => ['required', 'string', 'max:50'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại phải gồm đúng 10 chữ số, bắt đầu bằng số 0 và thuộc mạng Việt Nam (đầu số 03, 05, 07, 08, 09).',
            'phone.unique' => 'Số điện thoại đã tồn tại trong hệ thống.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'invite_code.required' => 'Vui lòng nhập mã mời cư dân.',
        ]);

        // Tìm mã mời hợp lệ
        $invite = ApartmentInvite::where('invite_code', $validated['invite_code'])
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
            })->first();

        if (! $invite || ! $invite->isValid()) {
            return back()->withErrors([
                'invite_code' => 'Mã mời cư dân không hợp lệ hoặc đã hết hạn.',
            ])->onlyInput(['name', 'phone', 'email', 'invite_code']);
        }

        // Mã mời phải được gắn với căn hộ cụ thể
        if (! $invite->apartment_id) {
            return back()->withErrors([
                'invite_code' => 'Mã mời này chưa được gắn với căn hộ cụ thể. Vui lòng liên hệ quản trị viên.',
            ])->onlyInput(['name', 'phone', 'email', 'invite_code']);
        }

        // Kiểm tra ràng buộc Chủ hộ (Owner)
        if ($invite->intended_relationship === 'owner') {
            $hasOwner = Resident::where('apartment_id', $invite->apartment_id)
                ->where('relationship', 'owner')
                ->whereNull('deleted_at')
                ->exists();

            if ($hasOwner) {
                return back()->withErrors([
                    'invite_code' => 'Căn hộ liên kết với mã mời này đã có chủ hộ đăng ký trong hệ thống.',
                ])->onlyInput(['name', 'phone', 'email', 'invite_code']);
            }
        }

        $apartment = Apartment::with(['floor.block'])->findOrFail($invite->apartment_id);

        // Kiểm tra giới hạn số lượng cư dân tối đa (10 người)
        $currentCount = Resident::where('apartment_id', $apartment->id)
            ->whereNull('deleted_at')
            ->whereIn('status', ['active', 'pending'])
            ->count();
        if ($currentCount >= 10) {
            return back()->withErrors([
                'invite_code' => 'Căn hộ liên kết với mã mời này đã đạt giới hạn cư dân tối đa (10 người).',
            ])->onlyInput(['name', 'phone', 'email', 'invite_code']);
        }

        DB::transaction(function () use ($validated, $invite, $apartment) {
            $user = User::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'password' => $validated['password'], // Password tự động được hash nhờ config Cast trong User Model
                'role' => 'resident',
                'status' => 'active',

                // CẬP NHẬT: Gán trực tiếp apartment_id từ bảng mã mời sang
                'apartment_id' => $invite->apartment_id,
            ]);

            Resident::create([
                'user_id' => $user->id,
                'apartment_id' => $apartment->id,
                'invite_id' => $invite->id,
                'relationship' => $invite->intended_relationship,
                'temporary_status' => 'permanent',
                'start_date' => now()->toDateString(),
                'status' => $invite->intended_relationship === 'owner' ? 'active' : 'pending',
            ]);

            $invite->uses_count += 1;
            if ($invite->uses_count >= $invite->max_uses) {
                $invite->status = 'used';
            }
            $invite->save();
        });

        return redirect()->route('resident.login')->with('status', 'Đăng ký tài khoản thành công và đã được thêm vào căn hộ ' . $apartment->apartment_number . '. Vui lòng đăng nhập.');
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
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
        ]);

        $email = strtolower(trim($request->email));
        $code = (string) random_int(100000, 999999);

        Cache::put("resident_reset_{$email}", $code, now()->addMinutes(15));

        $user = User::where('email', $email)->first();

        if ($user) {
            $mailer = (string) config('mail.default');

            if ($mailer === 'log' || empty($mailer)) {
                return back()->with('error', 'Chức năng gửi email chưa được cấu hình. Vui lòng liên hệ quản trị viên để được hỗ trợ.');
            }

            try {
                Mail::to($user)->send(new ResetPasswordCodeMail($code));
            } catch (\Throwable $exception) {
                report($exception);
                return back()->with('error', 'Không thể gửi mã xác nhận lúc này. Vui lòng thử lại sau ít phút.');
            }
        }

        return redirect()->route('resident.reset-password')->with('status', 'Mã xác nhận đã được gửi đến email của bạn nếu email hợp lệ.');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'code.digits' => 'Mã xác nhận phải gồm 6 chữ số.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

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
        $role = Auth::user() ? Auth::user()->role : null;

        if (Auth::check()) {
            \App\Helpers\SystemLogger::log('Đăng xuất', 'Hệ thống');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect cho từng role
        if ($role === 'admin') {
            return redirect()->route('admin.login');
        }
        if ($role === 'manager') {
            return redirect()->route('manager.login');
        }
        if ($role === 'staff') {
            return redirect()->route('staff.login');
        }
        if ($role === 'technician') {
            return redirect()->route('technician.login');
        }

        if ($role === 'security') {
            return redirect()->route('security.login');
        }

        if ($role === 'cleaning') {
            return redirect()->route('cleaning.login');
        }

        if ($role === 'receptionist') {
            return redirect()->route('receptionist.login');
        }

        return redirect()->route('resident.login');
    }

    private function adminHomeRouteFor(string $role): string
    {
        return match ($role) {
            'manager'      => 'manager.dashboard',
            'staff'        => 'staff.utility-readings.index',
            'technician'   => 'technician.tickets.my-tasks',
            default        => 'admin.dashboard',
        };
    }
}
