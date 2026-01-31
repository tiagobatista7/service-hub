<?php

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketProcessedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;

uses(RefreshDatabase::class);

it('envia pelo canal mail e cria a mensagem de email correta', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    $notification = new TicketProcessedNotification($ticket);

    expect($notification->via($user))->toContain('mail');

    $mailData = $notification->toMail($user);

    expect($mailData)->toBeInstanceOf(MailMessage::class);

    expect($mailData->subject)->toBe('Seu ticket foi processado');

    expect($mailData->introLines)->toContain("O anexo do ticket #{$ticket->id} foi processado e os dados foram atualizados.");

    expect($mailData->actionText)->toBe('Ver Ticket');

    $expectedUrl = url("/tickets/{$ticket->id}");

    expect($mailData->actionUrl)->toBe($expectedUrl);

    expect($mailData->outroLines)->toContain('Obrigado por usar o ServiceHub!');
});
