<?php

namespace Tests\Feature\Vehicle;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class VehicleRegistrationTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách xe.
     */
    public function test_admin_can_view_vehicles_list(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.vehicles.index'));
        $response->assertStatus(200);
    }

    /**
     * Resident xem trang danh sách xe của mình.
     */
    public function test_resident_can_view_vehicle_registration_page(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $this->actingAs($resident);

        $response = $this->get(route('resident.vehicles.index'));
        $response->assertStatus(200);
    }

    /**
     * Resident xem trang form đăng ký xe.
     */
    public function test_resident_can_view_create_vehicle_form(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $this->actingAs($resident);

        $response = $this->get(route('resident.vehicles.create'));
        $response->assertStatus(200);
    }

    /**
     * Resident đăng ký xe mới thành công.
     */
    public function test_resident_can_register_vehicle(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $this->actingAs($resident);

        $response = $this->post(route('resident.vehicles.store'), [
            'license_plate' => '51A12345',
            'vehicle_type'  => 'motorbike',
            'brand'         => 'Honda',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vehicles', [
            'license_plate' => '51A12345',
            'apartment_id'  => $apartment->id,
        ]);
    }

    /**
     * Validate: motorbike phải có biển số.
     */
    public function test_cannot_register_motorbike_without_license_plate(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $this->actingAs($resident);

        $response = $this->post(route('resident.vehicles.store'), [
            'license_plate' => '',
            'vehicle_type'  => 'motorbike',
        ]);

        $response->assertSessionHasErrors(['license_plate']);
    }

    /**
     * Resident xóa/hủy đăng ký xe.
     */
    public function test_resident_can_delete_vehicle(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

        $vehicle = Vehicle::create([
            'apartment_id'  => $apartment->id,
            'license_plate' => '51B99999',
            'vehicle_type'  => 'motorbike',
            'brand'         => 'Yamaha',
            'status'        => 'pending',
        ]);

        $this->actingAs($resident);

        $response = $this->delete(route('resident.vehicles.destroy', $vehicle));
        $response->assertRedirect();

        $this->assertEquals('inactive', $vehicle->fresh()->status);
    }
}
