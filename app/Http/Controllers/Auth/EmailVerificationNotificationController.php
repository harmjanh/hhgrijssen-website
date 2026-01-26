<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Rate limit: 1 request per minute
        $key = 'verification-email:' . $request->user()->id;
        
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            
            // For Inertia requests, return with error
            if ($request->wantsJson()) {
                return back()->withErrors([
                    'email' => "U kunt de verificatie-email maximaal 1x per minuut aanvragen. Probeer het over {$seconds} seconden opnieuw.",
                ]);
            }
            
            throw ValidationException::withMessages([
                'email' => "U kunt de verificatie-email maximaal 1x per minuut aanvragen. Probeer het over {$seconds} seconden opnieuw.",
            ]);
        }

        RateLimiter::hit($key, 60); // 60 seconds = 1 minute

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
