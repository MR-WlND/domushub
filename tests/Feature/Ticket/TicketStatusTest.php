<?php

namespace Tests\Feature\Ticket;

use App\Models\Ticket;
use App\Models\TicketProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class TicketStatusTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    private function makeTicket(): array
    {
        $apartment  = $this->makeApartment();
        $resident   = $this->makeResident($apartment);
        $technician = $this->makeTechnician();

        $ticket = Ticket::create([
            'apartment_id' => $apartment->id,
            'sender_id'    => $resident->id,
            'ticket_type'  => 'complaint',
            'title'        => 'Hỏng khóa cửa',
            'description'  => 'Khóa cửa căn hộ A101 bị hỏng',
            'priority'     => 'high',
            'status'       => 'pending',
        ]);

        return [$ticket, $technician, $resident];
    }

    /**
     * Admin xem danh sách ticket.
     */
    public function test_admin_can_view_ticket_list(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654321']);
        $this->makeTicket();

        $this->actingAs($admin);
        $response = $this->get(route('admin.tickets.index'));
        $response->assertStatus(200);
    }

    /**
     * Admin xem chi tiết ticket.
     */
    public function test_admin_can_view_ticket_detail(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654322']);
        [$ticket] = $this->makeTicket();

        $this->actingAs($admin);
        $response = $this->get(route('admin.tickets.show', $ticket->id));
        $response->assertStatus(200);
        $response->assertSee('Hỏng khóa cửa');
    }

    /**
     * Admin gán kỹ thuật viên cho ticket.
     */
    public function test_admin_can_assign_technician_to_ticket(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654323']);
        [$ticket, $technician] = $this->makeTicket();

        $this->actingAs($admin);
        $response = $this->post(route('admin.tickets.assign', $ticket->id), [
            'handler_id' => $technician->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals($technician->id, $ticket->fresh()->handler_id);
        $this->assertEquals('assigned', $ticket->fresh()->status);
    }

    /**
     * Admin approve nghiệm thu ticket (cần ticket đã completed và có progress record).
     */
    public function test_admin_can_approve_completed_ticket(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654324']);
        [$ticket, $technician] = $this->makeTicket();

        // Đưa ticket về trạng thái completed với progress record
        $ticket->update(['status' => 'completed', 'handler_id' => $technician->id, 'completed_at' => now()]);
        TicketProgress::create([
            'ticket_id'  => $ticket->id,
            'status'     => 'completed',
            'comment'    => 'Đã hoàn thành sửa chữa',
            'updated_by' => $technician->id,
        ]);

        $this->actingAs($admin);

        // approveReview trả về JSON
        $response = $this->postJson(route('admin.tickets.review.approve', $ticket->id));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Admin từ chối nghiệm thu ticket.
     */
    public function test_admin_can_reject_ticket_review(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654325']);
        [$ticket, $technician] = $this->makeTicket();

        $ticket->update(['status' => 'completed', 'handler_id' => $technician->id, 'completed_at' => now()]);
        TicketProgress::create([
            'ticket_id'  => $ticket->id,
            'status'     => 'completed',
            'comment'    => 'Đã hoàn thành',
            'updated_by' => $technician->id,
        ]);

        $this->actingAs($admin);

        $response = $this->postJson(route('admin.tickets.review.reject', $ticket->id), [
            'reject_reason' => 'Chưa sửa được hoàn toàn, cần làm lại',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals('in_progress', $ticket->fresh()->status);
    }
}
