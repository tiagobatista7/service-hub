<?php

namespace Tests\Unit\Models;

use App\Models\Ticket;
use App\Models\TicketDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_detail_belongs_to_ticket()
    {
        $ticket = Ticket::factory()->create();
        $ticketDetail = TicketDetail::factory()->for($ticket)->create();

        $this->assertEquals($ticket->id, $ticketDetail->ticket->id);
    }
}
