<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_belongs_to_project_and_user()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $ticket = Ticket::factory()->for($user)->for($project)->create();

        $this->assertEquals($user->id, $ticket->user->id);
        $this->assertEquals($project->id, $ticket->project->id);
    }

    public function test_ticket_has_one_ticket_detail()
    {
        $ticket = Ticket::factory()->create();
        $ticketDetail = TicketDetail::factory()->for($ticket)->create();

        $this->assertInstanceOf(TicketDetail::class, $ticket->ticketDetail);
        $this->assertEquals($ticketDetail->id, $ticket->ticketDetail->id);
    }
}
