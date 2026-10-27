<?php

namespace App\Http\Middleware;

use App\Models\News;
use App\Models\PageView;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackPageView
{
    /**
     * Paden (prefixen) die nooit meetellen in de statistieken.
     */
    private const EXCLUDED_PREFIXES = [
        'admin',
        'livewire',
        'horizon',
        'telescope',
        'pulse',
        'storage',
        'audio',
        'youtube/oauth',
        'up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Registreer de weergave pas na het versturen van de response,
     * zodat het bijhouden de bezoeker nooit vertraagt of de pagina breekt.
     */
    public function terminate(Request $request, Response $response): void
    {
        try {
            if (! $this->shouldTrack($request, $response)) {
                return;
            }

            $viewable = $this->resolveViewable($request);

            PageView::create([
                'path' => '/' . ltrim($request->path(), '/'),
                'viewable_type' => $viewable?->getMorphClass(),
                'viewable_id' => $viewable?->getKey(),
                'visitor_hash' => $this->visitorHash($request),
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200) {
            return false;
        }

        // Inertia partial reloads en prefetches zijn geen echte paginaweergaven
        if ($request->headers->has('X-Inertia-Partial-Component')) {
            return false;
        }

        if (str_contains(strtolower($request->headers->get('Purpose', '') . $request->headers->get('Sec-Purpose', '')), 'prefetch')) {
            return false;
        }

        if ($this->isBot($request)) {
            return false;
        }

        $path = trim($request->path(), '/');

        foreach (self::EXCLUDED_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return false;
            }
        }

        return true;
    }

    private function isBot(Request $request): bool
    {
        $userAgent = $request->userAgent();

        if (blank($userAgent)) {
            return true;
        }

        return (bool) preg_match(
            '/bot|crawl|spider|slurp|curl|wget|python|scrapy|httpclient|facebookexternalhit|bingpreview|headless|lighthouse|pingdom|uptime|monitor/i',
            $userAgent
        );
    }

    private function resolveViewable(Request $request): ?Model
    {
        $news = $request->route()?->parameter('news');

        return $news instanceof News ? $news : null;
    }

    /**
     * Anonieme, per dag wisselende hash om unieke bezoekers te kunnen tellen
     * zonder IP-adressen of cookies op te slaan.
     */
    private function visitorHash(Request $request): string
    {
        return hash('sha256', implode('|', [
            config('app.key'),
            now()->toDateString(),
            $request->ip(),
            (string) $request->userAgent(),
        ]));
    }
}
