<?php

namespace App\Http\Controllers;

use App\Actions\StoreSolidarityFundAuthorization;
use App\Http\Requests\SolidarityFundAuthorizationRequest;
use App\Models\SolidarityFundAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SolidarityFundAuthorizationController extends Controller
{
    public function index(): Response
    {
        $authorizations = SolidarityFundAuthorization::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $hasAuthorization = SolidarityFundAuthorization::where('user_id', Auth::id())->exists();

        return Inertia::render('SolidarityFundAuthorizations/Index', [
            'authorizations' => $authorizations,
            'hasAuthorization' => $hasAuthorization
        ]);
    }

    public function create(): Response
    {
        // Check if user already has an authorization
        $existingAuthorization = SolidarityFundAuthorization::where('user_id', Auth::id())->first();
        
        if ($existingAuthorization) {
            return redirect()
                ->route('solidarity-fund-authorizations.index')
                ->with('error', 'U heeft al een machtiging ingediend. U kunt slechts één machtiging indienen.');
        }

        $user = Auth::user();

        return Inertia::render('SolidarityFundAuthorizations/Create', [
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
        SolidarityFundAuthorizationRequest $request,
        StoreSolidarityFundAuthorization $action
    ): RedirectResponse {
        // Check if user already has an authorization
        $existingAuthorization = SolidarityFundAuthorization::where('user_id', Auth::id())->first();
        
        if ($existingAuthorization) {
            return redirect()
                ->route('solidarity-fund-authorizations.index')
                ->with('error', 'U heeft al een machtiging ingediend. U kunt slechts één machtiging indienen.');
        }

        $authorization = $action->execute($request->validated(), $request->user());

        return redirect()
            ->route('solidarity-fund-authorizations.index')
            ->with('success', 'Uw machtiging is succesvol ingediend. U ontvangt een bevestigingsemail.');
    }

    public function show(SolidarityFundAuthorization $solidarityFundAuthorization): Response
    {
        // Ensure the user can only view their own authorizations
        if ($solidarityFundAuthorization->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('SolidarityFundAuthorizations/Show', [
            'authorization' => $solidarityFundAuthorization
        ]);
    }
}

