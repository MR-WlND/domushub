<?php

namespace Tests\Unit\Models;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class TicketModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Ticket thuộc về Apartment và Sender.
     */
    public function test_ticket_relationships(): void
    {
        $apartment = $this->makeApartment();
        $resident  = $this->makeResident($apartment);

        $ticket = Ticket::create([
            'apartment_id' => $apartment->id,
            'sender_id'    => $resident->id,
            'ticket_type'  => 'complaint',
            'title'        => 'Thang máy hỏng',
            'description'  => 'Mô tả sự cố',
            'priority'     => 'high',
            'status'       => 'pending',
        ]);

        $this->assertEquals($apartment->id, $ticket->apartment->id);
        $this->assertEquals($resident->id, $ticket->sender->id);
    }
}
