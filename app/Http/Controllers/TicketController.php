<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function create(Project $project)
    {
        return Inertia::render('Tickets/Create', [
            'project' => $project,
        ]);
    }

    public function show($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
        ]);
    }

    public function store(StoreTicketRequest $request, Project $project)
    {
        $data = $request->validated();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets');
        }

        $ticket = $project->tickets()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'attachment_path' => $attachmentPath,
            'user_id' => Auth::id(),
        ]);

        $ticket->project->recalculateStatus();

        return redirect()->route('projects.index')
            ->with('success', 'Ticket criado com sucesso!');
    }

    public function update(StoreTicketRequest $request, Ticket $ticket)
    {
        $data = $request->validated();

        $ticket->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        $ticket->project->recalculateStatus();

        return redirect()->route('projects.index')
            ->with('success', 'Ticket atualizado com sucesso!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $ticket->status = $request->status;
        $ticket->save();

        $ticket->project->recalculateStatus();

        return redirect()->back()
            ->with('success', 'Status do ticket atualizado com sucesso!');
    }

    public function destroy($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);

        $ticket->delete();

        $ticket->project->recalculateStatus();

        return redirect()->route('projects.index')
            ->with('success', 'Ticket excluído com sucesso!');
    }

    public function allTickets(Request $request, $projectId)
    {
        $query = Ticket::where('project_id', $projectId);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $tickets = $query->orderBy('id', 'desc')->paginate(20);

        return response()->json($tickets);
    }
}
