<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ScipioRegistration;
use Inertia\Inertia;
use App\Models\Declaration;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = Auth::user();

        // Check if email was just verified
        $emailVerified = request()->query('verified') === '1';

        // Check if user has already submitted contact information
        // If they have, don't show the warning anymore
        $hasSubmittedContactInfo = $user->contact_information_submitted_at !== null;
        
        // Check if user's email or phone number exists in Scipio registrations
        $emailExists = ScipioRegistration::where('email', $user->email)->exists();
        $phoneExists = false;
        
        if ($user->phonenumber) {
            $phoneExists = ScipioRegistration::where('phonenumber', $user->phonenumber)
                ->orWhere('mobile', $user->phonenumber)
                ->exists();
        }
        
        // User is missing if email doesn't exist OR phone doesn't exist (when phone is provided)
        // But only show warning if they haven't already submitted their information
        $missingInScipio = !$hasSubmittedContactInfo && (!$emailExists || ($user->phonenumber && !$phoneExists));

        // Add any data you want to pass to the dashboard view
        $data = [
            'stats' => [
                'total_declarations' => Declaration::where('user_id', $user->id)->count(),
                // Add more stats as needed
            ],
            'emailVerified' => $emailVerified,
            'missingInScipio' => $missingInScipio,
        ];

        return Inertia::render('Dashboard', $data);
    }
}
