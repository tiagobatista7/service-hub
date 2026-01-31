<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Company;

/**
 * @extends Factory<\App\Models\Project>
 */
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
            'user_id' => User::inRandomOrder()->value('id'),
            'company_id' => Company::inRandomOrder()->value('id'),
            'name' => $this->faker->randomElement([
                'Sistema de Gestão Financeira',
                'Plataforma de Agendamento',
                'Portal do Cliente',
                'Controle de Estoque',
                'Sistema de Suporte Técnico',
                'Aplicação de Faturamento',
                'Sistema de Gestão Comercial',
                'Plataforma de Atendimento ao Cliente',
                'Sistema de Controle de Vendas',
                'Gestão de Ordens de Serviço',
                'Plataforma de Cobrança Online',
                'Sistema de Relatórios Gerenciais',
                'Gestão de Assinaturas',
                'Plataforma de Pagamentos',
                'Sistema de Cadastro de Clientes',
                'Gestão de Projetos',
                'Sistema de Emissão de Notas Fiscais',
                'Plataforma de Help Desk',
                'Sistema de Controle Financeiro',
                'Gestão de Contratos',
                'Portal Administrativo',
            ]),
            'status' => $this->faker->randomElement($statuses),
            'category' => $this->faker->randomElement($categories),
        ];
    }
}
