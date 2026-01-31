<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Project;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $openTickets = Ticket::where('status', 'pendente')
            ->where('user_id', $userId)
            ->count();

        $resolvedToday = Ticket::whereDate('updated_at', $today)
            ->where('status', 'concluido')
            ->where('user_id', $userId)
            ->count();

        $slaRisk = Ticket::where('status', '!=', 'concluido')
            ->where('sla_due_at', '<=', now()->addHours(2))
            ->where('user_id', $userId)
            ->count();

        $avgResolutionTimeRaw = Ticket::whereNotNull('resolved_at')
            ->where('user_id', $userId)
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_time')
            ->value('avg_time');

        $avgResolutionTime = $avgResolutionTimeRaw ? round($avgResolutionTimeRaw, 1) : 0;

        $ticketsLast7Days = Ticket::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(7))
            ->where('user_id', $userId)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $ticketsByCategory = Ticket::selectRaw('category, COUNT(*) as total')
            ->where('user_id', $userId)
            ->groupBy('category')
            ->get();

        $latestTickets = Ticket::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $criticalSlas = Ticket::where('status', '!=', 'concluido')
            ->where('sla_due_at', '<=', now()->addHours(4))
            ->where('user_id', $userId)
            ->orderBy('sla_due_at')
            ->take(5)
            ->get();

        $totalTickets = Ticket::where('user_id', $userId)->count();

        $projectsByCategory = Project::selectRaw('category, COUNT(*) as total')
            ->where('user_id', $userId)
            ->groupBy('category')
            ->get();

        return Inertia::render('Dashboard', [
            'kpis' => [
                'open_tickets' => $openTickets,
                'resolved_today' => $resolvedToday,
                'sla_risk' => $slaRisk,
                'avg_resolution_time' => $avgResolutionTime,
                'total' => $totalTickets,
            ],
            'tickets_last_7_days' => $ticketsLast7Days,
            'tickets_by_category' => $ticketsByCategory,
            'projects_by_category' => $projectsByCategory,
            'latest_tickets' => $latestTickets,
            'critical_slas' => $criticalSlas,
        ]);
    }
}
