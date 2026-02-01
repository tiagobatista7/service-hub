<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\TicketDetail>
 */
class TicketDetailFactory extends Factory
{
    protected $model = \App\Models\TicketDetail::class;

    public function definition()
    {
        $useJson = $this->faker->boolean();

        return [
            'technical_data' => $useJson ? [
                'info' => $this->faker->randomElement([
                    'Erro retornado pela API de autenticação',
                    'Timeout ao conectar com o banco de dados',
                    'Falha ao processar requisição HTTP',
                ]),
                'logs' => now()->toDateTimeString() . ' - ' . $this->faker->randomElement([
                    'HTTP 500 Internal Server Error',
                    'SQLSTATE[42S22]: Column not found',
                    'cURL error 28: Operation timed out',
                ]),
                'ip' => $this->faker->ipv4(),
            ] : null,
            'status' => $this->faker->randomElement(['open', 'pending', 'closed']),
            'details_text' => null,
        ];
    }
}
