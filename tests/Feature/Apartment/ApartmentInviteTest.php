<?php

namespace Tests\Feature\Apartment;

use App\Models\ApartmentInvite;
use App\Models\Block;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ApartmentInviteTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin xem danh sách mã mời.
     */
    public function test_admin_can_view_invitations_list(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.invitations.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin tạo mã mời mới thành công.
     */
    public function test_admin_can_create_invitation(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654322']);
        $block     = Block::create(['name' => 'Tòa A Test']);
        $apartment = $this->makeApartment(['apartment_number' => 'A501']);

        // Update apartment's floor block to match
        $apartment->floor->update(['block_id' => $block->id]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.invitations.store'), [
            'block_id'              => $block->id,
            'apartment_id'          => $apartment->id,
            'intended_relationship' => 'owner',
            'max_uses'              => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('apartment_invites', [
            'apartment_id'          => $apartment->id,
            'intended_relationship' => 'owner',
        ]);
    }

    /**
     * Admin xóa mã mời.
     */
    public function test_admin_can_delete_invitation(): void
    {
        $admin     = $this->makeAdmin(['phone' => '0987654323']);
        $apartment = $this->makeApartment();

        $this->actingAs($admin);

        // Tạo mã mời qua controller (để có created_by)
        $invite = ApartmentInvite::create([
            'apartment_id'          => $apartment->id,
            'block_id'              => $apartment->floor->block_id,
            'intended_relationship' => 'owner',
            'invite_code'           => 'TEST1234',
            'max_uses'              => 1,
            'uses_count'            => 0,
            'status'                => 'active',
            'created_by'            => $admin->id,
        ]);

        $response = $this->delete(route('admin.invitations.destroy', $invite->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('apartment_invites', ['id' => $invite->id]);
    }
}
