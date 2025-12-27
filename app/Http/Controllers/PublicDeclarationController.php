<?php

namespace App\Http\Controllers;

use App\Actions\StorePublicDeclaration;
use App\Http\Requests\PublicDeclarationRequest;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicDeclarationController extends Controller
{
    /**
     * Display the public declaration form.
     */
    public function create()
    {
        return Inertia::render('PublicDeclarations/Create', [
            'pages' => $this->getPages(),
        ]);
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
        return Inertia::render('PublicDeclarations/Success', [
            'pages' => $this->getPages(),
        ]);
    }
}
