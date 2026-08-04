<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Resident không thể truy cập route admin.
     */
    public function test_resident_cannot_access_admin_routes(): void
    {
        $resident = User::factory()->create([
            'role'  => 'resident',
            'phone' => '0912345678',
        ]);

        $this->actingAs($resident);

        $response = $this->get('/admin/apartments');
        // Phải bị redirect hoặc forbidden, không phải 200
        $this->assertNotEquals(200, $response->status());
    }

    /**
     * Admin có thể truy cập trang dashboard admin.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role'  => 'admin',
            'phone' => '0987654321',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/');
        $response->assertStatus(200);
    }

    /**
     * Khách chưa đăng nhập bị redirect về trang login khi truy cập resident route.
     */
    public function test_guest_is_redirected_from_resident_routes(): void
    {
        $response = $this->get('/resident/invoices');
        $response->assertRedirect();
        $this->assertStringContainsString('login', $response->headers->get('location') ?? '');
    }

    /**
     * Khách chưa đăng nhập bị redirect khi truy cập admin route.
     */
    public function test_guest_is_redirected_from_admin_routes(): void
    {
        $response = $this->get('/admin/');
        $response->assertRedirect();
    }

    /**
     * Admin không thể truy cập route technician (khác prefix).
     */
    public function test_admin_cannot_pretend_to_be_technician(): void
    {
        $admin = User::factory()->create([
            'role'  => 'admin',
            'phone' => '0987654322',
        ]);

        $this->actingAs($admin);

        // Technician route yêu cầu middleware technician
        $response = $this->get('/technician/tickets/my-tasks');
        $this->assertNotEquals(200, $response->status());
    }
}
