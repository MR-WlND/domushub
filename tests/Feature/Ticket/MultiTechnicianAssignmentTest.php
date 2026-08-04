<?php

namespace Tests\Feature\Ticket;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class MultiTechnicianAssignmentTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Admin phân công 1 đến 5 kỹ thuật viên cho 1 phản ánh thành công.
     */
    public function test_admin_can_assign_up_to_5_technicians_to_ticket(): void
    {
        $admin     = $this->makeAdmin();
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

        $ticket = Ticket::create([
            'apartment_id' => $apartment->id,
            'sender_id'    => $resident->id,
            'ticket_type'  => 'complaint',
            'title'        => 'Sự cố điện tầng 3',
            'description'  => 'Mất điện sảnh tầng 3',
            'priority'     => 'high',
            'status'       => 'pending',
        ]);

        $techs = User::factory()->count(3)->create(['role' => 'technician', 'status' => 'active']);
        $techIds = $techs->pluck('id')->toArray();

        $this->actingAs($admin);
        $response = $this->post(route('admin.tickets.assign', $ticket->id), [
            'technician_ids' => $techIds,
        ]);

        $response->assertRedirect();
        $ticket->refresh();

        $this->assertEquals('assigned', $ticket->status);
        $this->assertCount(3, $ticket->technicians);
        $this->assertEquals($techIds[0], $ticket->handler_id);
    }

    /**
     * Admin không thể phân công nhiều hơn 5 kỹ thuật viên cho 1 phản ánh.
     */
    public function test_admin_cannot_assign_more_than_5_technicians_to_ticket(): void
    {
        $admin     = $this->makeAdmin();
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

        $ticket = Ticket::create([
            'apartment_id' => $apartment->id,
            'sender_id'    => $resident->id,
            'ticket_type'  => 'complaint',
            'title'        => 'Sự cố nước tầng 5',
            'description'  => 'Rò rỉ nước',
            'priority'     => 'urgent',
            'status'       => 'pending',
        ]);

        // Tạo 6 KTV
        $techs = User::factory()->count(6)->create(['role' => 'technician', 'status' => 'active']);
        $techIds = $techs->pluck('id')->toArray();

        $this->actingAs($admin);
        $response = $this->post(route('admin.tickets.assign', $ticket->id), [
            'technician_ids' => $techIds,
        ]);

        $response->assertSessionHasErrors(['technician_ids']);
    }

    /**
     * Kỹ thuật viên nằm trong danh sách phân công được quyền xem và cập nhật tiến độ.
     */
    public function test_assigned_technician_can_view_and_update_ticket(): void
    {
        $admin     = $this->makeAdmin();
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

        $ticket = Ticket::create([
            'apartment_id' => $apartment->id,
            'sender_id'    => $resident->id,
            'ticket_type'  => 'complaint',
            'title'        => 'Sửa thang máy số 2',
            'description'  => 'Kẹt cửa thang máy',
            'priority'     => 'high',
            'status'       => 'pending',
        ]);

        $tech1 = User::factory()->create(['role' => 'technician', 'status' => 'active']);
        $tech2 = User::factory()->create(['role' => 'technician', 'status' => 'active']);

        $ticket->technicians()->sync([$tech1->id, $tech2->id]);
        $ticket->update(['handler_id' => $tech1->id, 'status' => 'assigned']);

        // Tech2 (KTV phụ) đăng nhập và cập nhật tiến độ
        $this->actingAs($tech2);
        $response = $this->post(route('admin.tickets.update-progress', $ticket->id), [
            'status'  => 'in_progress',
            'comment' => 'KTV2 đang kiểm tra bảng điều khiển.',
        ]);

        $response->assertRedirect();
        $ticket->refresh();
        $this->assertEquals('in_progress', $ticket->status);
    }
}
