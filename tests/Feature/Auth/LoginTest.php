<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Resident đăng nhập thành công sẽ redirect về dashboard.
     */
    public function test_resident_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'role'     => 'resident',
            'phone'    => '0912345678',
            'status'   => 'active',
            'email'    => 'resident@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('resident.login.submit'), [
            'email'    => 'resident@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Đăng nhập với mật khẩu sai sẽ trả về lỗi validation.
     */
    public function test_resident_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'role'   => 'resident',
            'phone'  => '0912345678',
            'status' => 'active',
            'email'  => 'resident2@test.com',
        ]);

        $response = $this->post(route('resident.login.submit'), [
            'email'    => 'resident2@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * Đăng nhập với email không tồn tại sẽ thất bại.
     */
    public function test_login_fails_with_nonexistent_email(): void
    {
        $response = $this->post(route('resident.login.submit'), [
            'email'    => 'nobody@notfound.com',
            'password' => 'anypassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * Trang đăng nhập của resident trả về HTTP 200.
     */
    public function test_resident_login_page_is_accessible(): void
    {
        $response = $this->get(route('resident.login'));
        $response->assertStatus(200);
    }

    /**
     * Admin đăng nhập thành công redirect về dashboard admin.
     */
    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = User::factory()->create([
            'role'   => 'admin',
            'phone'  => '0987654321',
            'status' => 'active',
            'email'  => 'admin@test.com',
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email'    => 'admin@test.com',
            'password' => 'password', // Default password in UserFactory
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Trang đăng nhập admin trả về HTTP 200.
     */
    public function test_admin_login_page_is_accessible(): void
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
    }
}
