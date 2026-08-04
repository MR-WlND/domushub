<?php

namespace Tests\Feature\Utility;

use App\Models\ServicePrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ServicePriceTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách bảng giá dịch vụ.
     */
    public function test_admin_can_view_service_prices(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.service-prices.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo bảng giá dịch vụ mới.
     */
    public function test_admin_can_create_service_price(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('admin.service-prices.store'), [
            'type'        => 'water',
            'name'        => 'Tiền nước sinh hoạt',
            'unit_price'  => 12000,
            'unit'        => 'm3',
            'description' => 'Áp dụng cho cư dân',
            'status'      => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_prices', [
            'name'       => 'Tiền nước sinh hoạt',
            'unit_price' => 12000,
        ]);
    }
}
