<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolidarityFundAuthorization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'initials',
        'street',
        'zipcode',
        'city',
        'iban',
        'submission_date',
        'agreed',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'agreed' => 'boolean',
    ];

    protected $hidden = [
        'iban',
    ];

    /**
     * Get the user that owns the authorization.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set the IBAN with encryption
     */
    public function setIbanAttribute($value)
    {
        if ($value) {
            $this->attributes['iban'] = encrypt($value);
        } else {
            $this->attributes['iban'] = null;
        }
    }

    /**
     * Get the decrypted IBAN
     */
    public function getIbanAttribute($value)
    {
        if ($value) {
            return decrypt($value);
        }

        return null;
    }
}
