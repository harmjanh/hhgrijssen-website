<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'agenda_item_id',
        'pastor',
        'liturgy',
        'youtube_url',
    ];

    public function agendaItem()
    {
        return $this->belongsTo(AgendaItem::class);
    }

    public function getStartDateAttribute()
    {
        return $this->agendaItem?->start_date;
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date?->format('d-m-Y H:i');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_date?->format('H:i');
    }

    public function getFormattedStartDateOnlyAttribute()
    {
        return $this->start_date?->format('d-m-Y');
    }
}
