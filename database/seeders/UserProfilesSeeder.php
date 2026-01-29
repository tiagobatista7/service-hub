<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;

class UserProfilesSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function ($user) {
            UserProfile::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
