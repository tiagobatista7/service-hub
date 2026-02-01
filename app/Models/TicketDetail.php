<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'technical_data',
        'details_text',
        'status',
    ];

    protected $casts = [
        'technical_data' => 'array',
        'details' => 'array',
        'details_text' => 'string',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
