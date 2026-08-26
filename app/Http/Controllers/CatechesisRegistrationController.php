<?php

namespace App\Http\Controllers;

use App\Enums\CatechesisGroup;
use App\Http\Requests\CatechesisRegistrationRequest;
use App\Models\CatechesisRegistration;
use App\Models\CatechesisSeason;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CatechesisRegistrationController extends Controller
{
    public function closed(): Response
    {
        return Inertia::render('CatechesisRegistrations/Closed', [
            'pages' => $this->getPages(),
        ]);
    }

    public function create(): Response|RedirectResponse
    {
        $season = CatechesisSeason::current();

        if (! $season) {
            return redirect()->route('catechesis-registrations.closed');
        }

        return Inertia::render('CatechesisRegistrations/Create', [
            'pages' => $this->getPages(),
            'season' => $season->name,
            'groups' => CatechesisGroup::groupedForFrontend(),
        ]);
    }

    public function store(CatechesisRegistrationRequest $request): RedirectResponse
    {
        $season = CatechesisSeason::current();

        if (! $season) {
            return redirect()->route('catechesis-registrations.closed');
        }

        $data = $request->validated();
        unset($data['website']);

        $registration = CatechesisRegistration::create([
            ...$data,
            'season_id' => $season->id,
            'group' => CatechesisGroup::from($data['group']),
        ]);

        return redirect()
            ->route('catechesis-registrations.success')
            ->with('catechesis_registration', [
                'first_name' => $registration->first_name,
                'last_name' => $registration->last_name,
                'email' => $registration->email,
                'phone' => $registration->phone,
                'group_label' => $registration->group->label(),
            ])
            ->with('catechesis_season', $season->name);
    }

    public function success(): Response|RedirectResponse
    {
        $registration = session('catechesis_registration');

        if (! $registration) {
            return redirect()->route('catechesis-registrations.create');
        }

        return Inertia::render('CatechesisRegistrations/Success', [
            'pages' => $this->getPages(),
            'registration' => $registration,
            'season' => session('catechesis_season'),
        ]);
    }

    /**
     * @return Collection<int, Page>
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
