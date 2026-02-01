<?php

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->project = Project::factory()->for($this->user)->create();
    $this->actingAs($this->user);
});

it('exibe a lista de tickets', function () {
    Ticket::factory()->for($this->project)->for($this->user)->create(['title' => 'Ticket de Teste']);

    $response = $this->get(route('tickets.index', ['project' => $this->project->id]));

    $response->assertStatus(200);

    $response->assertInertia(
        fn(Assert $page) =>
        $page->component('Tickets/Index')
            ->has(
                'tickets.0',
                fn(Assert $ticket) =>
                $ticket->where('title', 'Ticket de Teste')->etc()
            )
    );
});

it('exibe o formulário de criação de ticket', function () {
    $response = $this->get(route('tickets.create', ['project' => $this->project->id]));

    $response->assertStatus(200);

    $response->assertInertia(
        fn(Assert $page) =>
        $page->component('Tickets/Create')
    );
});

it('cria um novo ticket', function () {
    $data = [
        'title' => 'Novo Ticket',
        'description' => 'Descrição do ticket',
        'project_id' => $this->project->id,
        'status' => 'pendente',
    ];

    $response = $this->post(route('tickets.store'), $data);

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseHas('tickets', ['title' => 'Novo Ticket', 'project_id' => $this->project->id]);
});

it('exibe um ticket específico', function () {
    $ticket = Ticket::factory()->for($this->project)->for($this->user)->create(['title' => 'Ticket de Teste']);

    $response = $this->get(route('tickets.show', $ticket));

    $response->assertStatus(200);

    $response->assertInertia(
        fn(Assert $page) =>
        $page->component('Tickets/Show')
            ->where('ticket.title', 'Ticket de Teste')->etc()
    );
});

it('exibe o formulário de edição do ticket', function () {
    $ticket = Ticket::factory()->for($this->project)->for($this->user)->create();

    $response = $this->get(route('tickets.edit', $ticket));

    $response->assertStatus(200);

    $response->assertInertia(
        fn(Assert $page) =>
        $page->component('Tickets/Edit')
            ->where('ticket.id', $ticket->id)->etc()
    );
});

it('atualiza um ticket', function () {
    $ticket = Ticket::factory()->for($this->project)->for($this->user)->create();

    $data = ['title' => 'Ticket Atualizado', 'status' => 'pendente'];

    $response = $this->put(route('tickets.update', $ticket), $data);

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'title' => 'Ticket Atualizado']);
});

it('atualiza o status do ticket', function () {
    $ticket = Ticket::factory()->for($this->project)->for($this->user)->create(['status' => 'pendente']);

    $response = $this->patch(route('tickets.updateStatus', $ticket), ['status' => 'concluído']);

    $response->assertRedirect();
    $this->assertEquals('concluído', $ticket->fresh()->status);
});

it('deleta um ticket', function () {
    $ticket = Ticket::factory()->for($this->project)->for($this->user)->create();

    $response = $this->delete(route('tickets.destroy', $ticket));

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
});
