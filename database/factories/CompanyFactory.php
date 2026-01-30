<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = \App\Models\Company::class;

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement([
                'Conecta Sistemas',
                'Gestão Pro',
                'Brasil Tech',
                'Cloud ERP',
                'Integra Solutions',
                'Smart Sistemas',
            ]) . ' ' . $this->faker->randomElement([
                'LTDA',
                'S/A',
            ]),
        ];
    }
}
