<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\TicketDetail;

class TicketDetailsSeeder extends Seeder
{
    public function run()
    {
        Ticket::all()->each(function ($ticket) {
            TicketDetail::factory()->create([
                'ticket_id' => $ticket->id,
            ]);
        });
    }
}
