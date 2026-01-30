<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->company) {
            abort(403, 'Usuário não tem empresa associada.');
        }

        $projects = auth()->user()
            ->company
            ->projects()
            ->select('id', 'name')
            ->get();

        return Inertia::render('Dashboard', [
            'projects' => $projects
        ]);
    }
}
