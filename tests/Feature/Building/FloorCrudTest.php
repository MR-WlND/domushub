<?php

namespace Tests\Feature\Building;

use App\Models\Block;
use App\Models\Floor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class FloorCrudTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem form tạo tầng mới.
     */
    public function test_admin_can_view_create_floor_form(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.floors.create'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo tầng mới.
     */
    public function test_admin_can_create_floor(): void
    {
        $admin = $this->makeAdmin();
        $block = Block::create(['name' => 'Tòa Tầng Test']);

        $this->actingAs($admin);

        $response = $this->post(route('admin.floors.store'), [
            'block_id'    => $block->id,
            'name'        => 'Tầng 10',
            'floor_type'  => 'above_ground',
            'status'      => 'active',
            'description' => 'Mô tả tầng 10',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('floors', [
            'block_id' => $block->id,
            'name'     => 'Tầng 10',
        ]);
    }
}
