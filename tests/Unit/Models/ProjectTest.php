<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_belongs_to_user_and_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $project = Project::factory()->for($user)->for($company)->create();

        $this->assertEquals($user->id, $project->user->id);
        $this->assertEquals($company->id, $project->company->id);
    }

    public function test_project_has_many_tickets()
    {
        $project = Project::factory()->create();
        $ticket = Ticket::factory()->for($project)->create();

        $this->assertTrue($project->tickets->contains($ticket));
    }
}
