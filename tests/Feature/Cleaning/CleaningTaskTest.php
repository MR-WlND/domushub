<?php

namespace Tests\Feature\Cleaning;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class CleaningTaskTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Nhân viên vệ sinh xem dashboard vệ sinh.
     */
    public function test_cleaner_can_view_cleaning_dashboard(): void
    {
        $cleaner = User::factory()->create([
            'role'   => 'cleaning',
            'phone'  => '0977889900',
            'status' => 'active',
        ]);

        $this->actingAs($cleaner);

        $response = $this->get(route('cleaning.dashboard'));
        $response->assertStatus(200);
    }

    /**
     * Nhân viên vệ sinh xem danh sách công việc.
     */
    public function test_cleaner_can_view_tasks_list(): void
    {
        $cleaner = User::factory()->create([
            'role'   => 'cleaning',
            'phone'  => '0977889901',
            'status' => 'active',
        ]);

        $this->actingAs($cleaner);

        $response = $this->get(route('cleaning.tasks'));
        $response->assertStatus(200);
    }

    /**
     * Nhân viên vệ sinh xem trang cá nhân.
     */
    public function test_cleaner_can_view_profile(): void
    {
        $cleaner = User::factory()->create([
            'role'   => 'cleaning',
            'phone'  => '0977889902',
            'status' => 'active',
        ]);

        $this->actingAs($cleaner);

        $response = $this->get(route('cleaning.profile'));
        $response->assertStatus(200);
    }
}
