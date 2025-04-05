<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddressSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'date_of_birth',
        'phone_number',
        'old_street',
        'old_number',
        'old_zipcode',
        'old_city',
        'new_street',
        'new_number',
        'new_zipcode',
        'new_city',
        'other_people',
        'note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
