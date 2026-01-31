<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\AnonymousNotifiable;

class TicketProcessedNotification extends Notification
{
    use Queueable;

    protected Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Seu ticket foi processado')
            ->line("O anexo do ticket #{$this->ticket->id} foi processado e os dados foram atualizados.")
            ->action('Ver Ticket', url("/tickets/{$this->ticket->id}"))
            ->line('Obrigado por usar o ServiceHub!');
    }
}
