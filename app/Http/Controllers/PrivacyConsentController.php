<?php

namespace App\Http\Controllers;

use App\Actions\StorePrivacyConsent;
use App\Http\Requests\PrivacyConsentRequest;
use App\Models\PrivacyConsent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PrivacyConsentController extends Controller
{
    public function index(): Response
    {
        $consents = PrivacyConsent::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $hasConsent = PrivacyConsent::where('user_id', Auth::id())->exists();

        return Inertia::render('PrivacyConsents/Index', [
            'consents' => $consents,
            'hasConsent' => $hasConsent
        ]);
    }

    public function create(): Response
    {
        // Check if user already has a consent
        $existingConsent = PrivacyConsent::where('user_id', Auth::id())->first();
        
        if ($existingConsent) {
            return redirect()
                ->route('privacy-consents.index')
                ->with('error', 'U heeft al een toestemmingsformulier ingediend. U kunt slechts één formulier indienen.');
        }

        $user = Auth::user();

        return Inertia::render('PrivacyConsents/Create', [
            'user' => [
                'name' => $user->name,
                'street' => $user->street,
                'number' => $user->number,
                'zipcode' => $user->zipcode,
                'city' => $user->city,
            ]
        ]);
    }

    public function store(
        PrivacyConsentRequest $request,
        StorePrivacyConsent $action
    ): RedirectResponse {
        // Check if user already has a consent
        $existingConsent = PrivacyConsent::where('user_id', Auth::id())->first();
        
        if ($existingConsent) {
            return redirect()
                ->route('privacy-consents.index')
                ->with('error', 'U heeft al een toestemmingsformulier ingediend. U kunt slechts één formulier indienen.');
        }

        $consent = $action->execute($request->validated(), $request->user());

        return redirect()
            ->route('privacy-consents.index')
            ->with('success', 'Uw toestemmingsformulier is succesvol ingediend. U ontvangt een bevestigingsemail.');
    }

    public function show(PrivacyConsent $privacyConsent): Response
    {
        // Ensure the user can only view their own consents
        if ($privacyConsent->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('PrivacyConsents/Show', [
            'consent' => $privacyConsent
        ]);
    }
}
