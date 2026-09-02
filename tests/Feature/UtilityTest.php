<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Invoice;
use App\Models\User;
use App\Models\UtilityMeter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Utility Meter & Invoice Test Suite
 * Covers: TC_UTIL_01 → TC_UTIL_27
 */
class UtilityTest extends TestCase
{
    use RefreshDatabase;

    private User      $admin;
    private User      $staff;
    private Apartment $apartment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->staff = User::factory()->create(['role' => 'staff', 'status' => 'active']);

        $block           = Block::create(['name' => 'Block A']);
        $floor           = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 5', 'floor_number' => 5]);
        $this->apartment = Apartment::create([
            'floor_id'         => $floor->id,
            'apartment_number' => 'A502',
            'area'             => 75,
            'status'           => 'occupied',
        ]);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_01: Nhập chỉ số nước thủ công → tính tiêu thụ và lưu Pending
    // -------------------------------------------------------------------------
    public function test_staff_can_record_water_meter_reading(): void
    {
        $technician = User::factory()->create(['role' => 'technician', 'status' => 'active']);
        $this->actingAs($technician);

        $response = $this->post(route('admin.utility-readings.store'), [
            'apartment_id' => $this->apartment->id,
            'type'         => 'water',
            'old_value'    => 85,
            'new_value'    => 95,
            'record_month' => 7,
            'record_year'  => 2026,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('utility_meters', [
            'apartment_id' => $this->apartment->id,
            'old_value'    => 85,
            'new_value'    => 95,
            'status'       => 'pending',
        ]);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_02: Chỉ số nước mới < cũ → báo lỗi validate
    // -------------------------------------------------------------------------
    public function test_new_meter_value_less_than_old_shows_error(): void
    {
        $technician = User::factory()->create(['role' => 'technician', 'status' => 'active']);
        $this->actingAs($technician);

        $response = $this->post(route('admin.utility-readings.store'), [
            'apartment_id' => $this->apartment->id,
            'type'         => 'water',
            'old_value'    => 85,
            'new_value'    => 70,  // nhỏ hơn old_value
            'record_month' => 7,
            'record_year'  => 2026,
        ]);

        $response->assertSessionHasErrors(['new_value']);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_03: Nhập ký tự chữ vào ô chỉ số → báo lỗi
    // -------------------------------------------------------------------------
    public function test_non_numeric_meter_value_shows_validation_error(): void
    {
        $technician = User::factory()->create(['role' => 'technician', 'status' => 'active']);
        $this->actingAs($technician);

        $response = $this->post(route('admin.utility-readings.store'), [
            'apartment_id' => $this->apartment->id,
            'type'         => 'water',
            'old_value'    => 'abc',  // không phải số
            'new_value'    => 'xyz',
            'record_month' => 7,
            'record_year'  => 2026,
        ]);

        $response->assertSessionHasErrors();
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_06: Thay công tơ mới (is_reset) → old_value = 0
    // -------------------------------------------------------------------------
    public function test_meter_reset_sets_old_value_to_zero(): void
    {
        $technician = User::factory()->create(['role' => 'technician', 'status' => 'active']);
        $this->actingAs($technician);

        $response = $this->post(route('admin.utility-readings.store'), [
            'apartment_id' => $this->apartment->id,
            'type'         => 'water',
            'old_value'    => 0,
            'new_value'    => 45,
            'is_reset'     => true,
            'record_month' => 7,
            'record_year'  => 2026,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('utility_meters', [
            'apartment_id' => $this->apartment->id,
            'new_value'    => 45,
            'is_reset'     => true,
        ]);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_07: Kế toán từ chối chỉ số nước
    // -------------------------------------------------------------------------
    public function test_staff_can_reject_meter_reading(): void
    {
        $meter = UtilityMeter::create([
            'apartment_id' => $this->apartment->id,
            'type'         => 'water',
            'old_value'    => 85,
            'new_value'    => 95,
            'record_month' => 7,
            'record_year'  => 2026,
            'recorded_by'  => $this->staff->id,
            'status'       => 'pending',
        ]);

        $this->actingAs($this->staff);

        $response = $this->post(route('admin.utility-readings.reject', $meter), [
            'reject_reason' => 'Ảnh không rõ số',
        ]);

        $response->assertRedirect();
        $this->assertEquals('rejected', $meter->fresh()->status);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_11: Kế toán phê duyệt chỉ số nước
    // -------------------------------------------------------------------------
    public function test_staff_can_approve_meter_reading(): void
    {
        $meter = UtilityMeter::create([
            'apartment_id' => $this->apartment->id,
            'type'         => 'water',
            'old_value'    => 85,
            'new_value'    => 95,
            'record_month' => 7,
            'record_year'  => 2026,
            'recorded_by'  => $this->staff->id,
            'status'       => 'pending',
        ]);

        $this->actingAs($this->staff);

        $response = $this->post(route('admin.utility-readings.approve', $meter));

        $response->assertRedirect();
        $this->assertEquals('approved', $meter->fresh()->status);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_12: Xuất hóa đơn hàng loạt
    // -------------------------------------------------------------------------
    public function test_staff_can_batch_create_invoices(): void
    {
        $this->actingAs($this->staff);

        $response = $this->post(route('admin.utility-readings.batch-approve'), [
            'month' => 7,
            'year'  => 2026,
        ]);

        $response->assertRedirect();
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_17: Sửa Hóa đơn chưa thanh toán → thành công
    // -------------------------------------------------------------------------
    public function test_staff_can_edit_unpaid_invoice(): void
    {
        $resident = User::factory()->create(['role' => 'resident', 'apartment_id' => $this->apartment->id]);
        $invoice  = Invoice::create([
            'apartment_id' => $this->apartment->id,
            'month'        => 7,
            'year'         => 2026,
            'total_amount' => 500000,
            'status'       => 'unpaid',
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.invoices.edit', $invoice));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_18: Chặn sửa Hóa đơn ĐÃ THANH TOÁN
    // -------------------------------------------------------------------------
    public function test_paid_invoice_edit_is_blocked(): void
    {
        $invoice = Invoice::create([
            'apartment_id' => $this->apartment->id,
            'month'        => 6,
            'year'         => 2026,
            'total_amount' => 600000,
            'status'       => 'paid',
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.invoices.edit', $invoice));

        // Phải bị chặn: redirect với error hoặc 403
        $this->assertContains($response->status(), [302, 403]);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_19: Admin hủy Hóa đơn lỗi
    // -------------------------------------------------------------------------
    public function test_admin_can_cancel_invoice(): void
    {
        $invoice = Invoice::create([
            'apartment_id' => $this->apartment->id,
            'month'        => 8,
            'year'         => 2026,
            'total_amount' => 300000,
            'status'       => 'unpaid',
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.invoices.cancel', $invoice));

        $response->assertRedirect();
        $this->assertEquals('canceled', $invoice->fresh()->status);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_21: Tìm kiếm Hóa đơn theo mã / căn / tháng
    // -------------------------------------------------------------------------
    public function test_admin_can_search_invoices(): void
    {
        Invoice::create([
            'apartment_id' => $this->apartment->id,
            'month'        => 7,
            'year'         => 2026,
            'total_amount' => 400000,
            'status'       => 'unpaid',
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.invoices.index', [
            'search' => 'A502',
            'month'  => 7,
            'year'   => 2026,
        ]));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_22: Lọc Hóa đơn chưa thanh toán
    // -------------------------------------------------------------------------
    public function test_admin_can_filter_unpaid_invoices(): void
    {
        Invoice::create(['apartment_id' => $this->apartment->id, 'month' => 7, 'year' => 2026, 'total_amount' => 400000, 'status' => 'unpaid']);
        Invoice::create(['apartment_id' => $this->apartment->id, 'month' => 6, 'year' => 2026, 'total_amount' => 500000, 'status' => 'paid']);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.invoices.index', ['status' => 'unpaid']));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_15: Cư dân xem chi tiết Hóa đơn
    // -------------------------------------------------------------------------
    public function test_resident_can_view_own_invoice_detail(): void
    {
        $resident = User::factory()->create([
            'role'         => 'resident',
            'status'       => 'active',
            'apartment_id' => $this->apartment->id,
        ]);

        \App\Models\Resident::create([
            'user_id'          => $resident->id,
            'apartment_id'     => $this->apartment->id,
            'relationship'     => 'owner',
            'temporary_status' => 'permanent',
            'start_date'       => now()->toDateString(),
        ]);

        $invoice = Invoice::create([
            'apartment_id' => $this->apartment->id,
            'month'        => 7,
            'year'         => 2026,
            'total_amount' => 400000,
            'status'       => 'unpaid',
        ]);

        $this->actingAs($resident);

        $response = $this->get(route('resident.invoices.show', $invoice->id));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_UTIL_26: Cư dân khiếu nại chỉ số nước → tạo Ticket
    // -------------------------------------------------------------------------
    public function test_resident_can_submit_water_dispute_ticket(): void
    {
        $resident = User::factory()->create([
            'role'         => 'resident',
            'status'       => 'active',
            'apartment_id' => $this->apartment->id,
        ]);
        \App\Models\Resident::create([
            'user_id'          => $resident->id,
            'apartment_id'     => $this->apartment->id,
            'relationship'     => 'owner',
            'temporary_status' => 'permanent',
            'start_date'       => now()->toDateString(),
        ]);

        $invoice = Invoice::create([
            'apartment_id' => $this->apartment->id,
            'month'        => 7,
            'year'         => 2026,
            'total_amount' => 400000,
            'status'       => 'unpaid',
        ]);

        $this->actingAs($resident);

        $response = $this->post(route('resident.invoices.complain-water', $invoice));

        $response->assertRedirect();
        // Ticket phải được tạo
        $this->assertDatabaseHas('tickets', [
            'created_by' => $resident->id,
        ]);
    }
}
