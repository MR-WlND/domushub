<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin truy cập trang thống kê tài chính.
     */
    public function test_admin_can_view_finance_statistics(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.statistics.finance'));
        $response->assertStatus(200);
    }

    /**
     * Admin truy cập trang thống kê vận hành.
     */
    public function test_admin_can_view_operations_statistics(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.statistics.operations'));
        $response->assertStatus(200);
    }

    /**
     * Admin truy cập trang thống kê cư dân.
     */
    public function test_admin_can_view_residents_statistics(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.statistics.residents'));
        $response->assertStatus(200);
    }
}
