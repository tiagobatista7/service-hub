<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Project;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function __construct(protected TicketService $ticketService) {}

    public function index()
    {
        $tickets = Ticket::with('project')->get()->map(fn($ticket) => [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => $ticket->status,
            'project' => [
                'id' => $ticket->project->id,
                'name' => $ticket->project->name,
            ],
        ]);

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function indexByProject(Project $project)
    {
        $tickets = $project->tickets()->with('user')->get()->map(fn($ticket) => [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => $ticket->status,
            'user' => [
                'id' => $ticket->user->id,
                'name' => $ticket->user->name,
            ],
        ]);

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Tickets/Create');
    }

    public function createInProject(Project $project)
    {
        return Inertia::render('Tickets/Create', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
        ]);
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('user', 'project');

        return Inertia::render('Tickets/Show', [
            'ticket' => [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'description' => $ticket->description,
                'status' => $ticket->status,
                'user' => $ticket->user ? [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name,
                ] : null,
                'project' => [
                    'id' => $ticket->project->id,
                    'name' => $ticket->project->name,
                ],
            ],
        ]);
    }

    public function edit(Ticket $ticket)
    {
        $ticket->load('user', 'project');

        return Inertia::render('Tickets/Edit', [
            'ticket' => [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'description' => $ticket->description,
                'status' => $ticket->status,
                'user' => $ticket->user ? [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name,
                ] : null,
                'project' => [
                    'id' => $ticket->project->id,
                    'name' => $ticket->project->name,
                ],
            ],
        ]);
    }

    public function store(StoreTicketRequest $request)
    {
        $project = Project::findOrFail($request->input('project_id'));

        $this->ticketService->createTicket(
            $request->validated(),
            $project,
            Auth::id(),
            $request->file('attachment')
        );

        return redirect()->route('projects.index')
            ->with('success', 'Ticket criado com sucesso!');
    }

    public function storeInProject(StoreTicketRequest $request, Project $project)
    {
        $this->ticketService->createTicket(
            $request->validated(),
            $project,
            Auth::id(),
            $request->file('attachment')
        );

        return redirect()->route('projects.index')
            ->with('success', 'Ticket criado com sucesso!');
    }

    public function update(StoreTicketRequest $request, Ticket $ticket)
    {
        $validated = $request->validated();

        if (isset($validated['technical_data']) && is_array($validated['technical_data'])) {
            $ticket->technical_data = $validated['technical_data'];
        }

        $ticket->fill($validated);
        $ticket->save();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Ticket atualizado com sucesso!');
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket)
    {
        $this->ticketService->updateStatus($ticket, $request->status);

        return redirect()->back()
            ->with('success', 'Status do ticket atualizado com sucesso!');
    }

    public function destroy(Ticket $ticket)
    {
        $this->ticketService->deleteTicket($ticket);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Ticket excluído com sucesso!');
    }
}
