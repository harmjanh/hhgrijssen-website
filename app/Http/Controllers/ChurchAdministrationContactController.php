<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChurchAdministrationContactRequest;
use App\Models\ChurchAdministrationContact;
use App\Models\Page;
use App\Notifications\ChurchAdministrationContactConfirmation;
use App\Notifications\ChurchAdministrationContactReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class ChurchAdministrationContactController extends Controller
{
    /**
     * Display the church administration contact form.
     */
    public function show(): Response
    {
        return Inertia::render('ChurchAdministrationContact/Show', [
            'pages' => $this->getPages(),
        ]);
    }

    /**
     * Handle church administration contact form submission.
     */
    public function store(ChurchAdministrationContactRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Create the contact record
        $contact = ChurchAdministrationContact::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
        ]);

        // Send confirmation email to the submitter
        Notification::route('mail', $contact->email)
            ->notify(new ChurchAdministrationContactConfirmation($contact));

        // Send notification to kerkelijk bureau
        $kerkelijkBureauEmail = Config::get('hhgrijssen.admin_email', 'kerkelijkbureau@hhgrijssen.nl');
        Notification::route('mail', $kerkelijkBureauEmail)
            ->notify(new ChurchAdministrationContactReceived($contact));

        return redirect()
            ->route('church-administration-contact.show')
            ->with('success', 'Uw gegevens zijn succesvol verzonden. U ontvangt een bevestigingsmail en het kerkelijk bureau neemt zo spoedig mogelijk contact met u op.');
    }

    /**
     * Get pages for navigation.
     */
    private function getPages()
    {
        return Page::select(['id', 'title', 'slug'])
            ->with(['children' => function ($query) {
                $query->where('exclude_from_navigation', false)
                    ->active()
                    ->orderBy('sort_order');
            }])
            ->active()
            ->whereNull('parent_id')
            ->where('exclude_from_navigation', false)
            ->where('requires_authentication', false)
            ->orderBy('sort_order')
            ->get();
    }
}
