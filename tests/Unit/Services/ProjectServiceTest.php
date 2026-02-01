<?php

namespace Tests\Unit\Services;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProjectService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProjectService();
        $this->user = User::factory()->create();
    }

    public function test_it_returns_paginated_filtered_projects()
    {
        $company = Company::factory()->create(['name' => 'Example Corp']);
        Project::factory()->for($this->user)->create([
            'name' => 'Test Project',
            'company_id' => $company->id
        ]);

        $filters = ['name' => 'Test', 'company' => 'Example', 'created_from' => null, 'created_to' => null];

        $projects = $this->service->getFilteredProjects($filters, $this->user);

        $this->assertTrue($projects->total() > 0);
        $this->assertEquals('Test Project', $projects->first()->name);
        $this->assertEquals('Example Corp', $projects->first()->company->name);
    }

    public function test_it_creates_project()
    {
        $data = ['name' => 'New Project', 'status' => 'ativo'];
        $project = $this->service->createProject($data, $this->user);

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'New Project']);
        $this->assertEquals($this->user->id, $project->user_id);
    }

    public function test_it_updates_project()
    {
        $project = Project::factory()->for($this->user)->create(['name' => 'Old Name']);
        $data = ['name' => 'Updated Name', 'status' => 'ativo'];

        $result = $this->service->updateProject($project, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated Name']);
    }

    public function test_it_updates_project_status()
    {
        $project = Project::factory()->for($this->user)->create(['status' => 'pendente']);
        $result = $this->service->updateStatus($project, 'concluído');

        $this->assertTrue($result);
        $this->assertEquals('concluído', $project->fresh()->status);
    }

    public function test_it_prevents_deleting_project_with_tickets()
    {
        $project = Project::factory()->for($this->user)->create();
        $project->tickets()->create([
            'title' => 'Ticket 1',
            'user_id' => $this->user->id,
        ]);

        $response = $this->service->deleteProject($project);

        $this->assertFalse($response['success']);
        $this->assertStringContainsString('possui tickets vinculados', $response['message']);
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    public function test_it_deletes_project_without_tickets()
    {
        $project = Project::factory()->for($this->user)->create();

        $response = $this->service->deleteProject($project);

        $this->assertTrue($response['success']);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
