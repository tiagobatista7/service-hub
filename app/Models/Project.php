<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id',
        'user_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class)->orderBy('id', 'desc')->limit(3);
    }

    public function tickets_all()
    {
        return $this->hasMany(Ticket::class)->orderBy('id', 'desc');
    }

    public function tickets_limited()
    {
        return $this->hasMany(Ticket::class)->orderBy('id', 'desc')->limit(3);
    }
}
