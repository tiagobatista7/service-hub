<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Project;

class ProjectsSeeder extends Seeder
{
    public function run()
    {
        Company::all()->each(function ($company) {
            Project::factory(3)->create([
                'company_id' => $company->id,
            ]);
        });
    }
}
