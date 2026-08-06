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
     * Bảo vệ truy cập trang quét xe vào.
     */
    public function test_security_can_view_vehicle_checkin_page(): void
    {
        $security = User::factory()->create([
            'role'   => 'security',
            'phone'  => '0966778899',
            'status' => 'active',
        ]);

        $this->actingAs($security);

        $response = $this->get(route('security.vehicle-checkin.index'));
        $response->assertStatus(200);
    }
}

