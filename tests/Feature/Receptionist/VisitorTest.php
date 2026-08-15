<?php

namespace Tests\Feature\Receptionist;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class VisitorTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Lễ tân có thể truy cập trang Đăng ký khách vãng lai.
     */
    public function test_receptionist_can_view_walk_in_visitor_page(): void
    {
        $receptionist = User::factory()->create([
            'role'   => 'receptionist',
            'phone'  => '0988776655',
            'status' => 'active',
        ]);

        $this->actingAs($receptionist);

        $response = $this->get(route('receptionist.walk-in.index'));
        $response->assertStatus(200);
    }

    /**
     * Lễ tân có thể truy cập trang Lịch sử ra vào của khách.
     */
    public function test_receptionist_can_view_visitor_log_page(): void
    {
        $receptionist = User::factory()->create([
            'role'   => 'receptionist',
            'phone'  => '0988776654',
            'status' => 'active',
        ]);

        $this->actingAs($receptionist);

        $response = $this->get(route('receptionist.visitor-log.index'));
        $response->assertStatus(200);
    }
}
