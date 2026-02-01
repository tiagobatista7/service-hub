<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('retorna status 200 na rota projects.index', function () {
    $response = $this->get(route('projects.index'));
    $response->assertStatus(200);
});

it('retorna status 200 na rota projects.create', function () {
    $response = $this->get(route('projects.create'));
    $response->assertStatus(200);
});

it('cria um projeto pela rota projects.store', function () {
    $data = ['name' => 'Projeto Teste', 'status' => 'ativo'];

    $response = $this->post(route('projects.store'), $data);

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseHas('projects', ['name' => 'Projeto Teste']);
});

it('retorna status 200 na rota projects.show', function () {
    $project = Project::factory()->for($this->user)->create();

    $response = $this->get(route('projects.show', $project));

    $response->assertStatus(200);
});

it('atualiza projeto pela rota projects.update', function () {
    $project = Project::factory()->for($this->user)->create(['name' => 'Old Name']);
    $data = ['name' => 'Nome Atualizado', 'status' => 'concluído'];

    $response = $this->put(route('projects.update', $project), $data);

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Nome Atualizado']);
});

it('deleta projeto pela rota projects.destroy', function () {
    $project = Project::factory()->for($this->user)->create();

    $response = $this->delete(route('projects.destroy', $project));

    $response->assertRedirect(route('projects.index'));
    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

it('atualiza o status do projeto pela rota projects.updateStatus', function () {
    $project = Project::factory()->for($this->user)->create(['status' => 'ativo']);

    $response = $this->patch(route('projects.updateStatus', $project), ['status' => 'concluído']);

    $response->assertRedirect();
    expect($project->fresh()->status)->toBe('concluído');
});
