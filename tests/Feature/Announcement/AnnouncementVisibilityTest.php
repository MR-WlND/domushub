<?php

namespace Tests\Feature\Announcement;

use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class AnnouncementVisibilityTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin ghim (pin) thông báo.
     */
    public function test_admin_can_toggle_pin_announcement(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $announcement = Announcement::create([
            'user_id'  => $admin->id,
            'title'    => 'Thông báo quan trọng',
            'content'  => 'Nội dung thông báo quan trọng',
            'category' => 'general',
            'status'   => 'published',
            'pinned'   => false,
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.announcements.toggle-pin', $announcement->id));
        $response->assertRedirect();
        $this->assertTrue((bool) $announcement->fresh()->pinned);
    }

    /**
     * Resident xem danh sách thông báo đã phát hành.
     */
    public function test_resident_can_view_published_announcements(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $admin     = $this->makeAdmin(['phone' => '0987654322']);

        Announcement::create([
            'user_id'  => $admin->id,
            'title'    => 'Thông báo cho cư dân',
            'content'  => 'Nội dung thông báo công khai',
            'category' => 'general',
            'status'   => 'published',
        ]);

        $this->actingAs($resident);

        $response = $this->get(route('resident.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Thông báo cho cư dân');
    }
}
