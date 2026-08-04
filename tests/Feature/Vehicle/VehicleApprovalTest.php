<?php

namespace Tests\Feature\Vehicle;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class VehicleApprovalTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin khóa xe đã đăng ký.
     */
    public function test_admin_can_lock_vehicle(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654321']);
        $apartment = $this->makeApartment();

        $vehicle = Vehicle::create([
            'apartment_id'  => $apartment->id,
            'license_plate' => '51D22222',
            'vehicle_type'  => 'motorbike',
            'brand'         => 'Honda',
            'status'        => 'active',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.vehicles.lock', $vehicle));
        $response->assertRedirect();
        $this->assertEquals('locked', $vehicle->fresh()->status);
    }

    /**
     * Admin mở khóa xe.
     */
    public function test_admin_can_unlock_vehicle(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654322']);
        $apartment = $this->makeApartment();

        $vehicle = Vehicle::create([
            'apartment_id'  => $apartment->id,
            'license_plate' => '51E33333',
            'vehicle_type'  => 'car',
            'brand'         => 'VinFast',
            'status'        => 'locked',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.vehicles.unlock', $vehicle));
        $response->assertRedirect();
        $this->assertEquals('active', $vehicle->fresh()->status);
    }

    /**
     * Admin xóa xe khỏi hệ thống.
     */
    public function test_admin_can_delete_vehicle(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654323']);
        $apartment = $this->makeApartment();

        $vehicle = Vehicle::create([
            'apartment_id'  => $apartment->id,
            'license_plate' => '51F44444',
            'vehicle_type'  => 'motorbike',
            'brand'         => 'Yamaha',
            'status'        => 'pending',
        ]);

        $this->actingAs($admin);

        $response = $this->delete(route('admin.vehicles.destroy', $vehicle));
        $response->assertRedirect();

        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }
}
