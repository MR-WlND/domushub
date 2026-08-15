<?php

namespace Tests\Feature\Apartment;

use App\Models\Apartment;
use App\Models\ApartmentType;
use App\Models\Block;
use App\Models\Floor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ApartmentCrudTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách căn hộ trả về 200.
     */
    public function test_admin_can_view_apartment_list(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.apartments.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin xem trang tạo căn hộ mới trả về 200.
     */
    public function test_admin_can_view_create_apartment_form(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654322']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.apartments.create'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo căn hộ mới thành công.
     */
    public function test_admin_can_create_apartment(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654323']);
        $block = Block::create(['name' => 'Tòa A']);
        $floor = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 1', 'floor_number' => 1]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.apartments.store'), [
            'floor_id'         => $floor->id,
            'apartment_number' => 'A101',
            'status'           => 'vacant',
            'area'             => 65.5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('apartments', [
            'floor_id'         => $floor->id,
            'apartment_number' => 'A101',
        ]);
    }

    /**
     * Admin xem chi tiết căn hộ.
     */
    public function test_admin_can_view_apartment_detail(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654324']);
        $apartment = $this->makeApartment(['apartment_number' => 'B202']);

        $this->actingAs($admin);

        $response = $this->get(route('admin.apartments.show', $apartment));
        $response->assertStatus(200);
        $response->assertSee('B202');
    }

    /**
     * Admin cập nhật căn hộ thành công.
     */
    public function test_admin_can_update_apartment(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654325']);
        $apartment = $this->makeApartment(['apartment_number' => 'C303']);

        $this->actingAs($admin);

        $response = $this->put(route('admin.apartments.update', $apartment), [
            'floor_id'         => $apartment->floor_id,
            'apartment_number' => 'C303',
            'status'           => 'maintenance',
            'area'             => 80,
            'description'      => 'Đang bảo trì điện',
        ]);

        $response->assertRedirect();
        $this->assertEquals('maintenance', $apartment->fresh()->status);
    }

    /**
     * Admin xóa căn hộ (soft delete).
     */
    public function test_admin_can_delete_apartment(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654326']);
        $apartment = $this->makeApartment(['apartment_number' => 'D404']);

        $this->actingAs($admin);

        $response = $this->delete(route('admin.apartments.destroy', $apartment));
        $response->assertRedirect();

        $this->assertSoftDeleted('apartments', ['id' => $apartment->id]);
    }
}
