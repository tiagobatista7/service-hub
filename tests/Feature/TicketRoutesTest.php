<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->company = Company::factory()->create();
    $this->project = Project::factory()->for($this->company)->create();
    $this->actingAs($this->user);
});

it('retorna status 200 na rota tickets.index', function () {
    $response = $this->get(route('tickets.index'));
    $response->assertStatus(200);
});

it('retorna status 200 na rota tickets.create', function () {
    $response = $this->get(route('tickets.create'));
    $response->assertStatus(200);
});

it('cria ticket pela rota tickets.store', function () {
    $data = [
        'title' => 'Ticket Teste',
        'description' => 'Descrição do ticket',
        'project_id' => $this->project->id,
        'status' => 'ativo',
        'user_id' => $this->user->id,
    ];

    $response = $this->post(route('tickets.store'), $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('tickets', ['title' => 'Ticket Teste', 'user_id' => $this->user->id]);
});

it('retorna status 200 na rota tickets.show', function () {
    $ticket = Ticket::factory()->for($this->project)->create(['user_id' => $this->user->id]);

    $response = $this->get(route('tickets.show', $ticket));

    $response->assertStatus(200);
});

it('atualiza ticket pela rota tickets.update', function () {
    $ticket = Ticket::factory()->for($this->project)->create(['title' => 'Old Title', 'user_id' => $this->user->id]);
    $data = ['title' => 'Updated Title', 'status' => 'concluído'];

    $response = $this->put(route('tickets.update', $ticket), $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'title' => 'Updated Title']);
});

it('deleta ticket pela rota tickets.destroy', function () {
    $ticket = Ticket::factory()->for($this->project)->create(['user_id' => $this->user->id]);

    $response = $this->delete(route('tickets.destroy', $ticket));

    $response->assertRedirect();
    $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
});

it('atualiza status do ticket pela rota tickets.updateStatus', function () {
    $ticket = Ticket::factory()->for($this->project)->create(['status' => 'ativo', 'user_id' => $this->user->id]);

    $response = $this->patch(route('tickets.updateStatus', $ticket), ['status' => 'concluído']);

    $response->assertRedirect();
    expect($ticket->fresh()->status)->toBe('concluído');
});

it('retorna status 200 na rota projects.tickets.index', function () {
    $response = $this->get(route('projects.tickets.index', $this->project));
    $response->assertStatus(200);
});

it('retorna status 200 na rota projects.tickets.create', function () {
    $response = $this->get(route('projects.tickets.create', $this->project));
    $response->assertStatus(200);
});

it('cria ticket pela rota projects.tickets.store', function () {
    $data = [
        'title' => 'Ticket in Project',
        'description' => 'Descrição',
        'status' => 'ativo',
        'user_id' => $this->user->id,
    ];

    $response = $this->post(route('projects.tickets.store', $this->project), $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('tickets', ['title' => 'Ticket in Project', 'user_id' => $this->user->id]);
});
