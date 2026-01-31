<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;

class TicketsSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        Project::all()->each(function ($project) use ($users) {
            Ticket::factory(5)->create([
                'project_id' => $project->id,
                'user_id' => $users->random()->id,
            ]);
        });
    }
}
