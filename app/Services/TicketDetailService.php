<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketDetail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketDetailService
{
    public function createDetail(Ticket $ticket, array $data): TicketDetail
    {
        return $ticket->ticketDetails()->create([
            'technical_data' => $data['technical_data'] ?? null,
            'details_text' => $data['details_text'] ?? null,
            'status' => $data['status'] ?? 'pendente',
        ]);
    }

    public function updateDetail(TicketDetail $ticketDetail, array $data): bool
    {
        return $ticketDetail->update([
            'technical_data' => $data['technical_data'] ?? $ticketDetail->technical_data ?? [],
            'details_text' => $data['details_text'] ?? $ticketDetail->details_text,
            'status' => $data['status'] ?? $ticketDetail->status,
        ]);
    }

    public function updateStatus(TicketDetail $ticketDetail, string $status): bool
    {
        return $ticketDetail->update([
            'status' => $status,
        ]);
    }

    public function deleteDetail(int $ticketDetailId): int
    {
        $ticketDetail = TicketDetail::findOrFail($ticketDetailId);
        $ticketId = $ticketDetail->ticket_id;

        $ticketDetail->delete();

        return $ticketId;
    }

    public function getDetailsByTicket(int $ticketId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return TicketDetail::where('ticket_id', $ticketId)
            ->when($search, fn($q) => $q->where('status', 'like', "%{$search}%"))
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function findDetailOrFail(int $ticketDetailId): TicketDetail
    {
        return TicketDetail::findOrFail($ticketDetailId);
    }
}
