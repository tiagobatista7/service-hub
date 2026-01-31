<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Project;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function __construct(protected TicketService $ticketService)
    {
        //
    }

    public function create(Project $project)
    {
        return Inertia::render('Tickets/Create', compact('project'));
    }

    public function show(int $ticketId)
    {
        $ticket = $this->ticketService->findTicketOrFail($ticketId);

        return Inertia::render('Tickets/Show', compact('ticket'));
    }

    public function store(StoreTicketRequest $request, Project $project)
    {
        $data = $request->validated();
        $attachment = $request->file('attachment');

        $this->ticketService->createTicket($data, $project, Auth::id(), $attachment);

        return redirect()->route('projects.index')
            ->with('success', 'Ticket criado com sucesso!');
    }

    public function update(StoreTicketRequest $request, Ticket $ticket)
    {
        $data = $request->validated();

        $this->ticketService->updateTicket($ticket, $data);

        return redirect()->route('projects.index')
            ->with('success', 'Ticket atualizado com sucesso!');
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket)
    {
        $this->ticketService->updateStatus($ticket, $request->status);

        return redirect()->back()
            ->with('success', 'Status do ticket atualizado com sucesso!');
    }

    public function destroy(int $ticketId)
    {
        $ticket = $this->ticketService->findTicketOrFail($ticketId);

        $this->ticketService->deleteTicket($ticket);

        return redirect()->route('projects.index')
            ->with('success', 'Ticket excluído com sucesso!');
    }

    public function allTickets(Request $request, int $projectId)
    {
        $tickets = $this->ticketService->getTicketsByProject($projectId, $request->search);

        return response()->json($tickets);
    }
}
