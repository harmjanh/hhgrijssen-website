<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'location',
        'is_all_day',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'is_all_day' => 'boolean',
    ];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }
}
