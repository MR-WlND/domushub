<?php

namespace Tests\Feature\Building;

use App\Models\Block;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class BlockCrudTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách tòa nhà.
     */
    public function test_admin_can_view_blocks_list(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.blocks.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo tòa nhà mới.
     */
    public function test_admin_can_create_block(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('admin.blocks.store'), [
            'name' => 'Tòa C1 Test',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('blocks', ['name' => 'Tòa C1 Test']);
    }

    /**
     * Admin xem chi tiết tòa nhà.
     */
    public function test_admin_can_view_block_detail(): void
    {
        $admin = $this->makeAdmin();
        $block = Block::create(['name' => 'Tòa D1 Detail']);

        $this->actingAs($admin);

        $response = $this->get(route('admin.blocks.show', $block->id));
        $response->assertStatus(200);
    }
}
