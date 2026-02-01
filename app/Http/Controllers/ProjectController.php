<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function __construct(protected ProjectService $projectService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'company', 'created_from', 'created_to']);

        return Inertia::render('Projects/Index', [
            'projects' => $this->projectService->getFilteredProjects($filters, Auth::user()),
            'filters' => $filters,
            'flash' => session()->only(['success', 'error', 'warning', 'info']),
        ]);
    }

    public function show(Project $project)
    {
        $project->load(['tickets.user']);

        $tickets = $project->tickets->map(fn($ticket) => [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => $ticket->status,
            'user' => $ticket->user ? [
                'id' => $ticket->user->id,
                'name' => $ticket->user->name,
            ] : null,
        ]);

        return Inertia::render('Projects/Show', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'tickets' => $tickets,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Projects/Create', [
            'companies' => $this->projectService->getCompanies(),
        ]);
    }

    public function store(StoreProjectRequest $request)
    {
        $this->projectService->createProject($request->validated(), Auth::user());

        return redirect()->route('projects.index')->with('success', 'Projeto criado com sucesso!');
    }

    public function edit(Project $project)
    {
        $this->authorizeUser($project);

        return Inertia::render('Projects/Edit', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'company_id' => $project->company_id,
                'status' => $project->status,
                'category' => $project->category,
                'attachment' => $project->attachment,
            ],
            'companies' => $this->projectService->getCompanies(),
        ]);
    }

    public function update(StoreProjectRequest $request, Project $project)
    {
        $this->authorizeUser($project);

        $this->projectService->updateProject($project, $request->validated());

        return redirect()->route('projects.index')->with('success', 'Projeto atualizado com sucesso!');
    }

    public function updateStatus(Request $request, Project $project)
    {
        $this->authorizeUser($project);

        $validatedData = $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $this->projectService->updateStatus($project, $validatedData['status']);

        return redirect()->back()->with('success', 'Status do projeto atualizado com sucesso!');
    }

    public function destroy(Project $project)
    {
        $this->authorizeUser($project);

        $result = $this->projectService->deleteProject($project);

        return redirect()->route('projects.index')
            ->with($result['success'] ? 'success' : 'error', $result['success']
                ? 'Projeto deletado com sucesso!'
                : $result['message']);
    }

    private function authorizeUser(Project $project): void
    {
        abort_if($project->user_id !== Auth::id(), 403);
    }
}
