<?php

namespace Tests\Feature\Apartment;

use App\Models\ApartmentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ApartmentTypeTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    private function makeApartmentType(array $overrides = []): ApartmentType
    {
        return ApartmentType::create(array_merge([
            'name'             => 'Loại mẫu',
            'description'      => 'Mô tả loại căn hộ mẫu',
            'base_service_fee' => 500000,
            'bedroom_count'    => 2,
            'bathroom_count'   => 1,
        ], $overrides));
    }

    /**
     * Admin xem danh sách loại căn hộ.
     */
    public function test_admin_can_view_apartment_types(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->makeApartmentType(['name' => 'Studio']);

        $this->actingAs($admin);

        $response = $this->get(route('admin.apartment-types.index'));
        $response->assertStatus(200);
        $response->assertSee('Studio');
    }

    /**
     * Admin tạo loại căn hộ mới thành công.
     */
    public function test_admin_can_create_apartment_type(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654322']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.apartment-types.store'), [
            'name'             => '2 phòng ngủ',
            'description'      => 'Căn hộ 2 phòng ngủ tiêu chuẩn',
            'base_service_fee' => 600000,
            'bedroom_count'    => 2,
            'bathroom_count'   => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('apartment_types', ['name' => '2 phòng ngủ']);
    }

    /**
     * Validate: không thể tạo loại căn hộ không có tên.
     */
    public function test_cannot_create_apartment_type_without_name(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654323']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.apartment-types.store'), [
            'name'             => '',
            'base_service_fee' => 500000,
            'bedroom_count'    => 1,
            'bathroom_count'   => 1,
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Admin cập nhật loại căn hộ.
     */
    public function test_admin_can_update_apartment_type(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654324']);
        $type  = $this->makeApartmentType(['name' => 'Loại A']);

        $this->actingAs($admin);

        $response = $this->put(route('admin.apartment-types.update', $type), [
            'name'             => 'Loại A Premium',
            'description'      => 'Mô tả mới',
            'base_service_fee' => 700000,
            'bedroom_count'    => 3,
            'bathroom_count'   => 2,
        ]);

        $response->assertRedirect();
        $this->assertEquals('Loại A Premium', $type->fresh()->name);
    }

    /**
     * Admin xóa loại căn hộ không có căn hộ gắn.
     */
    public function test_admin_can_delete_apartment_type(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654325']);
        $type  = $this->makeApartmentType(['name' => 'Loại B Xóa Test']);

        $this->actingAs($admin);

        // Đảm bảo không có apartment gắn với loại này
        $this->assertDatabaseHas('apartment_types', ['id' => $type->id]);
        $this->assertEquals(0, $type->apartments()->count());

        $response = $this->delete(route('admin.apartment-types.destroy', $type));
        $response->assertRedirect();

        // Sau khi xóa, record không còn trong DB
        $this->assertNull(\App\Models\ApartmentType::find($type->id));
    }
}
