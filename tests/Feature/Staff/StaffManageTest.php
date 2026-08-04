<?php

namespace Tests\Feature\Staff;

use App\Models\Department;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class StaffManageTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách nhân sự.
     */
    public function test_admin_can_view_staff_list(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.staffs.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin xem trang tạo nhân sự mới.
     */
    public function test_admin_can_view_create_staff_form(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654322']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.staffs.create'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo nhân sự mới thành công.
     */
    public function test_admin_can_create_staff(): void
    {
        $admin      = $this->makeAdmin(['phone' => '0987654323']);
        $department = Department::create(['name' => 'Phòng Kỹ thuật']);

        $this->actingAs($admin);

        $response = $this->post(route('admin.staffs.store'), [
            'full_name'     => 'Trần Văn Kỹ Thuật',
            'phone'         => '0911223344',
            'cccd'          => '012345678901',
            'address'       => 'Hà Nội',
            'department_id' => $department->id,
            'status'        => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('staffs', [
            'full_name' => 'Trần Văn Kỹ Thuật',
            'phone'     => '0911223344',
        ]);
    }

    /**
     * Admin cập nhật thông tin nhân sự.
     */
    public function test_admin_can_update_staff(): void
    {
        $admin      = $this->makeAdmin(['phone' => '0987654324']);
        $department = Department::create(['name' => 'Phòng Vệ sinh']);

        $staff = Staff::create([
            'full_name'     => 'Nguyễn Văn A',
            'phone'         => '0922334455',
            'department_id' => $department->id,
            'status'        => 'active',
        ]);

        $this->actingAs($admin);

        $response = $this->put(route('admin.staffs.update', $staff), [
            'full_name'     => 'Nguyễn Văn A (Đã cập nhật)',
            'phone'         => '0922334455',
            'department_id' => $department->id,
            'status'        => 'active',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Nguyễn Văn A (Đã cập nhật)', $staff->fresh()->full_name);
    }

    /**
     * Admin xóa nhân sự.
     */
    public function test_admin_can_delete_staff(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654325']);
        $staff = Staff::create([
            'full_name' => 'Nhân viên xóa test',
            'phone'     => '0933445566',
            'status'    => 'active',
        ]);

        $this->actingAs($admin);

        $response = $this->delete(route('admin.staffs.destroy', $staff));
        $response->assertRedirect();

        $this->assertDatabaseMissing('staffs', ['id' => $staff->id]);
    }
}
