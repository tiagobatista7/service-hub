<?php

use App\Jobs\ProcessTicketJob;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Notifications\TicketProcessedNotification;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
    Storage::fake();
    $this->user = User::factory()->create();
});

it('não faz nada se o ticket não tiver anexo', function () {
    $ticket = Ticket::factory()->for($this->user)->create([
        'attachment_path' => null,
    ]);

    ProcessTicketJob::dispatchSync($ticket);

    $ticket->refresh();

    expect($ticket->ticketDetail)->toBeNull();
    Notification::assertNothingSent();
});

it('processa anexo JSON e atualiza o detalhe do ticket', function () {
    $jsonContent = json_encode(['key' => 'value']);
    $filePath = 'attachments/test.json';

    $ticket = Ticket::factory()->for($this->user)->create([
        'attachment_path' => $filePath,
    ]);

    Storage::shouldReceive('exists')
        ->once()
        ->with($filePath)
        ->andReturn(true);

    Storage::shouldReceive('get')
        ->once()
        ->with($filePath)
        ->andReturn($jsonContent);

    ProcessTicketJob::dispatchSync($ticket);

    $ticket->refresh();

    $ticketDetail = $ticket->ticketDetail()->first();

    expect($ticketDetail)->not->toBeNull();
    expect(json_decode($ticketDetail->details, true))->toBe(['key' => 'value']);
    expect($ticketDetail->details_text)->toBeNull();

    Notification::assertSentTo($this->user, TicketProcessedNotification::class);
});

it('processa anexo de texto simples e atualiza o detalhe do ticket', function () {
    $textContent = "Some plain text content";
    $filePath = 'attachments/test.txt';

    $ticket = Ticket::factory()->for($this->user)->create([
        'attachment_path' => $filePath,
    ]);

    Storage::shouldReceive('exists')
        ->once()
        ->with($filePath)
        ->andReturn(true);

    Storage::shouldReceive('get')
        ->once()
        ->with($filePath)
        ->andReturn($textContent);

    ProcessTicketJob::dispatchSync($ticket);

    $ticket->refresh();

    $ticketDetail = $ticket->ticketDetail()->first();

    expect($ticketDetail)->not->toBeNull();
    expect($ticketDetail->details_text)->toBe($textContent);
    expect($ticketDetail->details)->toBeNull();

    Notification::assertSentTo($this->user, TicketProcessedNotification::class);
});
