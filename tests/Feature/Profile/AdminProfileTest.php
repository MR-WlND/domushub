<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin đổi mật khẩu cá nhân thành công.
     */
    public function test_admin_can_change_password(): void
    {
        $admin = $this->makeAdmin(['password' => bcrypt('oldpassword')]);
        $this->actingAs($admin);

        $response = $this->put(route('admin.profile.change-password'), [
            'current_password'      => 'oldpassword',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
    }
}
