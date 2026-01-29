<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Project;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function create(Project $project)
    {
        return Inertia::render('Tickets/Create', [
            'project' => $project,
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
        ]);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Ticket criado com sucesso!');
    }
}
