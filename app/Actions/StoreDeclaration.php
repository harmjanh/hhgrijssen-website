<?php

namespace App\Actions;

use App\Models\Declaration;
use App\Models\User;
use App\Notifications\DeclarationSubmitted;
use App\Notifications\DeclarationSubmittedToTreasurer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class StoreDeclaration
{
    /**
     * Store a new declaration.
     *
     * @param  array  $data  The validated declaration data
     * @param  User  $user  The authenticated user
     * @param  Request  $request  The request instance for file handling
     */
    public function execute(array $data, User $user, Request $request): Declaration
    {
        // Create the declaration
        $declaration = Declaration::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'street' => $data['street'],
            'number' => $data['number'],
            'zipcode' => $data['zipcode'],
            'city' => $data['city'],
            'bankaccountnumber' => $data['bankaccountnumber'],
            'amount' => $data['amount'],
            'explanation' => $data['explanation'],
            'status' => 'pending',
        ]);

        // Handle attachments
        $this->handleAttachments($request, $declaration);

        // Send notification to the user
        $user->notify(new DeclarationSubmitted($declaration));

        // Send notification to the treasurer
        $treasurerEmail = Config::get('hhgrijssen.treasurer_email');
        Notification::route('mail', $treasurerEmail)
            ->notify(new DeclarationSubmittedToTreasurer($declaration));

        return $declaration;
    }

    /**
     * Handle file attachments for a declaration.
     */
    private function handleAttachments(Request $request, Declaration $declaration): void
    {
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('declarations/' . $declaration->id, 'public');

                $declaration->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }
    }
}
