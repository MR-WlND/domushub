<?php

namespace Tests\Feature\Staff;

use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class StaffScheduleTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách phòng ban.
     */
    public function test_admin_can_view_departments_list(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.departments.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo phòng ban mới.
     */
    public function test_admin_can_create_department(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654322']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.departments.store'), [
            'code'        => 'BQL01',
            'name'        => 'Ban Quản lý',
            'status'      => 'active',
            'description' => 'Bộ phận điều hành chung cư',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['name' => 'Ban Quản lý']);
    }

    /**
     * Admin xem lịch làm việc.
     */
    public function test_admin_can_view_schedules(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654323']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.schedules.index'));
        $response->assertStatus(200);
    }
}
