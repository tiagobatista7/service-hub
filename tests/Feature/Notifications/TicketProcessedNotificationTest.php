<?php

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketProcessedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('envia pelo canal mail e cria a mensagem de email correta', function () {

    app()->setLocale('pt_BR');

    Notification::fake();

    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    $user->notify(new TicketProcessedNotification($ticket));

    Notification::assertSentTo(
        $user,
        TicketProcessedNotification::class,
        function ($notification, $channels) use ($user, $ticket) {

            expect($notification->via($user))->toContain('mail');

            $mailData = $notification->toMail($user);

            expect($mailData)->toBeInstanceOf(MailMessage::class);

            $mailArray = $mailData->toArray();

            expect($mailArray['subject'])->toBe('Seu ticket foi processado');

            expect($mailArray['introLines'])->toContain("O seu ticket #{$ticket->id} foi processado.");

            expect($mailArray['actionText'])->toBe('Ver Ticket');

            $expectedUrl = url("/tickets/{$ticket->id}");
            expect($mailArray['actionUrl'])->toBe($expectedUrl);

            expect($mailArray['outroLines'])->toContain('Obrigado por usar o ServiceHub!');

            return true;
        }
    );
});
