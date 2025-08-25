<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $agenda_id
 * @property string $uid
 * @property string $title
 * @property string $description
 * @property string $location
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 */
class AgendaItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }

    public function service()
    {
        return $this->hasOne(Service::class);
    }
}
