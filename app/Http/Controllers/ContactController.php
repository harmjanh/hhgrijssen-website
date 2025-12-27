<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Page;
use App\Notifications\ContactFormSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    /**
     * Display the contact form.
     */
    public function show(): Response
    {
        return Inertia::render('Contact/Show', [
            'pages' => $this->getPages(),
        ]);
    }

    /**
     * Handle contact form submission.
     */
    public function store(ContactRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Send notification to webmaster
        Notification::route('mail', 'webmaster@hhgrijssen.nl')
            ->notify(new ContactFormSubmitted($data));

        return redirect()
            ->route('contact.show')
            ->with('success', 'Uw bericht is succesvol verzonden. We nemen zo spoedig mogelijk contact met u op.');
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
