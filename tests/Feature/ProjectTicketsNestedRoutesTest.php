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

it('exibe os tickets do projeto na index aninhada', function () {
    Ticket::factory()->for($this->project)->for($this->user)->create(['title' => 'Ticket de Teste']);

    $response = $this->get(route('projects.tickets.index', $this->project));

    $response->assertStatus(200);

    $response->assertInertia(
        fn(Assert $page) =>
        $page->component('Tickets/Index')
            ->has(
                'tickets.0',
                fn(Assert $ticket) =>
                $ticket->where('title', 'Ticket de Teste')->etc()
            )
            ->where('project.id', $this->project->id)
    );
});

it('exibe o formulário de criação de ticket dentro do projeto', function () {
    $response = $this->get(route('projects.tickets.create', $this->project));

    $response->assertStatus(200);

    $response->assertInertia(
        fn(Assert $page) =>
        $page->component('Tickets/Create')
            ->where('project.id', $this->project->id)
    );
});

it('cria um novo ticket dentro do projeto', function () {
    $data = [
        'title' => 'Ticket Novo',
        'description' => 'Descrição do ticket',
        'status' => 'pendente',
    ];

    $response = $this->post(route('projects.tickets.store', $this->project), $data);

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseHas('tickets', ['title' => 'Ticket Novo', 'project_id' => $this->project->id]);
});
