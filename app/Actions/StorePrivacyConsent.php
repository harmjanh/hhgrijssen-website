<?php

namespace App\Actions;

use App\Models\PrivacyConsent;
use App\Models\User;
use App\Notifications\PrivacyConsentReceived;
use App\Notifications\PrivacyConsentSubmitted;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class StorePrivacyConsent
{
    /**
     * Store a new privacy consent.
     *
     * @param  array  $data  The validated consent data
     * @param  User  $user  The authenticated user
     */
    public function execute(array $data, User $user): PrivacyConsent
    {
        // Create the consent
        $consent = PrivacyConsent::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'street' => $data['street'],
            'zipcode' => $data['zipcode'],
            'city' => $data['city'],
            'birth_date' => $data['birth_date'],
            'voorbede_eredienst' => $data['voorbede_eredienst'] ?? false,
            'voorbede_zaaier' => $data['voorbede_zaaier'] ?? false,
            'verjaardag_zaaier' => $data['verjaardag_zaaier'] ?? false,
            'rsv_gegevens' => $data['rsv_gegevens'] ?? false,
            'place' => $data['place'],
            'submission_date' => $data['submission_date'],
            'agreed' => $data['agreed'],
        ]);

        // Send notification to the user
        $user->notify(new PrivacyConsentReceived($consent));

        // Send notification to kerkelijkbureau
        $kerkelijkBureauEmail = Config::get('hhgrijssen.admin_email', 'kerkelijkbureau@hhgrijssen.nl');
        Notification::route('mail', $kerkelijkBureauEmail)
            ->notify(new PrivacyConsentSubmitted($consent));

        return $consent;
    }
}

