<?php

namespace Tests\Feature\Community;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class PostCrudTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin toggle chuyển trạng thái bài đăng cư dân.
     */
    public function test_admin_can_toggle_post_status(): void
    {
        $admin     = $this->makeAdmin();
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

        $post = Post::create([
            'user_id' => $resident->id,
            'title'   => 'Bài đăng test',
            'content' => 'Nội dung bài đăng test',
            'status'  => 'published',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.posts.toggle-status', $post->id));
        $response->assertRedirect();
    }

    /**
     * Admin lấy thông tin JSON bài đăng.
     */
    public function test_admin_can_get_post_json(): void
    {
        $admin     = $this->makeAdmin();
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

        $post = Post::create([
            'user_id' => $resident->id,
            'title'   => 'Bài đăng JSON',
            'content' => 'Nội dung JSON',
            'status'  => 'published',
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.posts.json', $post->id));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Admin xóa bài đăng vi phạm.
     */
    public function test_admin_can_delete_post(): void
    {
        $admin     = $this->makeAdmin();
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

        $post = Post::create([
            'user_id' => $resident->id,
            'title'   => 'Bài vi phạm',
            'content' => 'Bài đăng test vi phạm quy định',
            'status'  => 'published',
        ]);

        $this->actingAs($admin);

        $response = $this->delete(route('admin.posts.destroy', $post->id));
        $response->assertRedirect();
    }
}
