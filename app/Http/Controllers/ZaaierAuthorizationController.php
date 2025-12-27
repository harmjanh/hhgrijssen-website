<?php

namespace App\Http\Controllers;

use App\Actions\StoreZaaierAuthorization;
use App\Http\Requests\ZaaierAuthorizationRequest;
use App\Models\ZaaierAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ZaaierAuthorizationController extends Controller
{
    public function index(): Response
    {
        $authorizations = ZaaierAuthorization::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $hasAuthorization = ZaaierAuthorization::where('user_id', Auth::id())->exists();

        return Inertia::render('ZaaierAuthorizations/Index', [
            'authorizations' => $authorizations,
            'hasAuthorization' => $hasAuthorization
        ]);
    }

    public function create(): Response
    {
        // Check if user already has an authorization
        $existingAuthorization = ZaaierAuthorization::where('user_id', Auth::id())->first();
        
        if ($existingAuthorization) {
            return redirect()
                ->route('zaaier-authorizations.index')
                ->with('error', 'U heeft al een machtiging ingediend. U kunt slechts één machtiging indienen.');
        }

        $user = Auth::user();

        return Inertia::render('ZaaierAuthorizations/Create', [
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
        ZaaierAuthorizationRequest $request,
        StoreZaaierAuthorization $action
    ): RedirectResponse {
        // Check if user already has an authorization
        $existingAuthorization = ZaaierAuthorization::where('user_id', Auth::id())->first();
        
        if ($existingAuthorization) {
            return redirect()
                ->route('zaaier-authorizations.index')
                ->with('error', 'U heeft al een machtiging ingediend. U kunt slechts één machtiging indienen.');
        }

        $authorization = $action->execute($request->validated(), $request->user());

        return redirect()
            ->route('zaaier-authorizations.index')
            ->with('success', 'Uw machtiging is succesvol ingediend. U ontvangt een bevestigingsemail.');
    }

    public function show(ZaaierAuthorization $zaaierAuthorization): Response
    {
        // Ensure the user can only view their own authorizations
        if ($zaaierAuthorization->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('ZaaierAuthorizations/Show', [
            'authorization' => $zaaierAuthorization
        ]);
    }
}

