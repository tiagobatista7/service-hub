<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Project::with([
            'tickets_all' => fn($q) => $q->orderBy('id', 'desc'),
            'company',
        ])->withCount('tickets')
            ->where('user_id', $userId);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('company')) {
            $query->whereHas('company', fn($q) => $q->where('name', 'like', '%' . $request->company . '%'));
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }

        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        $projects = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $projectIds = $projects->pluck('id');

        $tickets = Ticket::whereIn('project_id', $projectIds)
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('project_id')
            ->map(fn($tickets) => $tickets->take(3));

        $projects->getCollection()->transform(fn($project) => $project->setRelation('tickets', $tickets->get($project->id) ?? collect()));

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['name', 'company', 'created_from', 'created_to']),
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
                'warning' => session('warning'),
                'info' => session('info'),
            ],
        ]);
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();

        return Inertia::render('Projects/Create', ['companies' => $companies]);
    }

    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['company_id'] = $user->company_id;

        Project::create($data);

        return redirect()->route('projects.index')
            ->with('success', 'Projeto criado com sucesso!');
    }

    public function edit(Project $project)
    {
        $this->authorizeUser($project);

        $companies = Company::orderBy('name')->get();

        return Inertia::render('Projects/Edit', [
            'project' => $project,
            'companies' => $companies,
        ]);
    }

    public function update(StoreProjectRequest $request, Project $project)
    {
        $this->authorizeUser($project);
        $data = $request->validated();
        $data['user_id'] = $project->user_id;

        $project->update($data);

        return redirect()->route('projects.index')
            ->with('success', 'Projeto atualizado com sucesso!');
    }

    public function destroy(Project $project)
    {
        $this->authorizeUser($project);

        if ($project->tickets()->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'Não é possível excluir o projeto porque ele possui tickets vinculados.');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projeto deletado com sucesso!');
    }

    private function authorizeUser(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Ação não autorizada.');
        }
    }
}
