<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactInformationRequest;
use App\Notifications\ContactInformationSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class ContactInformationController extends Controller
{
    /**
     * Display the contact information form.
     */
    public function create(): Response
    {
        $user = Auth::user();

        return Inertia::render('ContactInformation/Create', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phonenumber' => $user->phonenumber,
            ],
        ]);
    }

    /**
     * Handle contact information form submission.
     */
    public function store(ContactInformationRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        // Update user with new information and mark as submitted
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phonenumber' => $data['phonenumber'],
            'contact_information_submitted_at' => now(),
        ]);

        // Send notification to kerkelijk bureau
        $kerkelijkBureauEmail = Config::get('hhgrijssen.admin_email', 'kerkelijkbureau@hhgrijssen.nl');
        Notification::route('mail', $kerkelijkBureauEmail)
            ->notify(new ContactInformationSubmitted($user, $data));

        return redirect()
            ->route('dashboard')
            ->with('success', 'Uw gegevens zijn succesvol verzonden naar het kerkelijk bureau. Uw profiel is bijgewerkt.');
    }
}
