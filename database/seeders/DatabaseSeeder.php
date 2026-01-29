<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            UserProfilesSeeder::class,
            CompaniesSeeder::class,
            ProjectsSeeder::class,
            TicketsSeeder::class,
            TicketDetailsSeeder::class,
        ]);
    }
}
