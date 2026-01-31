<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketProcessedNotification extends Notification
{
    protected $ticket;

    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('notifications.ticket_processed_subject'))
            ->greeting(__('notifications.greeting', ['name' => $notifiable->name]))
            ->line(__('notifications.ticket_processed_intro', ['ticket_id' => $this->ticket->id]))
            ->action(__('notifications.view_ticket'), url("/tickets/{$this->ticket->id}"))
            ->line(__('notifications.thank_you'));
    }
}
