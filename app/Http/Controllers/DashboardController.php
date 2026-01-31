<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $now = now();

        $baseQuery = Ticket::where('user_id', $userId);
        $openTickets = (clone $baseQuery)->where('status', 'pendente')->count();

        $resolvedToday = (clone $baseQuery)
            ->whereDate('updated_at', $now)
            ->where('status', 'concluido')
            ->count();

        $slaRisk = (clone $baseQuery)
            ->where('status', '!=', 'concluido')
            ->where('sla_due_at', '<=', $now->copy()->addHours(2))
            ->count();

        $avgResolutionTime = round(
            (float) (clone $baseQuery)
                ->whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_time')
                ->value('avg_time') ?? 0,
            1
        );

        $ticketsLast7Days = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', $now->copy()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $ticketsByCategory = (clone $baseQuery)
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->get();

        $latestTickets = (clone $baseQuery)
            ->latest()
            ->limit(5)
            ->get();

        $criticalSlas = (clone $baseQuery)
            ->where('status', '!=', 'concluido')
            ->where('sla_due_at', '<=', $now->copy()->addHours(4))
            ->orderBy('sla_due_at')
            ->limit(5)
            ->get();

        $totalTickets = (clone $baseQuery)->count();

        $projectsByCategory = Project::where('user_id', $userId)
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->get();

        return Inertia::render('Dashboard', [
            'kpis' => [
                'open_tickets'        => $openTickets,
                'resolved_today'      => $resolvedToday,
                'sla_risk'            => $slaRisk,
                'avg_resolution_time' => $avgResolutionTime,
                'total'               => $totalTickets,
            ],
            'tickets_last_7_days'  => $ticketsLast7Days,
            'tickets_by_category'  => $ticketsByCategory,
            'projects_by_category' => $projectsByCategory,
            'latest_tickets'       => $latestTickets,
            'critical_slas'        => $criticalSlas,
        ]);
    }
}
