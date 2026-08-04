<?php

namespace Tests\Feature\Ticket;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class TicketCreateTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Resident xem trang danh sách ticket.
     */
    public function test_resident_can_view_ticket_list(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $this->actingAs($resident);

        $response = $this->get(route('resident.tickets.index'));
        $response->assertStatus(200);
    }

    /**
     * Resident xem form tạo ticket mới.
     */
    public function test_resident_can_view_create_ticket_form(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $this->actingAs($resident);

        $response = $this->get(route('resident.tickets.create'));
        $response->assertStatus(200);
    }

    /**
     * Resident tạo ticket phản ánh thành công.
     */
    public function test_resident_can_create_complaint_ticket(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $this->actingAs($resident);

        $response = $this->post(route('resident.tickets.store'), [
            'ticket_type' => 'complaint',
            'title'       => 'Máy bơm nước bị hỏng',
            'description' => 'Máy bơm tầng 1 không hoạt động từ sáng sớm',
            'priority'    => 'high',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'title'       => 'Máy bơm nước bị hỏng',
            'ticket_type' => 'complaint',
            'sender_id'   => $resident->id,
            'status'      => 'pending',
        ]);
    }

    /**
     * Validate: title là bắt buộc khi tạo ticket.
     */
    public function test_ticket_creation_requires_title(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $this->actingAs($resident);

        $response = $this->post(route('resident.tickets.store'), [
            'ticket_type' => 'complaint',
            'title'       => '',
            'description' => 'Mô tả sự cố',
            'priority'    => 'medium',
        ]);

        $response->assertSessionHasErrors(['title']);
        $this->assertDatabaseCount('tickets', 0);
    }

    /**
     * Resident xem chi tiết ticket của mình.
     */
    public function test_resident_can_view_own_ticket_detail(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $ticket = Ticket::create([
            'apartment_id' => $apartment->id,
            'sender_id'    => $resident->id,
            'ticket_type'  => 'complaint',
            'title'        => 'Thang máy kêu to',
            'description'  => 'Thang máy tầng 1 phát ra tiếng kêu lạ',
            'priority'     => 'medium',
            'status'       => 'pending',
        ]);

        $this->actingAs($resident);

        $response = $this->get(route('resident.tickets.show', $ticket->id));
        $response->assertStatus(200);
        $response->assertSee('Thang máy kêu to');
    }

    /**
     * Resident có thể hủy ticket đang ở trạng thái pending.
     */
    public function test_resident_can_cancel_pending_ticket(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);
        $ticket = Ticket::create([
            'apartment_id' => $apartment->id,
            'sender_id'    => $resident->id,
            'ticket_type'  => 'complaint',
            'title'        => 'Sự cố điện',
            'description'  => 'Mất điện tầng 3',
            'priority'     => 'high',
            'status'       => 'pending',
        ]);

        $this->actingAs($resident);

        $response = $this->post(route('resident.tickets.cancel', $ticket->id));
        $response->assertRedirect();

        $this->assertEquals('cancelled', $ticket->fresh()->status);
    }
}
