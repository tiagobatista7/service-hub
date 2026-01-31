<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Notifications\TicketProcessedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function handle()
    {
        $ticket = $this->ticket->fresh();

        if (!$ticket->attachment_path || !Storage::exists($ticket->attachment_path)) {
            return;
        }

        $attachmentContent = Storage::get($ticket->attachment_path);

        $data = json_decode($attachmentContent, true);

        $ticketDetail = $ticket->ticketDetail;

        if (!$ticketDetail) {
            $ticketDetail = $ticket->ticketDetail()->create([]);
        }

        if (is_array($data)) { 
            $ticketDetail->details = json_encode($data);
            $ticketDetail->details_text = null;
        } else {
            $ticketDetail->details_text = $attachmentContent;
            $ticketDetail->details = null;
        }

        $ticketDetail->save();

        $ticket->user->notify(new TicketProcessedNotification($ticket));
    }
}
