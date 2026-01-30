<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = \App\Models\Ticket::class;

    public function definition()
    {
        return [
            'title' => $this->faker->randomElement([
                'Erro ao acessar o sistema',
                'Falha no login do usuário',
                'Sistema lento na tela de relatórios',
                'Não consigo gerar boleto',
                'Problema ao atualizar cadastro',
                'Tela em branco ao abrir pedido',
            ]),
            'description' => $this->faker->randomElement([
                'O usuário relata que ao tentar acessar o sistema, recebe uma mensagem de erro inesperada.',
                'O sistema está demorando mais de 30 segundos para carregar a tela.',
                'Após a última atualização, não é mais possível concluir a operação.',
                'O problema ocorre apenas para usuários com perfil administrativo.',
                'O erro começou a acontecer após a integração com o gateway de pagamento.',
            ]),
        ];
    }
}
