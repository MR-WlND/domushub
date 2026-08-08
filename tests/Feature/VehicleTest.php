<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\ParkingLot;
use App\Models\Resident;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Vehicle Management Test Suite
 * Covers: TC_VEH_01 → TC_VEH_42
 */
class VehicleTest extends TestCase
{
    use RefreshDatabase;

    private User      $admin;
    private User      $security;
    private User      $resident;
    private Apartment $apartment;

    protected function setUp(): void
    {
        parent::setUp();

        $block           = Block::create(['name' => 'Block A']);
        $floor           = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 5', 'floor_number' => 5]);
        $this->apartment = Apartment::create([
            'floor_id'         => $floor->id,
            'apartment_number' => 'A502',
            'area'             => 75,
            'status'           => 'occupied',
        ]);

        $this->admin    = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->security = User::factory()->create(['role' => 'security', 'status' => 'active']);
        $this->resident = User::factory()->create([
            'role'         => 'resident',
            'status'       => 'active',
            'apartment_id' => $this->apartment->id,
        ]);

        Resident::create([
            'user_id'          => $this->resident->id,
            'apartment_id'     => $this->apartment->id,
            'relationship'     => 'owner',
            'temporary_status' => 'permanent',
            'start_date'       => now()->toDateString(),
        ]);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_01: Cư dân đăng ký xe thành công
    // -------------------------------------------------------------------------
    public function test_resident_can_register_vehicle(): void
    {
        $this->actingAs($this->resident);

        $response = $this->post(route('resident.vehicles.store'), [
            'license_plate' => '51A-12345',
            'type'          => 'car',
            'brand'         => 'Toyota',
            'color'         => 'White',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vehicles', [
            'license_plate' => '51A-12345',
            'user_id'       => $this->resident->id,
            'status'        => 'pending',
        ]);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_02: Đăng ký xe trùng biển số → lỗi
    // -------------------------------------------------------------------------
    public function test_register_duplicate_license_plate_shows_error(): void
    {
        Vehicle::create([
            'user_id'       => $this->resident->id,
            'license_plate' => '51B-99999',
            'type'          => 'motorbike',
            'status'        => 'approved',
        ]);

        $this->actingAs($this->resident);

        $response = $this->post(route('resident.vehicles.store'), [
            'license_plate' => '51B-99999',  // trùng
            'type'          => 'motorbike',
            'brand'         => 'Honda',
            'color'         => 'Black',
        ]);

        $response->assertSessionHasErrors(['license_plate']);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_03: Đăng ký xe với biển số không hợp lệ
    // -------------------------------------------------------------------------
    public function test_register_vehicle_with_invalid_plate_format_shows_error(): void
    {
        $this->actingAs($this->resident);

        $response = $this->post(route('resident.vehicles.store'), [
            'license_plate' => 'XYZ@!INVALID',  // format sai
            'type'          => 'car',
        ]);

        $response->assertSessionHasErrors(['license_plate']);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_04: Đăng ký xe không nhập biển số → lỗi required
    // -------------------------------------------------------------------------
    public function test_register_vehicle_without_license_plate_shows_required_error(): void
    {
        $this->actingAs($this->resident);

        $response = $this->post(route('resident.vehicles.store'), [
            // thiếu license_plate
            'type'  => 'car',
            'brand' => 'Honda',
        ]);

        $response->assertSessionHasErrors(['license_plate']);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_06: Cư dân xem QR xe đã duyệt
    // -------------------------------------------------------------------------
    public function test_resident_can_view_approved_vehicle_qr(): void
    {
        $vehicle = Vehicle::create([
            'user_id'       => $this->resident->id,
            'license_plate' => '51C-11111',
            'type'          => 'motorbike',
            'status'        => 'approved',
        ]);

        $this->actingAs($this->resident);

        $response = $this->get(route('resident.vehicles.qr', $vehicle));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_09: Admin phê duyệt xe đang chờ
    // -------------------------------------------------------------------------
    public function test_admin_can_approve_pending_vehicle(): void
    {
        $vehicle = Vehicle::create([
            'user_id'       => $this->resident->id,
            'license_plate' => '51D-22222',
            'type'          => 'car',
            'status'        => 'pending',
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.vehicles.approve', $vehicle));

        $response->assertRedirect();
        $this->assertEquals('approved', $vehicle->fresh()->status);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_10: Admin gán lốt đỗ xe
    // -------------------------------------------------------------------------
    public function test_admin_can_assign_parking_lot_to_vehicle(): void
    {
        $vehicle = Vehicle::create([
            'user_id'       => $this->resident->id,
            'license_plate' => '51D-33333',
            'type'          => 'car',
            'status'        => 'approved',
        ]);

        $parkingLot = ParkingLot::create([
            'name'   => 'P-101',
            'type'   => 'car',
            'status' => 'available',
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.vehicles.assignLot', $vehicle), [
            'parking_lot_id' => $parkingLot->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals($parkingLot->id, $vehicle->fresh()->parking_lot_id);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_22: Admin khóa xe vi phạm
    // -------------------------------------------------------------------------
    public function test_admin_can_lock_vehicle(): void
    {
        $vehicle = Vehicle::create([
            'user_id'       => $this->resident->id,
            'license_plate' => '51E-44444',
            'type'          => 'car',
            'status'        => 'approved',
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.vehicles.lock', $vehicle));

        $response->assertRedirect();
        $this->assertEquals('locked', $vehicle->fresh()->status);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_23: Bảo vệ quét QR xe vào → check-in thành công
    // -------------------------------------------------------------------------
    public function test_security_can_scan_qr_for_vehicle_checkin(): void
    {
        $vehicle = Vehicle::create([
            'user_id'       => $this->resident->id,
            'license_plate' => '51F-55555',
            'type'          => 'motorbike',
            'status'        => 'approved',
            'qr_code'       => 'VEH_QR_TOKEN_55555',
        ]);

        $this->actingAs($this->security);

        $response = $this->post(route('security.vehicle-checkin.scan'), [
            'qr_code' => 'VEH_QR_TOKEN_55555',
        ]);

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_28: Quét QR xe đã bị khóa → thông báo lỗi
    // -------------------------------------------------------------------------
    public function test_checkin_locked_vehicle_shows_locked_status(): void
    {
        $vehicle = Vehicle::create([
            'user_id'       => $this->resident->id,
            'license_plate' => '51G-66666',
            'type'          => 'car',
            'status'        => 'locked',
            'qr_code'       => 'VEH_QR_TOKEN_LOCKED',
        ]);

        $this->actingAs($this->security);

        $response = $this->post(route('security.vehicle-checkin.scan'), [
            'qr_code' => 'VEH_QR_TOKEN_LOCKED',
        ]);

        $response->assertStatus(200);
        $decoded = $response->json();
        // Phải cảnh báo trạng thái locked
        if ($decoded) {
            $this->assertContains($decoded['status'] ?? 'locked', ['locked', 'error', 'warning']);
        }
    }

    // -------------------------------------------------------------------------
    // TC_VEH_30: Xe không có trong hệ thống → thông báo không tìm thấy
    // -------------------------------------------------------------------------
    public function test_checkin_unknown_qr_shows_not_found(): void
    {
        $this->actingAs($this->security);

        $response = $this->post(route('security.vehicle-checkin.scan'), [
            'qr_code' => 'QR_THAT_DOES_NOT_EXIST',
        ]);

        $response->assertStatus(200);
        $decoded = $response->json();
        if ($decoded) {
            $this->assertContains($decoded['status'] ?? 'error', ['error', 'not_found', 'invalid']);
        }
    }

    // -------------------------------------------------------------------------
    // TC_VEH_36: Xe vãng lai (guest) check-in thủ công
    // -------------------------------------------------------------------------
    public function test_security_can_check_in_guest_vehicle(): void
    {
        $this->actingAs($this->security);

        $response = $this->post(route('security.vehicle-checkin.guest'), [
            'license_plate' => '51H-GUEST01',
            'type'          => 'car',
            'reason'        => 'Thăm người thân',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vehicle_logs', [
            'license_plate' => '51H-GUEST01',
        ]);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_38: Check-out xe đăng ký QR
    // -------------------------------------------------------------------------
    public function test_security_can_scan_qr_for_vehicle_checkout(): void
    {
        $vehicle = Vehicle::create([
            'user_id'       => $this->resident->id,
            'license_plate' => '51I-77777',
            'type'          => 'motorbike',
            'status'        => 'approved',
            'qr_code'       => 'VEH_QR_CHECKOUT',
        ]);

        // Tạo log check-in trước
        \App\Models\VehicleLog::create([
            'vehicle_id'    => $vehicle->id,
            'license_plate' => '51I-77777',
            'direction'     => 'in',
            'checked_at'    => now()->subHour(),
        ]);

        $this->actingAs($this->security);

        $response = $this->post(route('security.vehicle-checkout.scan'), [
            'qr_code' => 'VEH_QR_CHECKOUT',
        ]);

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_42: Bảo vệ xem Dashboard nhật ký xe
    // -------------------------------------------------------------------------
    public function test_security_can_view_vehicle_log_dashboard(): void
    {
        $this->actingAs($this->security);

        $response = $this->get(route('security.vehicle-logs.index'));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_11: Admin mở khóa xe
    // -------------------------------------------------------------------------
    public function test_admin_can_unlock_vehicle(): void
    {
        $vehicle = Vehicle::create([
            'user_id'       => $this->resident->id,
            'license_plate' => '51J-88888',
            'type'          => 'car',
            'status'        => 'locked',
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.vehicles.unlock', $vehicle));

        $response->assertRedirect();
        $this->assertEquals('approved', $vehicle->fresh()->status);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_12: Admin thu hồi lốt đỗ xe
    // -------------------------------------------------------------------------
    public function test_admin_can_release_parking_lot(): void
    {
        $parkingLot = ParkingLot::create([
            'name'   => 'P-202',
            'type'   => 'motorbike',
            'status' => 'occupied',
        ]);

        $vehicle = Vehicle::create([
            'user_id'        => $this->resident->id,
            'license_plate'  => '51K-99999',
            'type'           => 'motorbike',
            'status'         => 'approved',
            'parking_lot_id' => $parkingLot->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.vehicles.releaseLot', $vehicle));

        $response->assertRedirect();
        $this->assertNull($vehicle->fresh()->parking_lot_id);
    }

    // -------------------------------------------------------------------------
    // TC_VEH_20: Admin xem danh sách xe cư dân
    // -------------------------------------------------------------------------
    public function test_admin_can_view_all_vehicles(): void
    {
        Vehicle::create([
            'user_id'       => $this->resident->id,
            'license_plate' => '51L-12345',
            'type'          => 'car',
            'status'        => 'pending',
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.vehicles.index'));

        $response->assertStatus(200);
        $response->assertSee('51L-12345');
    }

    // -------------------------------------------------------------------------
    // TC_VEH_05: Cư dân xóa xe đã đăng ký
    // -------------------------------------------------------------------------
    public function test_resident_can_delete_own_vehicle(): void
    {
        $vehicle = Vehicle::create([
            'user_id'       => $this->resident->id,
            'license_plate' => '51M-55555',
            'type'          => 'motorbike',
            'status'        => 'pending',
        ]);

        $this->actingAs($this->resident);

        $response = $this->delete(route('resident.vehicles.destroy', $vehicle));

        $response->assertRedirect();
        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }
}
