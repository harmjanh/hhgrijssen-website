<?php

namespace App\Actions;

use App\Models\ZaaierAuthorization;
use App\Models\User;
use App\Notifications\ZaaierAuthorizationReceived;
use App\Notifications\ZaaierAuthorizationSubmitted;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class StoreZaaierAuthorization
{
    /**
     * Store a new Zaaier authorization.
     *
     * @param  array  $data  The validated authorization data
     * @param  User  $user  The authenticated user
     */
    public function execute(array $data, User $user): ZaaierAuthorization
    {
        // Create the authorization
        $authorization = ZaaierAuthorization::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'initials' => $data['initials'],
            'street' => $data['street'],
            'zipcode' => $data['zipcode'],
            'city' => $data['city'],
            'iban' => strtoupper(str_replace(' ', '', $data['iban'])), // Normalize IBAN: uppercase and remove spaces
            'submission_date' => $data['submission_date'],
            'agreed' => $data['agreed'],
        ]);

        // Send notification to the user
        $user->notify(new ZaaierAuthorizationReceived($authorization));

        // Send notification to kerkelijkbureau
        $kerkelijkBureauEmail = Config::get('hhgrijssen.admin_email', 'kerkelijkbureau@hhgrijssen.nl');
        Notification::route('mail', $kerkelijkBureauEmail)
            ->notify(new ZaaierAuthorizationSubmitted($authorization));

        return $authorization;
    }
}

