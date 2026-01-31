<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketDetail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketDetailService
{
    /**
     * Cria os detalhes de um ticket.
     *
     * @param Ticket $ticket
     * @param array $data ['technical_data' => string|null, 'status' => string]
     * @return TicketDetail
     */
    public function createDetail(Ticket $ticket, array $data): TicketDetail
    {
        return $ticket->details()->create([
            'technical_data' => $data['technical_data'] ?? null,
            'status' => $data['status'],
        ]);
    }

    /**
     * Atualiza os detalhes de um ticket.
     *
     * @param TicketDetail $ticketDetail
     * @param array $data ['technical_data' => string|null, 'status' => string]
     * @return bool
     */
    public function updateDetail(TicketDetail $ticketDetail, array $data): bool
    {
        return $ticketDetail->update([
            'technical_data' => $data['technical_data'] ?? null,
            'status' => $data['status'],
        ]);
    }

    /**
     * Atualiza somente o status do detalhe do ticket.
     *
     * @param TicketDetail $ticketDetail
     * @param string $status
     * @return bool
     */
    public function updateStatus(TicketDetail $ticketDetail, string $status): bool
    {
        $ticketDetail->status = $status;
        return $ticketDetail->save();
    }

    /**
     * Deleta um detalhe do ticket pelo ID.
     *
     * @param int $ticketDetailId
     * @return int Ticket ID do detalhe excluído
     */
    public function deleteDetail(int $ticketDetailId): int
    {
        $ticketDetail = TicketDetail::findOrFail($ticketDetailId);
        $ticketId = $ticketDetail->ticket_id;

        $ticketDetail->delete();

        return $ticketId;
    }

    /**
     * Retorna os detalhes de um ticket com filtro opcional e paginação.
     *
     * @param int $ticketId
     * @param string|null $search Texto para busca no status dos detalhes
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getDetailsByTicket(int $ticketId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = TicketDetail::where('ticket_id', $ticketId);

        if ($search) {
            $query->where('status', 'like', '%' . $search . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Busca um detalhe do ticket pelo ID ou lança exceção.
     *
     * @param int $ticketDetailId
     * @return TicketDetail
     */
    public function findDetailOrFail(int $ticketDetailId): TicketDetail
    {
        return TicketDetail::findOrFail($ticketDetailId);
    }
}
