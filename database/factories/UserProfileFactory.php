<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    protected $model = \App\Models\UserProfile::class;

    public function definition()
    {
        $roles = ['Member', 'Admin', 'Moderator'];

        return [           
            'phone' => $this->faker->phoneNumber(),
            'role' => $this->faker->randomElement($roles),
        ];
    }
}
