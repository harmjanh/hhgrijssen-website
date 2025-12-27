<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PickupMoment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'active',
    ];

    protected $casts = [
        'date' => 'date',
        'active' => 'boolean',
    ];

    /**
     * Get the coin orders for this pickup moment.
     */
    public function coinOrders(): HasMany
    {
        return $this->hasMany(CoinOrder::class);
    }

    /**
     * Get formatted date and time string.
     */
    public function getFormattedDateTimeAttribute(): string
    {
        $date = $this->date->format('d-m-Y');
        return $date . ' om 10:00';
    }
}
