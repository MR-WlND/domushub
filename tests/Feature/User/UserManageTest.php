<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class UserManageTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách tài khoản người dùng.
     */
    public function test_admin_can_view_users_list(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo tài khoản nhân viên mới.
     */
    public function test_admin_can_create_user(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('admin.users.store'), [
            'name'   => 'Nhân viên mới',
            'email'  => 'newstaff@test.com',
            'phone'  => '0912999888',
            'role'   => 'staff',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'newstaff@test.com',
            'role'  => 'staff',
        ]);
    }

    /**
     * Admin cập nhật trạng thái / phân quyền tài khoản người dùng.
     */
    public function test_admin_can_update_user_status(): void
    {
        $admin = $this->makeAdmin();
        $targetUser = User::factory()->create([
            'phone'  => '0988776655',
            'role'   => 'staff',
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        $response = $this->put(route('admin.users.updateStatus', $targetUser->id), [
            'role'   => 'technician',
            'status' => 'banned',
        ]);

        $response->assertRedirect();
        $this->assertEquals('technician', $targetUser->fresh()->role);
        $this->assertEquals('banned', $targetUser->fresh()->status);
    }

    /**
     * Admin reset mật khẩu mặc định cho người dùng.
     */
    public function test_admin_can_reset_user_password(): void
    {
        $admin = $this->makeAdmin();
        $targetUser = User::factory()->create([
            'phone'  => '0977665544',
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        $response = $this->put(route('admin.users.resetPassword', $targetUser->id));
        $response->assertRedirect();
    }
}
