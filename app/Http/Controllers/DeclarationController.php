<?php

namespace App\Http\Controllers;

use App\Actions\StoreDeclaration;
use App\Http\Requests\DeclarationStoreRequest;
use App\Models\Declaration;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DeclarationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the user's declarations.
     */
    public function index()
    {
        $this->authorize('viewAny', Declaration::class);

        $declarations = Declaration::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Declarations/Index', [
            'declarations' => $declarations,
        ]);
    }

    /**
     * Display the declaration form.
     */
    public function create()
    {
        $this->authorize('create', Declaration::class);

        $user = Auth::user();

        return Inertia::render('Declarations/Create', [
            'user' => [
                'name' => $user->name,
                'street' => $user->street,
                'number' => $user->number,
                'zipcode' => $user->zipcode,
                'city' => $user->city,
                'bankaccountnumber' => $user->bankaccountnumber,
            ],
        ]);
    }

    /**
     * Store a newly created declaration in storage.
     */
    public function store(DeclarationStoreRequest $request, StoreDeclaration $storeDeclaration)
    {
        $this->authorize('create', Declaration::class);

        $user = Auth::user();

        $declaration = $storeDeclaration->execute(
            $request->validated(),
            $user,
            $request
        );

        return redirect()->route('declarations.show', $declaration)
            ->with('success', 'Uw declaratie is succesvol ingediend.');
    }

    /**
     * Display the specified declaration.
     */
    public function show(Declaration $declaration)
    {
        $this->authorize('view', $declaration);

        return Inertia::render('Declarations/Show', [
            'declaration' => $declaration->load('attachments'),
        ]);
    }
}
