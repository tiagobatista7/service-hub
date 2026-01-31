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
        $statuses = ['pendente', 'ativo', 'concluído', 'cancelado'];

        $sla_due_at = null;

        if ($this->faker->boolean(80)) {
            $scenario = $this->faker->randomElement(['expired', 'near', 'normal']);

            switch ($scenario) {
                case 'expired':
                    $sla_due_at = $this->faker->dateTimeBetween('-3 days', '-1 days');
                    break;
                case 'near':
                    $sla_due_at = $this->faker->dateTimeBetween('now', '+2 days');
                    break;
                default:
                    $sla_due_at = $this->faker->dateTimeBetween('+3 days', '+7 days');
                    break;
            }
        }

        return [
            'title' => $this->faker->randomElement([
                'Erro ao acessar o sistema',
                'Falha no login do usuário',
                'Sistema lento na tela de relatórios',
                'Não consigo gerar boleto',
                'Problema ao atualizar cadastro',
                'Tela em branco ao abrir pedido',
                'Erro ao salvar dados do cliente',
                'Falha na autenticação via API',
                'Notificação por email não enviada',
                'Relatório financeiro com dados incorretos',
                'Página de cadastro apresenta validação errada',
                'Botão de enviar pedido inativo',
                'Problema na exportação de arquivos CSV',
                'Erro de timeout na consulta ao servidor',
                'Sistema não reconhece permissão de usuário',
                'Atualização automática não está funcionando',
            ]),

            'description' => $this->faker->randomElement([
                'O usuário relata que ao tentar acessar o sistema, recebe uma mensagem de erro inesperada.',
                'O sistema está demorando mais de 30 segundos para carregar a tela.',
                'Após a última atualização, não é mais possível concluir a operação.',
                'O problema ocorre apenas para usuários com perfil administrativo.',
                'O erro começou a acontecer após a integração com o gateway de pagamento.',
                'Ao tentar salvar informações do cliente, o sistema retorna um erro de banco de dados.',
                'A autenticação via API externa está falhando para usuários específicos.',
                'Emails de notificação não estão sendo disparados após criação de tickets.',
                'Os valores exibidos no relatório financeiro não batem com os dados reais.',
                'Os campos obrigatórios do formulário de cadastro não estão sendo validados corretamente.',
                'O botão para enviar pedido fica desabilitado sem motivo aparente.',
                'A exportação dos dados em CSV gera arquivos corrompidos ou vazios.',
                'Consultas ao servidor retornam timeout quando há grande volume de dados.',
                'Usuários com perfil específico não conseguem acessar determinadas funcionalidades.',
                'A função de atualização automática do sistema não realiza nenhuma ação.',
            ]),

            'status' => $this->faker->randomElement($statuses),
            'sla_due_at' => $sla_due_at,
        ];
    }
}
