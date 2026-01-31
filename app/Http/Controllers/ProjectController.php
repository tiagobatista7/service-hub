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
    public function __construct(protected ProjectService $projectService)
    {
        //
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'company', 'created_from', 'created_to']);
        $projects = $this->projectService->getFilteredProjects($filters, Auth::id());

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $filters,
            'flash' => session()->only(['success', 'error', 'warning', 'info']),
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

        return redirect()->route('projects.index')
            ->with('success', 'Projeto criado com sucesso!');
    }

    public function edit(Project $project)
    {
        $this->authorizeUser($project);

        return Inertia::render('Projects/Edit', [
            'project' => $project,
            'companies' => $this->projectService->getCompanies(),
        ]);
    }

    public function update(StoreProjectRequest $request, Project $project)
    {
        $this->authorizeUser($project);

        $this->projectService->updateProject($project, $request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Projeto atualizado com sucesso!');
    }

    public function updateStatus(Request $request, Project $project)
    {
        $request->validate(['status' => 'required|string|max:50']);
        $this->authorizeUser($project);

        $this->projectService->updateStatus($project, $request->status);

        return redirect()->back()
            ->with('success', 'Status do projeto atualizado com sucesso!');
    }

    public function destroy(Project $project)
    {
        $this->authorizeUser($project);

        $result = $this->projectService->deleteProject($project);

        return $result['success']
            ? redirect()->route('projects.index')->with('success', 'Projeto deletado com sucesso!')
            : redirect()->route('projects.index')->with('error', $result['message']);
    }

    private function authorizeUser(Project $project): void
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Ação não autorizada.');
        }
    }
}
