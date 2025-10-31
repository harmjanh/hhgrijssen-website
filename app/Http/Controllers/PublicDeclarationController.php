<?php

namespace App\Http\Controllers;

use App\Actions\StorePublicDeclaration;
use App\Http\Requests\PublicDeclarationRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicDeclarationController extends Controller
{
    /**
     * Display the public declaration form.
     */
    public function create()
    {
        return Inertia::render('PublicDeclarations/Create');
    }

    /**
     * Store a newly created public declaration.
     */
    public function store(PublicDeclarationRequest $request, StorePublicDeclaration $storePublicDeclaration)
    {
        $declaration = $storePublicDeclaration->execute($request->validated());

        return redirect()->route('public-declarations.success')
            ->with('success', 'Uw declaratie is succesvol ingediend. U ontvangt een bevestigingsmail.');
    }

    /**
     * Display the success page after form submission.
     */
    public function success()
    {
        return Inertia::render('PublicDeclarations/Success');
    }
}
