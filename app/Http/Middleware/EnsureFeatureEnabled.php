<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protège des routes derrière un feature-flag de configuration (doc 02 — §6.bis.5).
 *
 * Usage : Route::middleware('feature:auctions.enabled')->group(...)
 * Si le flag est désactivé, la route renvoie 404 (module invisible).
 */
class EnsureFeatureEnabled
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (! feature_on($feature)) {
            abort(404);
        }

        return $next($request);
    }
}
