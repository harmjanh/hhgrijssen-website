<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $fillable = [
        'title',
        'ical_url',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(AgendaItem::class);
    }
}
