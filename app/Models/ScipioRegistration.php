<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScipioRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'regnr',
        'phonenumber',
        'mobile',
        'has_solidarity_fund',
        'has_zaaier',
        'has_privacy_consent',
        'has_vwb',
        'imported_at',
    ];

    protected $casts = [
        'has_solidarity_fund' => 'boolean',
        'has_zaaier' => 'boolean',
        'has_privacy_consent' => 'boolean',
        'has_vwb' => 'boolean',
        'imported_at' => 'datetime',
    ];
}
