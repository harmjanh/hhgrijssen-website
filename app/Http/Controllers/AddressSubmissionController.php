<?php

namespace App\Http\Controllers;

use App\Actions\SubmitAddressChange;
use App\Http\Requests\AddressSubmissionRequest;
use App\Models\AddressSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AddressSubmissionController extends Controller
{
    public function index(): Response
    {
        $submissions = AddressSubmission::with('user')
            ->latest()
            ->paginate(10);

        return Inertia::render('AddressSubmissions/Index', [
            'submissions' => $submissions
        ]);
    }

    public function create(): Response
    {
        $user = Auth::user();

        return Inertia::render('AddressSubmissions/Create', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phonenumber' => $user->phonenumber,
                'date_of_birth' => $user->date_of_birth,
                'street' => $user->street,
                'number' => $user->number,
                'zipcode' => $user->zipcode,
                'city' => $user->city,
            ]
        ]);
    }

    public function store(AddressSubmissionRequest $request, SubmitAddressChange $action): RedirectResponse
    {
        $submission = $action->execute($request->validated(), $request->user());

        return redirect()
            ->route('address-submissions.show', $submission)
            ->with('success', 'Your address change request has been submitted successfully.');
    }

    public function show(AddressSubmission $submission): Response
    {
        return Inertia::render('AddressSubmissions/Show', [
            'submission' => $submission->load('user')
        ]);
    }
}
