<?php

namespace Tests\Feature\Apartment;

use App\Models\ApartmentMember;
use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ApartmentMemberTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Tạo owner resident record để user có quyền thêm nhân khẩu.
     */
    private function makeOwnerResident(): array
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        // Tạo Resident record với relationship = 'owner'
        Resident::create([
            'user_id'       => $resident->id,
            'apartment_id'  => $apartment->id,
            'relationship'  => 'owner',
            'status'        => 'active',
            'start_date'    => now()->toDateString(),
        ]);

        return [$resident, $apartment];
    }

    /**
     * Resident xem trang quản lý thành viên.
     */
    public function test_resident_can_view_members_page(): void
    {
        [$resident] = $this->makeOwnerResident();
        $this->actingAs($resident);

        $response = $this->get(route('resident.members.index'));
        $response->assertStatus(200);
    }

    /**
     * Chủ hộ khai báo nhân khẩu thành công.
     */
    public function test_owner_can_declare_member(): void
    {
        [$resident, $apartment] = $this->makeOwnerResident();
        $this->actingAs($resident);

        $response = $this->post(route('resident.members.declared.store'), [
            'apartment_id' => $apartment->id,
            'name'         => 'Nguyễn Văn B',
            'birth_year'   => '1990',
            'relationship' => 'spouse',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('apartment_members', [
            'apartment_id' => $apartment->id,
            'name'         => 'Nguyễn Văn B',
        ]);
    }

    /**
     * Validate: không thể khai báo nhân khẩu thiếu tên.
     */
    public function test_cannot_declare_member_without_name(): void
    {
        [$resident, $apartment] = $this->makeOwnerResident();
        $this->actingAs($resident);

        $response = $this->post(route('resident.members.declared.store'), [
            'apartment_id' => $apartment->id,
            'name'         => '',
            'relationship' => 'child',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Chủ hộ xóa nhân khẩu đã khai báo.
     */
    public function test_owner_can_delete_declared_member(): void
    {
        [$resident, $apartment] = $this->makeOwnerResident();
        $member = ApartmentMember::create([
            'apartment_id' => $apartment->id,
            'name'         => 'Nguyễn Văn C',
            'relationship' => 'parent',
            'status'       => 'verified',
        ]);

        $this->actingAs($resident);

        $response = $this->delete(route('resident.members.declared.destroy', $member));
        $response->assertRedirect();

        $this->assertDatabaseMissing('apartment_members', ['id' => $member->id]);
    }
}
