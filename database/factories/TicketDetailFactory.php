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
        $statuses = ['pending', 'in_progress', 'completed'];

        return [
            'technical_data' => [
                'info' => $this->faker->sentence(),
                'logs' => $this->faker->text(200),
            ],
            'status' => $this->faker->randomElement($statuses),
        ];
    }
}
