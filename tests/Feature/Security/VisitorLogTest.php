<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class VisitorLogTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Bảo vệ xem danh sách check-in khách.
     */
    public function test_security_can_view_visitor_checkin_page(): void
    {
        $security = User::factory()->create([
            'role'   => 'security',
            'phone'  => '0966778899',
            'status' => 'active',
        ]);

        $this->actingAs($security);

        $response = $this->get(route('security.visitor-check.index'));
        $response->assertStatus(200);
    }

    /**
     * Bảo vệ xem trang đăng ký khách vãng lai.
     */
    public function test_security_can_view_walkin_visitor_page(): void
    {
        $security = User::factory()->create([
            'role'   => 'security',
            'phone'  => '0966778898',
            'status' => 'active',
        ]);

        $this->actingAs($security);

        $response = $this->get(route('security.walk-in.index'));
        $response->assertStatus(200);
    }
}
