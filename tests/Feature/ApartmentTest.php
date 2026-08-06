<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\ApartmentInvite;
use App\Models\Block;
use App\Models\Floor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Apartment Management Test Suite
 * Covers: TC_APT_01 → TC_APT_30
 */
class ApartmentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);
    }

    // -------------------------------------------------------------------------
    // TC_APT_01: Admin tạo Block mới thành công
    // -------------------------------------------------------------------------
    public function test_admin_can_create_new_block(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.blocks.store'), [
            'name' => 'Block C',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('blocks', ['name' => 'Block C']);
    }

    // -------------------------------------------------------------------------
    // TC_APT_02: Tạo Block trùng mã → báo lỗi
    // -------------------------------------------------------------------------
    public function test_create_duplicate_block_shows_error(): void
    {
        Block::create(['name' => 'Block A']);
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.blocks.store'), [
            'name' => 'Block A',  // trùng
        ]);

        $response->assertSessionHasErrors();
    }

    // -------------------------------------------------------------------------
    // TC_APT_03: Admin tạo Tầng mới thuộc Block
    // -------------------------------------------------------------------------
    public function test_admin_can_create_floor_under_block(): void
    {
        $block = Block::create(['name' => 'Block D']);
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.floors.store'), [
            'block_id'     => $block->id,
            'floor_number' => 10,
            'name'         => 'Tầng 10',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('floors', [
            'block_id'     => $block->id,
            'floor_number' => 10,
        ]);
    }

    // -------------------------------------------------------------------------
    // TC_APT_04: Admin tạo Căn hộ mới thuộc Tầng
    // -------------------------------------------------------------------------
    public function test_admin_can_create_apartment(): void
    {
        $block = Block::create(['name' => 'Block E']);
        $floor = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 10', 'floor_number' => 10]);
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.apartments.store'), [
            'floor_id'         => $floor->id,
            'apartment_number' => 'C1002',
            'area'             => 80,
            'status'           => 'vacant',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('apartments', ['apartment_number' => 'C1002']);
    }

    // -------------------------------------------------------------------------
    // TC_APT_05: Tạo Căn hộ trùng số phòng trong cùng tầng → lỗi
    // -------------------------------------------------------------------------
    public function test_create_duplicate_apartment_number_in_same_floor_shows_error(): void
    {
        $block = Block::create(['name' => 'Block F']);
        $floor = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 10', 'floor_number' => 10]);
        Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'C1002', 'area' => 80, 'status' => 'vacant']);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.apartments.store'), [
            'floor_id'         => $floor->id,
            'apartment_number' => 'C1002',  // trùng
            'area'             => 80,
            'status'           => 'vacant',
        ]);

        $response->assertSessionHasErrors();
    }

    // -------------------------------------------------------------------------
    // TC_APT_06: Admin cập nhật thông tin Căn hộ
    // -------------------------------------------------------------------------
    public function test_admin_can_update_apartment_info(): void
    {
        $block     = Block::create(['name' => 'Block G']);
        $floor     = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 1', 'floor_number' => 1]);
        $apartment = Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'G101', 'status' => 'available', 'area' => 80]);

        $this->actingAs($this->admin);

        $response = $this->put(route('admin.apartments.update', $apartment), [
            'floor_id'         => $floor->id,
            'apartment_number' => 'G101',
            'area'             => 85,  // cập nhật diện tích
            'status'           => 'vacant',
        ]);

        $response->assertRedirect();
        $this->assertEquals(85, $apartment->fresh()->area);
    }

    // -------------------------------------------------------------------------
    // TC_APT_07: Admin xóa Căn hộ trống thành công
    // -------------------------------------------------------------------------
    public function test_admin_can_delete_empty_apartment(): void
    {
        $block     = Block::create(['name' => 'Block H']);
        $floor     = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 1', 'floor_number' => 1]);
        $apartment = Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'H101', 'area' => 80, 'status' => 'vacant']);

        $this->actingAs($this->admin);

        $response = $this->delete(route('admin.apartments.destroy', $apartment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('apartments', ['id' => $apartment->id]);
    }

    // -------------------------------------------------------------------------
    // TC_APT_08: Chặn xóa Căn hộ đang có cư dân
    // -------------------------------------------------------------------------
    public function test_cannot_delete_apartment_with_residents(): void
    {
        $block     = Block::create(['name' => 'Block I']);
        $floor     = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 5', 'floor_number' => 5]);
        $apartment = Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'I502', 'area' => 80, 'status' => 'occupied']);
        $resident  = User::factory()->create(['role' => 'resident', 'apartment_id' => $apartment->id]);

        \App\Models\Resident::create([
            'user_id'          => $resident->id,
            'apartment_id'     => $apartment->id,
            'relationship'     => 'owner',
            'temporary_status' => 'permanent',
            'start_date'       => now()->toDateString(),
        ]);

        $this->actingAs($this->admin);

        $response = $this->delete(route('admin.apartments.destroy', $apartment));

        // Phải bị chặn: redirect với error hoặc 422/403
        $this->assertDatabaseHas('apartments', ['id' => $apartment->id]);
    }

    // -------------------------------------------------------------------------
    // TC_APT_24: Admin xem danh sách Cư dân trong Căn hộ
    // -------------------------------------------------------------------------
    public function test_admin_can_view_residents_in_apartment(): void
    {
        $block     = Block::create(['name' => 'Block J']);
        $floor     = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 5', 'floor_number' => 5]);
        $apartment = Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'A502', 'area' => 80, 'status' => 'occupied']);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.apartments.show', $apartment));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // TC_APT_26: Tìm kiếm Căn hộ theo từ khóa
    // -------------------------------------------------------------------------
    public function test_admin_can_search_apartments_by_keyword(): void
    {
        $block     = Block::create(['name' => 'Block K']);
        $floor     = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 5', 'floor_number' => 5]);
        Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'A502', 'area' => 80, 'status' => 'vacant']);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.apartments.index', ['search' => 'A502']));

        $response->assertStatus(200);
        $response->assertSee('A502');
    }

    // -------------------------------------------------------------------------
    // TC_APT_27: Lọc Căn hộ trống
    // -------------------------------------------------------------------------
    public function test_admin_can_filter_empty_apartments(): void
    {
        $block     = Block::create(['name' => 'Block L']);
        $floor     = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 1', 'floor_number' => 1]);
        Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'L101', 'area' => 80, 'status' => 'vacant']);
        Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'L102', 'area' => 80, 'status' => 'occupied']);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.apartments.index', ['status' => 'vacant']));

        $response->assertStatus(200);
        $response->assertSee('L101');
    }

    // -------------------------------------------------------------------------
    // TC_APT_09: Admin tạo Mã mời cư dân (Invitation)
    // -------------------------------------------------------------------------
    public function test_admin_can_create_invitation_code(): void
    {
        $block     = Block::create(['name' => 'Block M']);
        $floor     = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 5', 'floor_number' => 5]);
        $apartment = Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'M502', 'area' => 80, 'status' => 'vacant']);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.invitations.store'), [
            'apartment_id'           => $apartment->id,
            'intended_relationship'  => 'owner',
            'max_uses'               => 1,
            'expired_at'             => now()->addDay()->toDateTimeString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('apartment_invites', ['apartment_id' => $apartment->id]);
    }

    // -------------------------------------------------------------------------
    // TC_APT_14: Nhập Mã mời hết hạn → báo lỗi
    // -------------------------------------------------------------------------
    public function test_register_with_expired_invite_code_shows_error(): void
    {
        $block     = Block::create(['name' => 'Block N']);
        $floor     = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 1', 'floor_number' => 1]);
        $apartment = Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'N101', 'area' => 80, 'status' => 'vacant']);

        ApartmentInvite::create([
            'apartment_id'           => $apartment->id,
            'invite_code'            => 'EXPIRED-CODE',
            'intended_relationship'  => 'owner',
            'max_uses'               => 5,
            'uses_count'             => 0,
            'status'                 => 'active',
            'expired_at'             => now()->subDay(),  // đã hết hạn
        ]);

        $response = $this->post(route('resident.register.submit'), [
            'name'                  => 'Test User',
            'phone'                 => '0912345099',
            'email'                 => 'expiredtest@test.com',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
            'invite_code'           => 'EXPIRED-CODE',
        ]);

        $response->assertSessionHasErrors(['invite_code']);
    }

    // -------------------------------------------------------------------------
    // TC_APT_16: Nhập Mã mời không tồn tại → báo lỗi
    // -------------------------------------------------------------------------
    public function test_register_with_invalid_invite_code_shows_error(): void
    {
        $response = $this->post(route('resident.register.submit'), [
            'name'                  => 'Test User',
            'phone'                 => '0912345100',
            'email'                 => 'invalid@test.com',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
            'invite_code'           => 'INV-INVALID-9999',  // không tồn tại
        ]);

        $response->assertSessionHasErrors(['invite_code']);
    }

    // -------------------------------------------------------------------------
    // TC_APT_15: Nhập Mã mời đã dùng hết lượt → báo lỗi
    // -------------------------------------------------------------------------
    public function test_register_with_maxed_out_invite_code_shows_error(): void
    {
        $block     = Block::create(['name' => 'Block O']);
        $floor     = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 1', 'floor_number' => 1]);
        $apartment = Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'O101', 'area' => 80, 'status' => 'vacant']);

        ApartmentInvite::create([
            'apartment_id'          => $apartment->id,
            'invite_code'           => 'USED-CODE',
            'intended_relationship' => 'family_member',
            'max_uses'              => 1,
            'uses_count'            => 1,   // đã dùng đủ lượt
            'status'                => 'used',
        ]);

        $response = $this->post(route('resident.register.submit'), [
            'name'                  => 'Test User',
            'phone'                 => '0912345101',
            'email'                 => 'usedcode@test.com',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
            'invite_code'           => 'USED-CODE',
        ]);

        $response->assertSessionHasErrors(['invite_code']);
    }

    // -------------------------------------------------------------------------
    // TC_APT_30: Giới hạn Cư dân tối đa / Căn
    // -------------------------------------------------------------------------
    public function test_apartment_blocks_new_member_when_at_max_capacity(): void
    {
        $block     = Block::create(['name' => 'Block P']);
        $floor     = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 1', 'floor_number' => 1]);
        $apartment = Apartment::create(['floor_id' => $floor->id, 'apartment_number' => 'P101', 'area' => 80, 'status' => 'occupied']);

        // Tạo đủ 10 cư dân
        for ($i = 1; $i <= 10; $i++) {
            $u = User::factory()->create(['role' => 'resident', 'apartment_id' => $apartment->id]);
            \App\Models\Resident::create([
                'user_id'          => $u->id,
                'apartment_id'     => $apartment->id,
                'relationship'     => 'family_member',
                'temporary_status' => 'permanent',
                'start_date'       => now()->toDateString(),
            ]);
        }

        // Tạo mã mời cho căn hộ đầy
        $invite = ApartmentInvite::create([
            'apartment_id'          => $apartment->id,
            'invite_code'           => 'FULL-APT-CODE',
            'intended_relationship' => 'tenant',
            'max_uses'              => 99,
            'uses_count'            => 0,
            'status'                => 'active',
        ]);

        // Người thứ 11 cố đăng ký
        $response = $this->post(route('resident.register.submit'), [
            'name'                  => 'User 11',
            'phone'                 => '0912999999',
            'email'                 => 'user11@test.com',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
            'invite_code'           => 'FULL-APT-CODE',
        ]);

        // Căn hộ đã đầy - không được thêm vào
        $this->assertEquals(10, \App\Models\Resident::where('apartment_id', $apartment->id)->count());
    }
}
