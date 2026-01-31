<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Company;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $password = Hash::make('password');

        $firstName = $this->faker->firstName();
        $lastName  = $this->faker->lastName();
        $domains = ['meusistema.com.br', 'appgestao.com.br', 'sistemaerp.com.br'];

        return [
            'name' => "$firstName $lastName",
            'email' => strtolower(
                Str::slug($firstName) . '.' . Str::slug($lastName) . '@' . $this->faker->randomElement($domains)
            ),
            'email_verified_at' => now(),
            'password' => $password,
            'remember_token' => Str::random(10),
            'company_id' => Company::factory(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Cria um usuário administrador fixo.
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456789'),
            'remember_token' => Str::random(10),
            'company_id' => 1,
        ]);
    }
}
