<?php

namespace Tests\Feature\Ticket;

use App\Models\Ticket;
use App\Models\TicketCost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class TicketCostTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    private function makeInProgressTicket(): array
    {
        $apartment  = $this->makeApartment();
        $resident   = $this->makeResident($apartment);
        $technician = $this->makeTechnician();

        $ticket = Ticket::create([
            'apartment_id' => $apartment->id,
            'sender_id'    => $resident->id,
            'handler_id'   => $technician->id,
            'ticket_type'  => 'complaint',
            'title'        => 'Sửa máy lạnh',
            'description'  => 'Máy lạnh không hoạt động',
            'priority'     => 'medium',
            'status'       => 'in_progress',
        ]);

        return [$ticket, $technician];
    }

    /**
     * Admin thêm chi phí sửa chữa cho ticket.
     */
    public function test_admin_can_add_repair_cost_to_ticket(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654350']);
        [$ticket] = $this->makeInProgressTicket();
        $this->actingAs($admin);

        $response = $this->post(route('admin.tickets.add-cost', $ticket->id), [
            'cost_type'   => 'repair',
            'description' => 'Gas máy lạnh R22',
            'amount'      => 250000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ticket_costs', [
            'ticket_id'   => $ticket->id,
            'description' => 'Gas máy lạnh R22',
            'amount'      => 250000,
        ]);
    }

    /**
     * Admin thêm nhiều chi phí và tổng phải đúng.
     */
    public function test_admin_can_add_multiple_costs(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654351']);
        [$ticket] = $this->makeInProgressTicket();
        $this->actingAs($admin);

        $this->post(route('admin.tickets.add-cost', $ticket->id), [
            'cost_type'   => 'repair',
            'description' => 'Dây điện 3m',
            'amount'      => 50000,
        ]);

        $this->post(route('admin.tickets.add-cost', $ticket->id), [
            'cost_type'   => 'repair',
            'description' => 'Công thợ',
            'amount'      => 150000,
        ]);

        $this->assertDatabaseCount('ticket_costs', 2);
        $total = TicketCost::where('ticket_id', $ticket->id)->sum('amount');
        $this->assertEquals(200000, $total);
    }

    /**
     * Admin xóa chi phí vật tư khỏi ticket.
     */
    public function test_admin_can_delete_ticket_cost(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654352']);
        [$ticket] = $this->makeInProgressTicket();

        $cost = TicketCost::create([
            'ticket_id'   => $ticket->id,
            'cost_type'   => 'repair',
            'description' => 'Vật tư cần xóa',
            'amount'      => 100000,
        ]);

        $this->actingAs($admin);

        $response = $this->delete(route('admin.tickets.delete-cost', [$ticket->id, $cost->id]));
        $response->assertRedirect();

        $this->assertDatabaseMissing('ticket_costs', ['id' => $cost->id]);
    }

    /**
     * Validate: amount phải ít nhất 1000đ.
     */
    public function test_cost_amount_must_be_at_least_1000(): void
    {
        $admin = $this->makeAdmin(['phone' => '0987654353']);
        [$ticket] = $this->makeInProgressTicket();
        $this->actingAs($admin);

        $response = $this->post(route('admin.tickets.add-cost', $ticket->id), [
            'cost_type'   => 'repair',
            'description' => 'Chi phí nhỏ',
            'amount'      => 500,
        ]);

        $response->assertSessionHasErrors(['amount']);
    }
}
