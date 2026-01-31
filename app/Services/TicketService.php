<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Jobs\ProcessTicketJob;

class TicketService
{
    /**
     * Cria um ticket para um projeto, com anexo opcional.
     *
     * @param array $data Dados do ticket (title, description)
     * @param Project $project Projeto ao qual o ticket pertence
     * @param int $userId ID do usuário criador do ticket
     * @param UploadedFile|null $attachment Arquivo de anexo opcional
     * @return Ticket O ticket criado
     */
    public function createTicket(array $data, Project $project, int $userId, ?UploadedFile $attachment = null): Ticket
    {
        $attachmentPath = $attachment?->store('tickets');

        $ticket = $project->tickets()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'attachment_path' => $attachmentPath,
            'user_id' => $userId,
        ]);

        if ($attachmentPath) {
            ProcessTicketJob::dispatch($ticket);
        }

        $ticket->project->recalculateStatus();

        return $ticket;
    }

    /**
     * Atualiza os dados de um ticket.
     *
     * @param Ticket $ticket Ticket a ser atualizado
     * @param array $data Dados atualizados do ticket (title, description, attachment opcional)
     * @param UploadedFile|null $attachment Arquivo de anexo opcional para atualizar
     * @return bool True se a atualização foi bem sucedida, caso contrario "false"
     */
    public function updateTicket(Ticket $ticket, array $data, ?UploadedFile $attachment = null): bool
    {
        if ($attachment) {
            $data['attachment_path'] = $attachment->store('tickets');
        }

        $updated = $ticket->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'attachment_path' => $data['attachment_path'] ?? $ticket->attachment_path,
        ]);

        if ($updated && !empty($ticket->attachment_path)) {
            ProcessTicketJob::dispatch($ticket);
        }

        $ticket->project->recalculateStatus();

        return $updated;
    }

    /**
     * Atualiza o status de um ticket.
     *
     * @param Ticket $ticket Ticket a ser atualizado
     * @param string $status Novo status do ticket
     * @return bool True se a atualização foi bem sucedida, false caso contrário
     */
    public function updateStatus(Ticket $ticket, string $status): bool
    {
        $ticket->status = $status;
        $saved = $ticket->save();

        $ticket->project->recalculateStatus();

        return $saved;
    }

    /**
     * Remove um ticket e recalcula o status do projeto associado.
     *
     * @param Ticket $ticket Ticket a ser deletado
     * @return void
     */
    public function deleteTicket(Ticket $ticket): void
    {
        $project = $ticket->project;
        $ticket->delete();
        $project->recalculateStatus();
    }

    /**
     * Retorna tickets de um projeto com filtro opcional de busca e paginação.
     *
     * @param int $projectId ID do projeto
     * @param string|null $search Texto para busca no título dos tickets
     * @param int $perPage Quantidade de tickets por página (padrão 20)
     * @return LengthAwarePaginator Paginação dos tickets encontrados
     */
    public function getTicketsByProject(int $projectId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = Ticket::where('project_id', $projectId);

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Busca um ticket pelo ID ou lança exceção se não encontrado.
     *
     * @param int $ticketId ID do ticket
     * @return Ticket O ticket encontrado
     */
    public function findTicketOrFail(int $ticketId): Ticket
    {
        return Ticket::findOrFail($ticketId);
    }
}
