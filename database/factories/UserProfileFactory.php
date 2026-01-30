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
        $roles = ['Membro', 'Administrador', 'Moderador'];

        return [
            'phone' => $this->faker->numerify('(##) 9####-####'),
            'role' => $this->faker->randomElement($roles),
        ];
    }
}
