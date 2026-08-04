<?php

namespace Tests\Feature\Facility;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class FacilityBookingTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách tiện ích (amenities).
     */
    public function test_admin_can_view_amenities_list(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.amenities.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin xem form tạo tiện ích mới.
     */
    public function test_admin_can_view_create_amenity_form(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654322']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.amenities.create'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo tiện ích mới thành công.
     */
    public function test_admin_can_create_facility(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654323']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.amenities.store'), [
            'name'          => 'Phòng Gym Tầng 2',
            'facility_type' => 'gym',
            'capacity'      => 20,
            'open_time'     => '06:00',
            'close_time'    => '22:00',
            'status'        => 'available',
            'slot_duration' => 60,
            'booking_type'  => 'slot',
            'fee_type'      => 'free',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('facilities', [
            'name' => 'Phòng Gym Tầng 2',
        ]);
    }

    /**
     * Resident xem danh sách tiện ích và đặt chỗ.
     */
    public function test_resident_can_view_facility_bookings_page(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $this->actingAs($resident);

        $response = $this->get(route('resident.facility-bookings.index'));
        $response->assertStatus(200);
    }
}
