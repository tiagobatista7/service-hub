<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

class UsersSeeder extends Seeder
{
    public function run()
    {
        Company::firstOrCreate(
            ['id' => 1],
            ['name' => 'Empresa Principal']
        );

        User::factory()->admin()->create();

        User::factory(10)->create();
    }
}
