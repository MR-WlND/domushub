<?php

namespace Tests\Feature\Utility;

use App\Models\UtilityMeter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class UtilityMeterTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách ghi chỉ số điện nước.
     */
    public function test_admin_can_view_utility_readings(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.utility-readings.index'));
        $response->assertStatus(200);
    }

    /**
     * Kỹ thuật viên xem form nhập số điện nước.
     */
    public function test_technician_can_view_create_reading_form(): void
    {
        $technician = $this->makeTechnician();
        $this->actingAs($technician);

        $response = $this->get(route('admin.utility-readings.create'));
        $response->assertStatus(200);
    }

    /**
     * Admin xem lịch sử ghi chỉ số.
     */
    public function test_admin_can_view_utility_logs(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.utility-logs.index'));
        $response->assertStatus(200);
    }
}
