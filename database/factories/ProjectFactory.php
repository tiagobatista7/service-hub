<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Company;

class ProjectFactory extends Factory
{
    protected $model = \App\Models\Project::class;

    public function definition()
    {
        $statuses = [
            'ativo',
            'concluído',
            'pendente',
            'cancelado',
        ];

        $categories = [
            'Financeiro',
            'TI',
            'Marketing',
            'Recursos Humanos',
            'Operações',
            'Vendas',
            'Desenvolvimento',
            'Suporte',
            'Comercial',
        ];

        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'name' => 'Projeto Teste',
            'status' => $this->faker->randomElement($statuses),
            'category' => $this->faker->randomElement($categories),
        ];
    }
}
