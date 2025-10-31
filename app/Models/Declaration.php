<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Declaration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'street',
        'number',
        'zipcode',
        'city',
        'bankaccountnumber',
        'amount',
        'explanation',
        'status',
        'admin_notes',
        'date_of_service',
        'time_of_service_1',
        'time_of_service_2',
        'kilometers',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'bankaccountnumber',
    ];

    /**
     * Get the user that owns the declaration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this is a public declaration (no user_id).
     */
    public function isPublic(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Get the attachments for the declaration.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(DeclarationAttachment::class);
    }

    /**
     * Set the bank account number with encryption
     *
     * @param  string  $value
     * @return void
     */
    public function setBankaccountnumberAttribute($value)
    {
        if ($value) {
            $this->attributes['bankaccountnumber'] = encrypt($value);
        } else {
            $this->attributes['bankaccountnumber'] = null;
        }
    }

    /**
     * Get the decrypted bank account number
     *
     * @param  string  $value
     * @return string|null
     */
    public function getBankaccountnumberAttribute($value)
    {
        if ($value) {
            return decrypt($value);
        }

        return null;
    }
}
