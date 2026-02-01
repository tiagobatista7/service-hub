<?php

use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->ticket = Ticket::factory()->for($this->user)->create();
    $this->ticketDetail = TicketDetail::factory()->for($this->ticket)->create();
    $this->actingAs($this->user);
});

it('retorna status 200 na rota tickets.details.index', function () {
    $response = $this->get(route('tickets.details.index', $this->ticket));
    $response->assertStatus(200);
});

it('retorna status 200 na rota tickets.details.create', function () {
    $response = $this->get(route('tickets.details.create', $this->ticket));
    $response->assertStatus(200);
});

it('cria um ticket detail pela rota tickets.details.store', function () {
    $data = [
        'technical_data' => ['key' => 'value'],
        'details_text' => null,
    ];

    $response = $this->post(route('tickets.details.store', $this->ticket), $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('ticket_details', [
        'ticket_id' => $this->ticket->id,
    ]);
});

it('retorna status 200 na rota ticket-details.show', function () {
    $response = $this->get(route('ticket-details.show', $this->ticketDetail));
    $response->assertStatus(200);
});

it('atualiza um ticket detail pela rota ticket-details.update', function () {
    $data = [
        'technical_data' => ['key' => 'updated'],
        'details_text' => 'Texto atualizado',
    ];

    $this->ticketDetail->technical_data = ['key' => 'old'];
    $this->ticketDetail->save();

    $response = $this->put(route('ticket-details.update', $this->ticketDetail), $data);

    $response->assertRedirect();

    $fresh = $this->ticketDetail->fresh();

    expect($fresh->details_text)->toBe('Texto atualizado');
    expect($fresh->technical_data)->toBeArray();
    expect($fresh->technical_data['key'])->toBe('updated');
});


it('atualiza o status do ticket detail pela rota ticket-details.updateStatus', function () {
    $data = ['status' => 'concluído'];

    $response = $this->patch(route('ticket-details.updateStatus', $this->ticketDetail), $data);

    $response->assertRedirect();
    expect($this->ticketDetail->fresh()->status)->toBe('concluído');
});

it('deleta um ticket detail pela rota ticket-details.destroy', function () {
    $response = $this->delete(route('ticket-details.destroy', $this->ticketDetail));

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseMissing('ticket_details', ['id' => $this->ticketDetail->id]);
});
