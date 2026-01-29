<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TicketController;

Route::get('/projects/{project}/tickets/create', [TicketController::class, 'create'])
    ->name('tickets.create');

Route::post('/projects/{project}/tickets', [TicketController::class, 'store'])
    ->name('tickets.store');
