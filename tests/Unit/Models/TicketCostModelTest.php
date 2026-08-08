<?php

namespace Tests\Unit\Models;

use App\Models\Ticket;
use App\Models\TicketCost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class TicketCostModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * TicketCost thuộc về Ticket.
     */
    public function test_ticket_cost_belongs_to_ticket(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

        $ticket = Ticket::create([
            'apartment_id' => $apartment->id,
            'sender_id'    => $resident->id,
            'ticket_type'  => 'complaint',
            'title'        => 'Sửa điện',
            'description'  => 'Hỏng bóng đèn',
            'priority'     => 'low',
            'status'       => 'in_progress',
        ]);

        $cost = TicketCost::create([
            'ticket_id'   => $ticket->id,
            'cost_type'   => 'repair',
            'description' => 'Bóng đèn LED 15W',
            'amount'      => 45000,
        ]);

        $this->assertEquals($ticket->id, $cost->ticket->id);
    }
}
