<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivacyConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'street',
        'zipcode',
        'city',
        'birth_date',
        'voorbede_eredienst',
        'voorbede_zaaier',
        'verjaardag_zaaier',
        'rsv_gegevens',
        'place',
        'submission_date',
        'agreed',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'submission_date' => 'date',
        'voorbede_eredienst' => 'boolean',
        'voorbede_zaaier' => 'boolean',
        'verjaardag_zaaier' => 'boolean',
        'rsv_gegevens' => 'boolean',
        'agreed' => 'boolean',
    ];

    /**
     * Get the user that owns the consent.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
