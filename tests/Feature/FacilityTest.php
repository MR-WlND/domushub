<?php
 
 namespace Tests\Feature;
 
 use App\Models\Facility;
 use App\Models\FacilityBooking;
 use App\Models\User;
 use Illuminate\Foundation\Testing\RefreshDatabase;
 use Illuminate\Http\UploadedFile;
 use Illuminate\Support\Facades\Storage;
 use Tests\TestCase;
 
 class FacilityTest extends TestCase
 {
     use RefreshDatabase;
 
     /**
      * Test that resident can view facility list and details
      */
     public function test_resident_can_view_facilities(): void
     {
         $resident = User::factory()->create([
             'role'  => 'resident',
             'phone' => '0912345678',
         ]);
         $facility = Facility::create([
             'name'           => 'Hồ bơi',
             'capacity'       => 30,
             'description'    => 'Hồ bơi ngoài trời tầng 5',
             'status'         => 'available',
             'open_time'      => '06:00',
             'close_time'     => '20:00',
             'slot_duration'  => 60,
             'price_per_slot' => 20000,
             'rules'          => 'Cần mặc đồ bơi',
         ]);
 
         $this->actingAs($resident);
 
         // List
         $response = $this->get(route('resident.facilities.index'));
         $response->assertStatus(200);
         $response->assertSee('Hồ bơi');
         $response->assertSee('Đang mở');
 
         // Show
         $response = $this->get(route('resident.facilities.show', $facility));
         $response->assertStatus(200);
         $response->assertSee('Hồ bơi');
         $response->assertSee('Khung giờ có thể đặt');
     }
 
     /**
      * Test that admin can view statistics
      */
     public function test_admin_can_view_statistics(): void
     {
         $admin = User::factory()->create([
             'role'  => 'admin',
             'phone' => '0987654321',
         ]);
         $facility = Facility::create([
             'name'           => 'Sân BBQ',
             'capacity'       => 15,
             'status'         => 'available',
             'price_per_slot' => 50000,
         ]);
 
         // Add some bookings
         FacilityBooking::create([
             'facility_id'  => $facility->id,
             'user_id'      => $admin->id,
             'booking_date' => now()->toDateString(),
             'start_time'   => '18:00',
             'end_time'     => '20:00',
             'status'       => 'approved',
         ]);
 
         $this->actingAs($admin);
 
         $response = $this->get(route('admin.amenities.statistics'));
         $response->assertStatus(200);
         $response->assertSee('Báo cáo thống kê');
         $response->assertSee('Sân BBQ');
     }
 
     /**
      * Test quick status update
      */
     public function test_admin_can_update_status_quickly(): void
     {
         $admin = User::factory()->create([
             'role'  => 'admin',
             'phone' => '0987654322',
         ]);
         $facility = Facility::create([
             'name'     => 'Phòng Gym',
             'capacity' => 20,
             'status'   => 'available',
         ]);
 
         $this->actingAs($admin);
 
         $response = $this->patch(route('admin.amenities.status', $facility), [
             'status' => 'maintenance',
         ]);
 
         $response->assertStatus(302);
         $this->assertEquals('maintenance', $facility->fresh()->status);
     }
 
     /**
      * Test image upload and delete flow
      */
     public function test_admin_can_upload_and_delete_images(): void
     {
         Storage::fake('public');
         $admin = User::factory()->create([
             'role'  => 'admin',
             'phone' => '0987654323',
         ]);
         $facility = Facility::create([
             'name'     => 'Phòng Gym',
             'capacity' => 20,
             'status'   => 'available',
         ]);
 
         $this->actingAs($admin);
 
         // Test upload
         $file1 = UploadedFile::fake()->image('gym1.jpg');
         $file2 = UploadedFile::fake()->image('gym2.png');
 
         $response = $this->post(route('admin.amenities.images.store', $facility), [
             'images' => [$file1, $file2],
         ]);
 
         $response->assertStatus(302);
         $facility = $facility->fresh();
         $this->assertCount(2, $facility->images);
 
         // Check file exists in disk
         Storage::disk('public')->assertExists($facility->images[0]);
         Storage::disk('public')->assertExists($facility->images[1]);
 
         // Test delete index 0
         $firstPath = $facility->images[0];
         $response = $this->delete(route('admin.amenities.images.destroy', [$facility, 0]));
         $response->assertStatus(302);
 
         $facility = $facility->fresh();
         $this->assertCount(1, $facility->images);
         Storage::disk('public')->assertMissing($firstPath);
     }
 
     /**
      * Test capacity booking validation
      */
     public function test_resident_cannot_exceed_capacity(): void
     {
         $resident1 = User::factory()->create(['role' => 'resident', 'phone' => '0912345678']);
         $resident2 = User::factory()->create(['role' => 'resident', 'phone' => '0912345679']);
         $facility = Facility::create([
             'name'           => 'BBQ Yard',
             'capacity'       => 10,
             'status'         => 'available',
             'open_time'      => '08:00',
             'close_time'     => '20:00',
             'slot_duration'  => 60,
             'price_per_slot' => 10000,
         ]);
 
         $this->actingAs($resident1);
         $response = $this->post(route('resident.facilities.book.store', $facility), [
             'booking_date'     => now()->addDay()->toDateString(),
             'start_time'       => '10:00',
             'end_time'         => '11:00',
             'number_of_people' => 6,
         ]);
         $response->assertStatus(302);
         $this->assertDatabaseHas('facility_bookings', [
             'facility_id'      => $facility->id,
             'user_id'          => $resident1->id,
             'number_of_people' => 6,
             'status'           => 'approved',
         ]);
 
         // Second resident booking 5 people (total 11 > 10)
         $this->actingAs($resident2);
         $response = $this->post(route('resident.facilities.book.store', $facility), [
             'booking_date'     => now()->addDay()->toDateString(),
             'start_time'       => '10:00',
             'end_time'         => '11:00',
             'number_of_people' => 5,
         ]);
         $response->assertStatus(302);
         $response->assertSessionHas('error');
         // Total should still be 6 in the database
         $this->assertEquals(6, FacilityBooking::where('facility_id', $facility->id)->sum('number_of_people'));
 
         // Book 4 people (total 10 <= 10) -> successful
         $response = $this->post(route('resident.facilities.book.store', $facility), [
             'booking_date'     => now()->addDay()->toDateString(),
             'start_time'       => '10:00',
             'end_time'         => '11:00',
             'number_of_people' => 4,
         ]);
         $response->assertStatus(302);
         $this->assertEquals(10, FacilityBooking::where('facility_id', $facility->id)->sum('number_of_people'));
     }
 
     /**
      * Test maintenance checking
      */
     public function test_cannot_book_maintenance_facility(): void
     {
         $resident = User::factory()->create(['role' => 'resident', 'phone' => '0912345678']);
         $facility = Facility::create([
             'name'           => 'Sauna room',
             'capacity'       => 5,
             'status'         => 'maintenance',
             'open_time'      => '08:00',
             'close_time'     => '20:00',
             'slot_duration'  => 60,
         ]);
 
         $this->actingAs($resident);
         $response = $this->post(route('resident.facilities.book.store', $facility), [
             'booking_date'     => now()->addDay()->toDateString(),
             'start_time'       => '10:00',
             'end_time'         => '11:00',
             'number_of_people' => 2,
         ]);
         $response->assertStatus(302);
         $response->assertSessionHas('error');
         $this->assertDatabaseMissing('facility_bookings', [
             'facility_id' => $facility->id,
         ]);
     }

     /**
      * Test booking payment flow and QR code visibility rules
      */
     public function test_booking_payment_and_qr_rules(): void
     {
         $resident = User::factory()->create(['role' => 'resident', 'phone' => '0912345678']);
         $facility = Facility::create([
             'name'           => 'Paid Pool',
             'capacity'       => 10,
             'status'         => 'available',
             'open_time'      => '08:00',
             'close_time'     => '20:00',
             'slot_duration'  => 60,
             'price_per_slot' => 20000,
         ]);

         $this->actingAs($resident);

         // Book a slot (requires payment of 20000)
         $response = $this->post(route('resident.facilities.book.store', $facility), [
             'booking_date'     => now()->addDay()->toDateString(),
             'start_time'       => '10:00',
             'end_time'         => '11:00',
             'number_of_people' => 1,
         ]);

         $booking = FacilityBooking::where('facility_id', $facility->id)->first();
         $this->assertNotNull($booking);

         // 1. Should redirect to booking history with pay_booking_id parameter (to show payment modal)
         $response->assertRedirect(route('resident.facility-bookings.index', ['pay_booking_id' => $booking->id]));

         // 2. Accessing QR page while unpaid should not display QR code image
         $response = $this->get(route('resident.facility-bookings.qr', $booking));
         $response->assertStatus(200);
         $response->assertDontSee('https://api.qrserver.com/v1/create-qr-code');
         $response->assertSee('Yêu cầu thanh toán');

         // 3. Attempting to pay with Momo should fail validation
         $response = $this->post(route('resident.facility-bookings.pay', $booking), [
             'payment_method' => 'momo',
         ]);
         $response->assertStatus(302);
         $response->assertSessionHasErrors(['payment_method']);

         // 4. Paying with Cash should keep it unpaid and redirect to history index
         $response = $this->post(route('resident.facility-bookings.pay', $booking), [
             'payment_method' => 'cash',
         ]);
         $response->assertRedirect(route('resident.facility-bookings.index'));
         $this->assertEquals('unpaid', $booking->fresh()->payment_status);

         // 5. Paying with VNPay should succeed, mark as paid, and redirect directly to QR page
         $response = $this->post(route('resident.facility-bookings.pay', $booking), [
             'payment_method' => 'vnpay',
         ]);
         $response->assertRedirect(route('resident.facility-bookings.qr', $booking));
         $this->assertEquals('paid', $booking->fresh()->payment_status);

         // Now the QR page should show the QR code image
         $response = $this->get(route('resident.facility-bookings.qr', $booking));
         $response->assertStatus(200);
         $response->assertSee('https://api.qrserver.com/v1/create-qr-code');
         $response->assertDontSee('Yêu cầu thanh toán');
     }

     /**
      * Test facility with all day duration (slot_duration = 0)
      */
     public function test_facility_with_all_day_duration(): void
     {
         $resident = User::factory()->create([
             'role'  => 'resident',
             'phone' => '0912345679',
         ]);

         $facility = Facility::create([
             'name'           => 'Hồ bơi trung tâm',
             'capacity'       => 50,
             'description'    => 'Hồ bơi rộng lớn',
             'status'         => 'available',
             'open_time'      => '06:00',
             'close_time'     => '20:00',
             'slot_duration'  => 0,
             'price_per_slot' => 20000,
             'rules'          => 'No glass.',
         ]);

         // Assert that getTimeSlots returns exactly 1 slot covering the whole day
         $slots = $facility->getTimeSlots();
         $this->assertCount(1, $slots);
         $this->assertEquals('06:00', $slots[0]['start']);
         $this->assertEquals('20:00', $slots[0]['end']);

         // Create booking
         $booking = FacilityBooking::create([
             'facility_id' => $facility->id,
             'user_id' => $resident->id,
             'booking_date' => now()->addDay()->toDateString(),
             'start_time' => '06:00',
             'end_time' => '20:00',
             'number_of_people' => 2,
             'status' => 'approved',
         ]);

         // Assert amount calculated is for 1 slot only (2 * 20000 * 1 = 40000)
         $this->assertEquals(40000, $booking->fresh()->amount);
     }
 }
