<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProjectService
{
    public function getFilteredProjects(array $filters, User $user): LengthAwarePaginator
    {
        $query = Project::with('company')
            ->withCount('tickets')
            ->where('user_id', $user->id)
            ->when($filters['name'] ?? null, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($filters['company'] ?? null, fn($q, $v) => $q->whereHas('company', fn($qq) => $qq->where('name', 'like', "%{$v}%")))
            ->when($filters['created_from'] ?? null, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['created_to'] ?? null, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->orderBy('id', 'desc');

        $projects = $query->paginate(10)->withQueryString();

        $projectIds = $projects->pluck('id')->toArray();

        $ticketsGrouped = Ticket::whereIn('project_id', $projectIds)
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('project_id')
            ->map(fn($tickets) => $tickets->take(3));

        $ticketsAllGrouped = Ticket::whereIn('project_id', $projectIds)
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('project_id');

        $projects->getCollection()->transform(function ($project) use ($ticketsGrouped, $ticketsAllGrouped) {
            $project->setRelation('tickets', $ticketsGrouped->get($project->id) ?? collect());
            $project->setRelation('tickets_all', $ticketsAllGrouped->get($project->id) ?? collect());
            return $project;
        });

        return $projects;
    }

    public function getCompanies(): Collection
    {
        return Company::orderBy('name')->get();
    }

    public function createProject(array $data, User $user): Project
    {
        $data['user_id'] = $user->id;
        $data['company_id'] = $user->company_id;

        return Project::create($data);
    }

    public function updateProject(Project $project, array $data): bool
    {
        $data['user_id'] = $project->user_id;
        $data['company_id'] = $project->company_id;

        return $project->update($data);
    }

    public function updateStatus(Project $project, string $status): bool
    {
        $project->status = $status;
        $saved = $project->save();

        $project->refresh();

        return $saved;
    }

    public function deleteProject(Project $project): array
    {
        if ($project->tickets()->exists()) {
            return [
                'success' => false,
                'message' => 'Não é possível excluir o projeto porque ele possui tickets vinculados.',
            ];
        }

        $project->delete();

        return ['success' => true, 'message' => null];
    }
}
