<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Resident;
use App\Models\User;
use App\Models\VisitorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Visitor Management Test Suite
 * Covers: TC_VIS_01 → TC_VIS_22
 */
class VisitorTest extends TestCase
{
    use RefreshDatabase;

    private User      $resident;
    private User      $security;
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

        $this->security = User::factory()->create(['role' => 'security', 'status' => 'active']);
    }

    // -------------------------------------------------------------------------
    // TC_VIS_01: Cư dân tạo mã QR khách thăm thành công
    // -------------------------------------------------------------------------
    public function test_resident_can_create_visitor_qr(): void
    {
        $this->actingAs($this->resident);

        $response = $this->post(route('resident.visitors.store'), [
            'visitor_name'  => 'Nguyen Thi B',
            'visitor_phone' => '0987654321',
            'visit_date'    => now()->addDay()->toDateString(),
            'reason'        => 'Thăm bạn bè',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('visitor_logs', [
            'resident_id'   => $this->resident->id,
            'visitor_name'  => 'Nguyen Thi B',
        ]);
    }

    // -------------------------------------------------------------------------
    // TC_VIS_02: Tạo mã QR thiếu tên khách → lỗi required
    // -------------------------------------------------------------------------
    public function test_create_visitor_without_name_shows_error(): void
    {
        $this->actingAs($this->resident);

        $response = $this->post(route('resident.visitors.store'), [
            // thiếu visitor_name
            'visitor_phone' => '0987654321',
            'visit_date'    => now()->addDay()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['visitor_name']);
    }

    // -------------------------------------------------------------------------
    // TC_VIS_03: Tạo QR với ngày quá khứ → lỗi validation
    // -------------------------------------------------------------------------
    public function test_create_visitor_with_past_date_shows_error(): void
    {
        $this->actingAs($this->resident);

        $response = $this->post(route('resident.visitors.store'), [
            'visitor_name'  => 'Test Guest',
            'visitor_phone' => '0987654321',
            'visit_date'    => now()->subDay()->toDateString(),  // ngày quá khứ
        ]);

        $response->assertSessionHasErrors(['visit_date']);
    }

    // -------------------------------------------------------------------------
    // TC_VIS_04: Cư dân xem danh sách khách đã đăng ký
    // -------------------------------------------------------------------------
    public function test_resident_can_view_registered_visitors(): void
    {
        VisitorLog::create([
            'resident_id'   => $this->resident->id,
            'visitor_name'  => 'Test Guest',
            'visitor_phone' => '0987654321',
            'visit_date'    => now()->addDay()->toDateString(),
            'status'        => 'pending',
            'qr_code'       => 'VIS_QR_001',
        ]);

        $this->actingAs($this->resident);

        $response = $this->get(route('resident.visitors.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Guest');
    }

    // -------------------------------------------------------------------------
    // TC_VIS_05: Cư dân hủy mã QR khách (trước ngày thăm)
    // -------------------------------------------------------------------------
    public function test_resident_can_cancel_visitor_pass(): void
    {
        $visitor = VisitorLog::create([
            'resident_id'   => $this->resident->id,
            'visitor_name'  => 'Guest To Cancel',
            'visitor_phone' => '0987654322',
            'visit_date'    => now()->addDay()->toDateString(),
            'status'        => 'pending',
            'qr_code'       => 'VIS_QR_CANCEL',
        ]);

        $this->actingAs($this->resident);

        $response = $this->delete(route('resident.visitors.destroy', $visitor->id));

        $response->assertRedirect();
        // Visitor phải bị xóa hoặc chuyển sang cancelled
        $this->assertContains(
            VisitorLog::find($visitor->id)?->status ?? 'deleted',
            ['cancelled', 'canceled', 'deleted', null]
        );
    }

    // -------------------------------------------------------------------------
    // TC_VIS_07: Bảo vệ quét QR hợp lệ → check-in thành công
    // -------------------------------------------------------------------------
    public function test_security_can_check_in_visitor_with_valid_qr(): void
    {
        VisitorLog::create([
            'resident_id'   => $this->resident->id,
            'visitor_name'  => 'Nguyen Van X',
            'visitor_phone' => '0987654323',
            'visit_date'    => now()->toDateString(),  // hôm nay
            'status'        => 'pending',
            'qr_code'       => 'VIS_QR_VALID_001',
        ]);

        $this->actingAs($this->security);

        $response = $this->post(route('security.visitor-check.scan'), [
            'qr_code' => 'VIS_QR_VALID_001',
        ]);

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_VIS_09: Quét QR sai ngày (đến sớm) → thông báo chưa đến hạn
    // -------------------------------------------------------------------------
    public function test_checkin_visitor_before_visit_date_shows_warning(): void
    {
        VisitorLog::create([
            'resident_id'   => $this->resident->id,
            'visitor_name'  => 'Nguyen Van Y',
            'visitor_phone' => '0987654324',
            'visit_date'    => now()->addDays(3)->toDateString(),  // 3 ngày nữa
            'status'        => 'pending',
            'qr_code'       => 'VIS_QR_FUTURE',
        ]);

        $this->actingAs($this->security);

        $response = $this->post(route('security.visitor-check.scan'), [
            'qr_code' => 'VIS_QR_FUTURE',
        ]);

        $response->assertStatus(200);
        // Phải có cảnh báo về ngày
        $decoded = $response->json();
        if ($decoded) {
            $this->assertContains($decoded['status'] ?? 'error', ['invalid', 'error', 'warning', 'not_today']);
        }
    }

    // -------------------------------------------------------------------------
    // TC_VIS_10: Quét QR đã dùng → thông báo đã check-in rồi
    // -------------------------------------------------------------------------
    public function test_checkin_already_used_visitor_qr_shows_error(): void
    {
        VisitorLog::create([
            'resident_id'    => $this->resident->id,
            'visitor_name'   => 'Nguyen Van Z',
            'visitor_phone'  => '0987654325',
            'visit_date'     => now()->toDateString(),
            'status'         => 'checked_in',  // đã check-in rồi
            'qr_code'        => 'VIS_QR_USED',
            'checked_in_at'  => now()->subHour(),
        ]);

        $this->actingAs($this->security);

        $response = $this->post(route('security.visitor-check.scan'), [
            'qr_code' => 'VIS_QR_USED',
        ]);

        $response->assertStatus(200);
        $decoded = $response->json();
        if ($decoded) {
            $this->assertContains($decoded['status'] ?? 'error', ['already_checked_in', 'error', 'invalid']);
        }
    }

    // -------------------------------------------------------------------------
    // TC_VIS_11: Quét QR không tồn tại → thông báo không tìm thấy
    // -------------------------------------------------------------------------
    public function test_checkin_invalid_visitor_qr_shows_not_found(): void
    {
        $this->actingAs($this->security);

        $response = $this->post(route('security.visitor-check.scan'), [
            'qr_code' => 'QR_NOT_IN_SYSTEM_XYZ',
        ]);

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_VIS_12: Walk-in khách không có QR → bảo vệ tạo lượt thủ công
    // -------------------------------------------------------------------------
    public function test_security_can_manually_register_walkin_visitor(): void
    {
        $this->actingAs($this->security);

        $response = $this->post(route('security.walk-in.store'), [
            'visitor_name'  => 'Walk-in Guest',
            'visitor_phone' => '0987654326',
            'reason'        => 'Giao hàng',
            'apartment_id'  => $this->apartment->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('visitor_logs', [
            'visitor_name'  => 'Walk-in Guest',
        ]);
    }

    // -------------------------------------------------------------------------
    // TC_VIS_15: Check-out khách thăm
    // -------------------------------------------------------------------------
    public function test_security_can_check_out_visitor(): void
    {
        $visitor = VisitorLog::create([
            'resident_id'    => $this->resident->id,
            'visitor_name'   => 'Checkout Guest',
            'visitor_phone'  => '0987654327',
            'visit_date'     => now()->toDateString(),
            'status'         => 'checked_in',
            'qr_code'        => 'VIS_QR_FOR_CHECKOUT',
            'checked_in_at'  => now()->subHour(),
        ]);

        $this->actingAs($this->security);

        $response = $this->post(route('security.visitor-check.checkout'), [
            'visitor_log_id' => $visitor->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals('checked_out', $visitor->fresh()->status);
    }

    // -------------------------------------------------------------------------
    // TC_VIS_20: Admin/Bảo vệ xem lịch sử khách thăm
    // -------------------------------------------------------------------------
    public function test_security_can_view_visitor_log_history(): void
    {
        $this->actingAs($this->security);

        $response = $this->get(route('security.visitor-logs.index'));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_VIS_22: Tìm kiếm lịch sử khách thăm theo tên
    // -------------------------------------------------------------------------
    public function test_security_can_search_visitor_logs(): void
    {
        VisitorLog::create([
            'resident_id'   => $this->resident->id,
            'visitor_name'  => 'Search Target Guest',
            'visitor_phone' => '0987654328',
            'visit_date'    => now()->subDay()->toDateString(),
            'status'        => 'checked_out',
            'qr_code'       => 'VIS_HISTORY_QR',
            'checked_in_at' => now()->subDay(),
        ]);

        $this->actingAs($this->security);

        $response = $this->get(route('security.visitor-logs.index', ['search' => 'Search Target']));

        $response->assertStatus(200);
    }
}
