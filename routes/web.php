<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketDetailController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // PROJETOS
    Route::resource('projects', ProjectController::class);

    // TICKETS
    Route::get('/tickets', [TicketController::class, 'index'])
        ->name('tickets.index');

    Route::get('/tickets/create', [TicketController::class, 'create'])
        ->name('tickets.create');

    Route::post('/tickets', [TicketController::class, 'store'])
        ->name('tickets.store');

    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->name('tickets.show');

    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])
        ->name('tickets.edit');

    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])
        ->name('tickets.update');

    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])
        ->name('tickets.destroy');

    // TICKETS DENTRO DO PROJETO
    Route::prefix('projects/{project}')->group(function () {
        Route::get('/tickets', [TicketController::class, 'indexByProject'])
            ->name('projects.tickets.index');

        Route::get('/tickets/create', [TicketController::class, 'createInProject'])
            ->name('projects.tickets.create');

        Route::post('/tickets', [TicketController::class, 'storeInProject'])
            ->name('projects.tickets.store');
    });

    // PERFIL
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// TICKET DETAILS
Route::prefix('tickets/{ticket}')->group(function () {
    Route::get('/details', [TicketDetailController::class, 'allDetails'])
        ->name('tickets.details.index');

    Route::get('/details/create', [TicketDetailController::class, 'create'])
        ->name('tickets.details.create');

    Route::post('/details', [TicketDetailController::class, 'store'])
        ->name('tickets.details.store');
});

Route::get('/ticket-details/{ticketDetail}', [TicketDetailController::class, 'show'])
    ->name('ticket-details.show');

Route::get('/ticket-details/{ticketDetail}/edit', [TicketDetailController::class, 'edit'])
    ->name('ticket-details.edit');

Route::put('/ticket-details/{ticketDetail}', [TicketDetailController::class, 'update'])
    ->name('ticket-details.update');

Route::patch('/ticket-details/{ticketDetail}/status', [TicketDetailController::class, 'updateStatus'])
    ->name('ticket-details.updateStatus');

Route::delete('/ticket-details/{ticketDetail}', [TicketDetailController::class, 'destroy'])
    ->name('ticket-details.destroy');

// STATUS
Route::patch('/projects/{project}/status', [ProjectController::class, 'updateStatus'])
    ->name('projects.updateStatus');

Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
    ->name('tickets.updateStatus');

require __DIR__ . '/auth.php';
