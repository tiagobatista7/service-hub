<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id',
        'user_id',
        'category',
        'is_completed',
        'status',  
    ];
   
    protected $casts = [
        'status' => 'string',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class)->orderBy('id', 'desc');
    }

    public function ticketsLimited()
    {
        return $this->hasMany(Ticket::class)->orderBy('id', 'desc')->limit(3);
    }

    public function lastTickets()
    {
        return $this->hasMany(Ticket::class)
            ->orderBy('id', 'desc')
            ->limit(3);
    }

    public function recalculateStatus()
    {
        $tickets = $this->tickets;

        if ($tickets->isEmpty()) {
            $this->status = 'pendente';
            $this->save();
            return;
        }

        $allConcluded = $tickets->every(fn($t) => $t->status === 'concluído');
        $allCanceled  = $tickets->every(fn($t) => $t->status === 'cancelado');

        if ($allConcluded) {
            $this->status = 'concluído';
        } elseif ($allCanceled) {
            $this->status = 'cancelado';
        } else {
            $this->status = 'pendente';
        }

        $this->save();
    }
}
