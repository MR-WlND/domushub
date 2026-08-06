<?php

namespace Tests\Feature\Announcement;

use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class AnnouncementCrudTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách thông báo.
     */
    public function test_admin_can_view_announcement_list(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.announcements.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin xem form tạo thông báo mới.
     */
    public function test_admin_can_view_create_announcement_form(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654322']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.announcements.create'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo thông báo mới thành công.
     */
    public function test_admin_can_create_announcement(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654323']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.announcements.store'), [
            'title'    => 'Thông báo cắt nước tạm thời',
            'content'  => 'Tòa A sẽ bị cắt nước từ 8h đến 12h ngày mai để bảo trì.',
            'category' => 'maintenance',
            'status'   => 'published',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('announcements', [
            'title'    => 'Thông báo cắt nước tạm thời',
            'category' => 'maintenance',
        ]);
    }

    /**
     * Validate: tiêu đề thông báo là bắt buộc.
     */
    public function test_cannot_create_announcement_without_title(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654324']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.announcements.store'), [
            'title'   => '',
            'content' => 'Nội dung thông báo',
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    /**
     * Admin cập nhật thông báo.
     */
    public function test_admin_can_update_announcement(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654325']);
        $announcement = Announcement::create([
            'user_id'  => $admin->id,
            'title'    => 'Thông báo cũ',
            'content'  => 'Nội dung cũ',
            'category' => 'general',
            'status'   => 'draft',
        ]);

        $this->actingAs($admin);

        $response = $this->put(route('admin.announcements.update', $announcement), [
            'title'    => 'Thông báo đã cập nhật',
            'content'  => 'Nội dung mới đã sửa',
            'category' => 'event',
            'status'   => 'published',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Thông báo đã cập nhật', $announcement->fresh()->title);
    }

    /**
     * Admin xóa thông báo.
     */
    public function test_admin_can_delete_announcement(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654326']);
        $announcement = Announcement::create([
            'user_id'  => $admin->id,
            'title'    => 'Thông báo sẽ bị xóa',
            'content'  => 'Nội dung',
            'category' => 'general',
            'status'   => 'draft',
        ]);

        $this->actingAs($admin);

        $response = $this->delete(route('admin.announcements.destroy', $announcement));
        $response->assertRedirect();

        $this->assertSoftDeleted('announcements', ['id' => $announcement->id]);
    }
}
