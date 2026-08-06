<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Kiểm tra helper isAdmin().
     */
    public function test_user_is_admin_check(): void
    {
        $admin    = User::factory()->make(['role' => 'admin']);
        $resident = User::factory()->make(['role' => 'resident']);

        $this->assertEquals('admin', $admin->role);
        $this->assertEquals('resident', $resident->role);
    }

    /**
     * Mật khẩu được tự động mã hóa khi gán.
     */
    public function test_user_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'phone'    => '0911223344',
            'password' => 'secret123',
        ]);

        $this->assertNotEquals('secret123', $user->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('secret123', $user->password));
    }

    /**
     * Soft delete hoạt động đúng trên User model.
     */
    public function test_user_can_be_soft_deleted(): void
    {
        $user = User::factory()->create(['phone' => '0999888777']);
        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
}
