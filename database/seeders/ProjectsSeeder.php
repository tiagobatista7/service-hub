<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Faker\Factory as Faker;

class ProjectsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR');

        $firstUser = User::first();

        if (!$firstUser) {
            $this->command->error('Nenhum usuário encontrado. Crie pelo menos um usuário antes de executar este seeder.');
            return;
        }

        Company::all()->each(function ($company) use ($faker, $firstUser) {
            Project::factory(3)->create([
                'company_id' => $company->id,
                'user_id' => $firstUser->id,
            ]);
        });
    }
}
