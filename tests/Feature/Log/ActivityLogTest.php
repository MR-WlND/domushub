<?php

namespace Tests\Feature\Log;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách nhật ký hệ thống.
     */
    public function test_admin_can_view_system_logs(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.system-logs.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin xem nhật ký tài chính.
     */
    public function test_admin_can_view_finance_logs(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.finance-logs.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin xem lịch sử thông báo.
     */
    public function test_admin_can_view_notification_logs(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.notification-logs.index'));
        $response->assertStatus(200);
    }
}
