<?php

namespace App\Actions;

use App\Models\AddressSubmission;
use App\Models\User;
use App\Notifications\AddressSubmissionReceived;
use App\Notifications\NewAddressSubmission;
use Illuminate\Support\Facades\Notification;

class SubmitAddressChange
{
    public function execute(array $data, User $user): AddressSubmission
    {
        // Create the address submission
        $submission = AddressSubmission::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'date_of_birth' => $data['date_of_birth'],
            'phone_number' => $data['phone_number'],

            'old_street' => $data['old_street'],
            'old_number' => $data['old_number'],
            'old_zipcode' => $data['old_zipcode'],
            'old_city' => $data['old_city'],
            'new_street' => $data['new_street'],
            'new_number' => $data['new_number'],
            'new_zipcode' => $data['new_zipcode'],
            'new_city' => $data['new_city'],
            'other_people' => $data['other_people'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Update the user's address
        $user->update([
            'street' => $data['new_street'],
            'number' => $data['new_number'],
            'zipcode' => $data['new_zipcode'],
            'city' => $data['new_city'],
            'date_of_birth' => $data['date_of_birth'],
            'phonenumber' => $data['phone_number'],
        ]);

        // Send notifications
        $user->notify(new AddressSubmissionReceived($submission));

        // Notify admins
        $adminEmail = config('hhgrijssen.admin_email');
        if ($adminEmail) {
            Notification::route('mail', $adminEmail)
                ->notify(new NewAddressSubmission($submission));
        }

        return $submission;
    }
}
