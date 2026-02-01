<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Jobs\ProcessTicketJob;

class TicketService
{
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

    public function updateStatus(Ticket $ticket, string $status): bool
    {
        $saved = $ticket->update([
            'status' => $status,
        ]);

        $ticket->project->recalculateStatus();

        return $saved;
    }

    public function deleteTicket(Ticket $ticket): void
    {
        $project = $ticket->project;

        $ticket->delete();

        $project->recalculateStatus();
    }

    public function getTicketsByProject(int $projectId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return Ticket::where('project_id', $projectId)
            ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function findTicketOrFail(int $ticketId): Ticket
    {
        return Ticket::findOrFail($ticketId);
    }
}
