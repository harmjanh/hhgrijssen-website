<?php

namespace App\Actions;

use App\Models\SolidarityFundAuthorization;
use App\Models\User;
use App\Notifications\SolidarityFundAuthorizationReceived;
use App\Notifications\SolidarityFundAuthorizationSubmitted;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class StoreSolidarityFundAuthorization
{
    /**
     * Store a new solidarity fund authorization.
     *
     * @param  array  $data  The validated authorization data
     * @param  User  $user  The authenticated user
     */
    public function execute(array $data, User $user): SolidarityFundAuthorization
    {
        // Create the authorization
        $authorization = SolidarityFundAuthorization::create([
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
        $user->notify(new SolidarityFundAuthorizationReceived($authorization));

        // Send notification to kerkelijkbureau
        $kerkelijkBureauEmail = Config::get('hhgrijssen.admin_email', 'kerkelijkbureau@hhgrijssen.nl');
        Notification::route('mail', $kerkelijkBureauEmail)
            ->notify(new SolidarityFundAuthorizationSubmitted($authorization));

        return $authorization;
    }
}

