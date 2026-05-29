<?php

namespace App\Http\Middleware;

use App\Models\TreatOrder;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTreatOrdersOpen
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! TreatOrder::isOrderingOpen()) {
            if ($request->isMethod('POST')) {
                return redirect()
                    ->route('treat-orders.closed')
                    ->with('error', 'De bestelperiode is gesloten. Bestellen was mogelijk tot en met '.TreatOrder::orderDeadlineFormatted().'.');
            }

            return redirect()->route('treat-orders.closed');
        }

        return $next($request);
    }
}
