<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordCodeMail;
use App\Models\ApartmentMember;
use App\Models\Invitation;
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

        if ($user->role !== 'admin') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản này không có quyền truy cập vào admin.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('home');
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

        if ($user->role !== 'security') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản này không có quyền truy cập vào security.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('security.dashboard');
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

        if (! in_array($user->role, ['resident', 'owner'], true)) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Tài khoản này không có quyền truy cập vào resident.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('resident.dashboard');
    }

    // Đăng ký
    public function showResidentRegister(): View
    {
        $apartments = DB::table('apartments')
            ->select('apartments.id', 'apartments.apartment_number', 'floors.floor_number', 'blocks.name as building_name')
            ->join('floors', 'apartments.floor_id', 'floors.id')
            ->join('blocks', 'floors.block_id', 'blocks.id')
            ->orderBy('blocks.name')
            ->orderBy('floors.floor_number')
            ->orderBy('apartments.apartment_number')
            ->get();

        return view('auth.resident.register', compact('apartments'));
    }

    public function registerResident(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+]+$/', 'unique:users,phone'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'apartment_id' => ['required', 'exists:apartments,id'],
            'invite_code' => [
                'required',
                'string',
                'max:20',
                Rule::exists('invitations', 'code')->where(function ($query) {
                    $query->where('type', 'resident_master')
                        ->where('status', 'active')
                        ->whereRaw('uses_count < max_uses')
                        ->where(function ($q) {
                            $q->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                        });
                }),
            ],
            'members' => ['nullable', 'array'],
            'members.*.name' => ['required_with:members', 'string', 'max:150'],
            'members.*.relationship' => ['required_with:members', 'string', 'max:50'],
            'members.*.birth_year' => ['nullable', 'integer', 'between:1900,' . now()->year],
        ]);

        $invite = DB::table('invitations')
            ->where('code', $validated['invite_code'])
            ->where('type', 'resident_master')
            ->where('status', 'active')
            ->whereRaw('uses_count < max_uses')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $invite) {
            return back()->withErrors([
                'invite_code' => 'Mã mời cư dân không hợp lệ hoặc đã hết hạn.',
            ])->onlyInput(['name', 'phone', 'email', 'apartment_id', 'invite_code']);
        }

        $apartment = DB::table('apartments')
            ->join('floors', 'apartments.floor_id', 'floors.id')
            ->where('apartments.id', $validated['apartment_id'])
            ->select('floors.block_id as building_id')
            ->first();

        if (! $apartment || $apartment->building_id !== $invite->building_id) {
            return back()->withErrors([
                'apartment_id' => 'Căn hộ không thuộc tòa nhà của mã mời RES.',
            ])->onlyInput(['name', 'phone', 'email', 'apartment_id', 'invite_code']);
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'owner',
                'status' => 'active',
            ]);

            ApartmentMember::create([
                'apartment_id' => $validated['apartment_id'],
                'user_id' => $user->id,
                'name' => $validated['name'],
                'birth_year' => null,
                'relationship' => 'owner',
                'status' => 'pending',
            ]);

            if (! empty($validated['members'])) {
                foreach ($validated['members'] as $memberData) {
                    if (empty($memberData['name']) || empty($memberData['relationship'])) {
                        continue;
                    }

                    ApartmentMember::create([
                        'apartment_id' => $validated['apartment_id'],
                        'name' => $memberData['name'],
                        'birth_year' => $memberData['birth_year'] ?? null,
                        'relationship' => $memberData['relationship'],
                        'status' => 'pending',
                    ]);
                }
            }

            $invitation = Invitation::find($invite->id);
            $invitation->increment('uses_count');

            if ($invitation->uses_count >= $invitation->max_uses) {
                $invitation->status = 'used';
                $invitation->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('resident.login')->with('status', 'Đăng ký tài khoản chủ hộ thành công. Vui lòng đăng nhập để tiếp tục.');
    }

    public function showMemberRegister(): View
    {
        return view('auth.member.register');
    }

    public function registerMember(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+]+$/', 'unique:users,phone'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invite_code' => [
                'required',
                'string',
                'max:20',
                Rule::exists('invitations', 'code')->where(function ($query) {
                    $query->where('type', 'member_invite')
                        ->where('status', 'active')
                        ->whereRaw('uses_count < max_uses')
                        ->where(function ($q) {
                            $q->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                        });
                }),
            ],
        ]);

        $invite = DB::table('invitations')
            ->where('code', $validated['invite_code'])
            ->where('type', 'member_invite')
            ->where('status', 'active')
            ->whereRaw('uses_count < max_uses')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $invite) {
            return back()->withErrors([
                'invite_code' => 'Mã mời MEM không hợp lệ hoặc đã hết hạn.',
            ])->onlyInput(['name', 'phone', 'email', 'invite_code']);
        }

        // Nếu invitation gắn trực tiếp với một apartment_member thì dùng member đó,
        // còn nếu là mã MEM chung cho căn hộ (không có apartment_member_id),
        // thì lấy nhân khẩu đã được xác thực (verified) và chưa có tài khoản.
        $invitation = Invitation::find($invite->id);

        if ($invitation->apartment_member_id) {
            $member = ApartmentMember::find($invitation->apartment_member_id);

            if (! $member || $member->user_id) {
                return back()->withErrors([
                    'invite_code' => 'Mã mời không thể sử dụng vì thành viên đã có tài khoản hoặc không tồn tại.',
                ])->onlyInput(['name', 'phone', 'email', 'invite_code']);
            }
        } else {
            $member = ApartmentMember::where('apartment_id', $invitation->apartment_id)
                ->whereNull('user_id')
                ->where('status', 'verified')
                ->orderBy('id')
                ->first();

            if (! $member) {
                return back()->withErrors([
                    'invite_code' => 'Không có nhân khẩu hợp lệ để liên kết với mã mời này.',
                ])->onlyInput(['name', 'phone', 'email', 'invite_code']);
            }
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'resident',
                'status' => 'active',
            ]);

            $member->update(['user_id' => $user->id]);

            $invitation->increment('uses_count');

            if ($invitation->uses_count >= $invitation->max_uses) {
                $invitation->status = 'used';
            }

            $invitation->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('resident.login')->with('status', 'Đăng ký tài khoản thành viên thành công. Vui lòng đăng nhập.');
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

            if (in_array($mailer, ['log', 'array'], true)) {
                return back()->with('error', 'Chức năng gửi email chưa được cấu hình. Vui lòng liên hệ quản trị viên để được hỗ trợ.');
            }

            try {
                Mail::to($user)->send(new ResetPasswordCodeMail($code));
            } catch (\Throwable $exception) {
                report($exception);
                return back()->with('error', 'Không thể gửi mã xác nhận lúc này. Vui lòng thử lại sau ít phút.');
            }
        }

        return redirect()->route('resident.reset-password')->with('status', 'Nếu email của bạn tồn tại trong hệ thống, mã xác nhận đã được gửi. Vui lòng kiểm tra hộp thư của bạn.');
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
            'code.required' => 'Vui lòng nhập mã xác nhận.',
            'code.digits' => 'Mã xác nhận phải gồm 6 chữ số.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
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
        $role = Auth::user() ? Auth::user()->role : null;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($role === 'admin') {
            return redirect()->route('admin.login');
        }

        if ($role === 'security') {
            return redirect()->route('security.login');
        }

        return redirect()->route('resident.login');
    }
}
