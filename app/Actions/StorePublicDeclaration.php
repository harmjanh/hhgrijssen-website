<?php

namespace App\Actions;

use App\Models\Declaration;
use App\Notifications\DeclarationSubmitted;
use App\Notifications\DeclarationSubmittedToTreasurer;
use Illuminate\Support\Facades\Notification;

class StorePublicDeclaration
{
    /**
     * Execute the action to store a public declaration.
     */
    public function execute(array $data): Declaration
    {
        // Calculate amounts
        $timeslotPrice = 130.00;
        $kilometerPrice = 0.35;

        // Count timeslots (1 if only time_of_service_1, 2 if both are filled)
        $numberOfTimeslots = (!empty($data['time_of_service_1']) && !empty($data['time_of_service_2'])) ? 2 : 1;
        $timeslotTotal = $numberOfTimeslots * $timeslotPrice;
        $kilometerTotal = ($data['kilometers'] ?? 0) * $kilometerPrice;
        $totalAmount = $timeslotTotal + $kilometerTotal;

        // Create the declaration without user_id (public declaration)
        $declaration = Declaration::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'street' => $data['street'],
            'number' => $data['number'],
            'zipcode' => $data['zipcode'],
            'city' => $data['city'],
            'bankaccountnumber' => $data['bankaccountnumber'],
            'date_of_service' => $data['date_of_service'],
            'time_of_service_1' => $data['time_of_service_1'],
            'time_of_service_2' => $data['time_of_service_2'] ?? null,
            'kilometers' => $data['kilometers'],
            'amount' => $totalAmount,
            'explanation' => 'Declaratie predikant ingediend via website',
            'status' => 'pending',
        ]);

        // Send confirmation email to the person who submitted
        Notification::route('mail', $declaration->email)
            ->notify(new DeclarationSubmitted($declaration));

        // Send notification to treasurer
        // You might want to get the treasurer email from settings or a specific user
        $treasurerEmail = config('mail.treasurer_email', 'treasurer@hhgrijssen.nl');
        Notification::route('mail', $treasurerEmail)
            ->notify(new DeclarationSubmittedToTreasurer($declaration));

        return $declaration;
    }
}
