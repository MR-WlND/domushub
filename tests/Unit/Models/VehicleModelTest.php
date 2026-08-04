<?php

namespace Tests\Unit\Models;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class VehicleModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Helper methods isPending() và isActive() của Vehicle.
     */
    public function test_vehicle_status_helper_methods(): void
    {
        $apartment = $this->makeApartment();

        $pendingVehicle = Vehicle::create([
            'apartment_id'  => $apartment->id,
            'license_plate' => '51X12345',
            'vehicle_type'  => 'motorbike',
            'status'        => 'pending',
        ]);

        $activeVehicle = Vehicle::create([
            'apartment_id'  => $apartment->id,
            'license_plate' => '51X67890',
            'vehicle_type'  => 'car',
            'status'        => 'active',
        ]);

        $this->assertTrue($pendingVehicle->isPending());
        $this->assertFalse($activeVehicle->isPending());
    }
}
