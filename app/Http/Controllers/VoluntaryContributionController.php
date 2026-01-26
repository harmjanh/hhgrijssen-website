<?php

namespace App\Http\Controllers;

use App\Models\ScipioRegistration;
use App\Notifications\RequestRegistrationNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class VoluntaryContributionController extends Controller
{
    /**
     * Display the voluntary contribution page.
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Find registration number via email match
        $scipioRegistration = ScipioRegistration::where('email', $user->email)->first();
        $registrationNumber = $scipioRegistration?->regnr;

        return Inertia::render('VoluntaryContributions/Index', [
            'registrationNumber' => $registrationNumber,
        ]);
    }

    /**
     * Send a request for registration number to the treasurer.
     */
    public function requestRegistrationNumber(): RedirectResponse
    {
        $user = Auth::user();

        // Send notification to treasurer
        $treasurerEmail = 'hhhazelhorst@hhgrijssen.nl';
        Notification::route('mail', $treasurerEmail)
            ->notify(new RequestRegistrationNumber($user));

        return redirect()->route('voluntary-contributions.index')
            ->with('success', 'Uw verzoek is verzonden. De penningmeester zal u zo spoedig mogelijk uw registratienummer toesturen.');
    }
}
