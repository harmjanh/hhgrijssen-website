<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'user.not.blocked' => \App\Http\Middleware\EnsureUserNotBlocked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);

        // Bij rate limit (te veel contactverzendingen): terug naar formulier met foutmelding
        $exceptions->renderable(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->header('X-Inertia') && $request->isMethod('POST')) {
                $url = $request->headers->get('Referer', route('contact.show'));
                return redirect($url)->with('error', 'U kunt maar een beperkt aantal berichten per minuut versturen. Probeer het over een minuut opnieuw.');
            }
            return null;
        });
    })->create();
