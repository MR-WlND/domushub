<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Authentication & Authorization Test Suite
 * Covers: TC_AUTH_03 → TC_AUTH_30
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // TC_AUTH_03: Đăng ký thất bại - Trùng SĐT
    // -------------------------------------------------------------------------
    public function test_register_with_duplicate_phone_shows_error(): void
    {
        User::factory()->create(['phone' => '0912345678', 'role' => 'resident']);

        $response = $this->post(route('resident.register.submit'), [
            'name'                  => 'Nguyen Van A',
            'phone'                 => '0912345678',
            'email'                 => 'newuser@test.com',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
            'invite_code'           => 'INVALID_CODE',
        ]);

        $response->assertSessionHasErrors(['phone']);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_04: Đăng ký thất bại - Email sai định dạng
    // -------------------------------------------------------------------------
    public function test_register_with_invalid_email_format_shows_error(): void
    {
        $response = $this->post(route('resident.register.submit'), [
            'name'                  => 'Nguyen Van A',
            'phone'                 => '0912345001',
            'email'                 => 'test@com',   // email sai format
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
            'invite_code'           => 'SOME_CODE',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_05: Đăng ký thất bại - Mật khẩu yếu (< 8 ký tự)
    // -------------------------------------------------------------------------
    public function test_register_with_short_password_shows_error(): void
    {
        $response = $this->post(route('resident.register.submit'), [
            'name'                  => 'Nguyen Van A',
            'phone'                 => '0912345002',
            'email'                 => 'user2@test.com',
            'password'              => '12345',     // < 8 ký tự
            'password_confirmation' => '12345',
            'invite_code'           => 'SOME_CODE',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_06: Đăng ký thất bại - Mật khẩu xác nhận không khớp
    // -------------------------------------------------------------------------
    public function test_register_with_mismatched_password_confirmation_shows_error(): void
    {
        $response = $this->post(route('resident.register.submit'), [
            'name'                  => 'Nguyen Van A',
            'phone'                 => '0912345003',
            'email'                 => 'user3@test.com',
            'password'              => 'Abc@123456',
            'password_confirmation' => 'Abc@456789',  // không khớp
            'invite_code'           => 'SOME_CODE',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_07: Đăng ký thất bại - Thiếu field bắt buộc (phone)
    // -------------------------------------------------------------------------
    public function test_register_without_required_field_shows_error(): void
    {
        $response = $this->post(route('resident.register.submit'), [
            'name'                  => 'Nguyen Van A',
            // thiếu 'phone'
            'email'                 => 'user4@test.com',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
            'invite_code'           => 'SOME_CODE',
        ]);

        $response->assertSessionHasErrors(['phone']);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_12: Đăng nhập Admin thành công → redirect /admin/dashboard
    // -------------------------------------------------------------------------
    public function test_admin_login_success_redirects_to_dashboard(): void
    {
        $admin = User::factory()->create([
            'email'    => 'admin@test.com',
            'password' => bcrypt('Admin@123'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email'    => 'admin@test.com',
            'password' => 'Admin@123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_15: Đăng nhập thất bại - Sai mật khẩu
    // -------------------------------------------------------------------------
    public function test_login_with_wrong_password_shows_error(): void
    {
        User::factory()->create([
            'email'    => 'resident@test.com',
            'password' => bcrypt('CorrectPass123'),
            'role'     => 'resident',
        ]);

        $response = $this->post(route('resident.login.submit'), [
            'email'    => 'resident@test.com',
            'password' => 'WrongPass123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_16: Đăng nhập thất bại - Tài khoản chưa đăng ký
    // -------------------------------------------------------------------------
    public function test_login_with_nonexistent_account_shows_error(): void
    {
        $response = $this->post(route('resident.login.submit'), [
            'email'    => 'notfound@gmail.com',
            'password' => 'SomePass123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_17: Đăng nhập thất bại - Tài khoản bị khóa (locked)
    // ❌ Expected FAIL → phát hiện bug: hệ thống chưa chặn status=locked
    // -------------------------------------------------------------------------
    public function test_login_with_locked_account_shows_locked_error(): void
    {
        User::factory()->create([
            'email'    => 'locked@test.com',
            'password' => bcrypt('Pass@123'),
            'role'     => 'resident',
            'status'   => 'locked',
        ]);

        $response = $this->post(route('resident.login.submit'), [
            'email'    => 'locked@test.com',
            'password' => 'Pass@123',
        ]);

        // Phải có lỗi và KHÔNG được đăng nhập
        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_18: Đăng nhập thất bại - Tài khoản chưa duyệt (pending)
    // ❌ Expected FAIL → phát hiện bug: hệ thống chưa chặn status=pending
    // -------------------------------------------------------------------------
    public function test_login_with_pending_account_shows_pending_error(): void
    {
        User::factory()->create([
            'email'    => 'pending@test.com',
            'password' => bcrypt('Pass@123'),
            'role'     => 'resident',
            'status'   => 'pending',
        ]);

        $response = $this->post(route('resident.login.submit'), [
            'email'    => 'pending@test.com',
            'password' => 'Pass@123',
        ]);

        // Phải có lỗi và KHÔNG được đăng nhập
        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_20: Remember Me - Ghi nhớ đăng nhập
    // -------------------------------------------------------------------------
    public function test_login_with_remember_me_sets_cookie(): void
    {
        User::factory()->create([
            'email'    => 'resident2@test.com',
            'password' => bcrypt('Pass@123'),
            'role'     => 'resident',
            'status'   => 'active',
        ]);

        $response = $this->post(route('resident.login.submit'), [
            'email'    => 'resident2@test.com',
            'password' => 'Pass@123',
            'remember' => '1',
        ]);

        $response->assertCookieNotExpired('remember_web_' . sha1('Illuminate\Auth\SessionGuard' . 'web' . User::class));
        $this->assertAuthenticated();
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_21: Phân quyền - Cư dân vào URL Admin → 403 hoặc redirect
    // -------------------------------------------------------------------------
    public function test_resident_cannot_access_admin_dashboard(): void
    {
        $resident = User::factory()->create(['role' => 'resident', 'status' => 'active']);
        $this->actingAs($resident);

        $response = $this->get(route('admin.dashboard'));

        // Phải bị chặn: 403 hoặc redirect
        $this->assertContains($response->status(), [302, 403]);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_22: Phân quyền - Bảo vệ vào system settings → 403
    // -------------------------------------------------------------------------
    public function test_security_role_cannot_access_system_settings(): void
    {
        $security = User::factory()->create(['role' => 'security', 'status' => 'active']);
        $this->actingAs($security);

        $response = $this->get(route('admin.users.index'));

        $this->assertContains($response->status(), [302, 403]);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_24: Phân quyền - Guest chưa đăng nhập vào /resident/home → redirect login
    // -------------------------------------------------------------------------
    public function test_guest_cannot_access_resident_dashboard_and_is_redirected(): void
    {
        $response = $this->get(route('resident.dashboard'));

        $response->assertRedirect();
        $this->assertStringContainsString('login', $response->headers->get('Location'));
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_23: IDOR - Cư dân không xem được hóa đơn người khác
    // -------------------------------------------------------------------------
    public function test_resident_cannot_view_other_residents_invoice(): void
    {
        $resident1 = User::factory()->create(['role' => 'resident', 'status' => 'active']);
        $resident2 = User::factory()->create(['role' => 'resident', 'status' => 'active']);

        // Tạo hóa đơn thuộc resident2
        $invoice = \App\Models\Invoice::create([
            'apartment_id' => 1,
            'month'        => 7,
            'year'         => 2026,
            'total_amount' => 500000,
            'status'       => 'unpaid',
        ]);

        // resident1 thử xem hóa đơn của resident2
        $this->actingAs($resident1);
        $response = $this->get(route('resident.invoices.show', $invoice->id));

        // Phải bị chặn
        $this->assertContains($response->status(), [302, 403, 404]);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_25: Quên mật khẩu - Email hợp lệ → gửi OTP
    // -------------------------------------------------------------------------
    public function test_forgot_password_with_valid_email_accepts_request(): void
    {
        User::factory()->create([
            'email'  => 'cudan@gmail.com',
            'role'   => 'resident',
            'status' => 'active',
        ]);

        $response = $this->post(route('resident.forgot-password.submit'), [
            'email' => 'cudan@gmail.com',
        ]);

        // Phải không có lỗi validation
        $response->assertSessionDoesntHaveErrors(['email']);
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_26: Quên mật khẩu - Email không tồn tại
    // -------------------------------------------------------------------------
    public function test_forgot_password_with_nonexistent_email(): void
    {
        $response = $this->post(route('resident.forgot-password.submit'), [
            'email' => 'fake@gmail.com',
        ]);

        // Không có lỗi validation (email format đúng, chỉ không tìm thấy user)
        $response->assertSessionDoesntHaveErrors(['email']);
        // Hệ thống không nên tiết lộ email có tồn tại hay không (security best practice)
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_28: Đổi mật khẩu trong Cài đặt - Thành công
    // -------------------------------------------------------------------------
    public function test_change_password_with_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('OldPass@123'),
            'role'     => 'resident',
            'status'   => 'active',
        ]);

        $this->actingAs($user);

        $response = $this->put(route('resident.profile.change-password'), [
            'current_password'      => 'OldPass@123',
            'password'              => 'NewPass@456',
            'password_confirmation' => 'NewPass@456',
        ]);

        $response->assertSessionDoesntHaveErrors();
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_29: Đổi mật khẩu - Sai mật khẩu cũ
    // -------------------------------------------------------------------------
    public function test_change_password_with_wrong_current_password_shows_error(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('OldPass@123'),
            'role'     => 'resident',
            'status'   => 'active',
        ]);

        $this->actingAs($user);

        $response = $this->put(route('resident.profile.change-password'), [
            'current_password'      => 'WrongOldPass',  // sai
            'password'              => 'NewPass@456',
            'password_confirmation' => 'NewPass@456',
        ]);

        $response->assertSessionHasErrors();
    }

    // -------------------------------------------------------------------------
    // TC_AUTH_30: Đăng xuất - Xóa session, không thể quay lại
    // -------------------------------------------------------------------------
    public function test_logout_clears_session_and_redirects_to_login(): void
    {
        $user = User::factory()->create([
            'role'   => 'resident',
            'status' => 'active',
        ]);

        $this->actingAs($user);
        $response = $this->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect();
    }
}
