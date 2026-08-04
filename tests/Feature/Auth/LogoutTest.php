<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Resident đăng xuất thành công và được redirect về trang login.
     */
    public function test_authenticated_resident_can_logout(): void
    {
        $user = User::factory()->create([
            'role'  => 'resident',
            'phone' => '0912345678',
        ]);

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->post(route('logout'));

        $response->assertRedirect();
        $this->assertGuest();
    }

    /**
     * Khách vãng lai (chưa đăng nhập) POST /logout vẫn redirect bình thường.
     */
    public function test_guest_logout_does_not_crash(): void
    {
        $response = $this->post(route('logout'));

        // Laravel mặc định sẽ redirect (không crash)
        $response->assertRedirect();
        $this->assertGuest();
    }

    /**
     * Admin đăng xuất thành công.
     */
    public function test_authenticated_admin_can_logout(): void
    {
        $admin = User::factory()->create([
            'role'  => 'admin',
            'phone' => '0987654321',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('logout'));

        $response->assertRedirect();
        $this->assertGuest();
    }
}
