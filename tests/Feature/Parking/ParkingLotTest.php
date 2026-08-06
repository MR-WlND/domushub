<?php

namespace Tests\Feature\Parking;

use App\Models\ParkingLot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ParkingLotTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách lốt đỗ xe.
     */
    public function test_admin_can_view_parking_lots(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.parking-lots.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo lốt đỗ xe mới.
     */
    public function test_admin_can_create_parking_lot(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('admin.parking-lots.store'), [
            'creation_mode' => 'single',
            'lot_type'      => 'car',
            'lot_number'    => 'A-01',
            'zone'          => 'Zone A',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('parking_lots', ['lot_number' => 'A-01']);
    }
}
