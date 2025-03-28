<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaItem extends Model
{
    protected $guarded = [];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }
}
