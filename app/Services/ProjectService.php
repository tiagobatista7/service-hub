<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProjectService
{
    /**
     * Retorna projetos filtrados com paginação, trazendo os últimos 3 tickets relacionados em cada projeto.
     *
     * @param array $filters ['name' => string, 'company' => string, 'created_from' => date, 'created_to' => date]
     * @param int $userId
     * @return LengthAwarePaginator
     */
    public function getFilteredProjects(array $filters, int $userId): LengthAwarePaginator
    {
        $query = Project::with([
            'tickets_all' => fn($q) => $q->orderBy('id', 'desc'),
            'company',
        ])->withCount('tickets')
            ->where('user_id', $userId);

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['company'])) {
            $query->whereHas('company', fn($q) => $q->where('name', 'like', '%' . $filters['company'] . '%'));
        }

        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        $projects = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $projectIds = $projects->pluck('id')->toArray();

        $tickets = Ticket::whereIn('project_id', $projectIds)
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('project_id')
            ->map(fn($tickets) => $tickets->take(3));

        $projects->getCollection()->transform(function ($project) use ($tickets) {
            $project->setRelation('tickets', $tickets->get($project->id) ?? collect());
            return $project;
        });

        return $projects;
    }

    /**
     * Retorna todas as empresas ordenadas por nome.
     *
     * @return Collection
     */
    public function getCompanies(): Collection
    {
        return Company::orderBy('name')->get();
    }

    /**
     * Cria um projeto vinculado ao usuário e empresa do usuário.
     *
     * @param array $data
     * @param \App\Models\User $user
     * @return Project
     */
    public function createProject(array $data, $user): Project
    {
        $data['user_id'] = $user->id;
        $data['company_id'] = $user->company_id;

        return Project::create($data);
    }

    /**
     * Atualiza o projeto mantendo user_id e company_id originais.
     *
     * @param Project $project
     * @param array $data
     * @return bool
     */
    public function updateProject(Project $project, array $data): bool
    {
        $data['user_id'] = $project->user_id;
        $data['company_id'] = $project->company_id;

        return $project->update($data);
    }

    /**
     * Atualiza somente o status do projeto.
     *
     * @param Project $project
     * @param string $status
     * @return bool
     */
    public function updateStatus(Project $project, string $status): bool
    {
        $project->status = $status;
        return $project->save();
    }

    /**
     * Deleta projeto se não possuir tickets vinculados.
     *
     * @param Project $project
     * @return array ['success' => bool, 'message' => string|null]
     */
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
