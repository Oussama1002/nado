<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Injecte le popup "Découvrir Auto-Enchère B2B" avant </body> sur TOUTES les
 * pages publiques (hors admin, hors module nadoautoenchere lui-même).
 *
 * Gating :
 *  - Flag admin popups.autoenchere_promo = true
 *  - Visiteur PAS déjà un pro approuvé (invité OU client dont
 *    type != 'flotte&enchere' OU ae_approved = false)
 *  - Page HTML "normale" côté front (pas d'AJAX, pas d'admin, pas d'AE)
 *
 * Même pattern que InjectRadioMiniPlayer.
 */
class InjectPopupAutoEnchere
{
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $response = $next($request);

        if (! $request->isMethod('GET')
            || $request->ajax()
            || $request->is('admin', 'admin/*')
            || $request->is('nadoautoenchere', 'nadoautoenchere/*')
            || ! $response instanceof Response
            || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        if (! feature_on('popups.autoenchere_promo')) {
            return $response;
        }

        // On ne montre PAS aux pros déjà approuvés (ils connaissent déjà le module).
        if ($client = Auth::guard('client')->user()) {
            if ($client->type === 'flotte&enchere' && (bool) $client->ae_approved) {
                return $response;
            }
        }

        $content = $response->getContent();
        if (! is_string($content) || stripos($content, '</body>') === false) {
            return $response;
        }

        try {
            $html = View::make('partials.popup-autoenchere')->render();
        } catch (\Throwable $e) {
            $html = '';
        }
        if ($html !== '') {
            $response->setContent(preg_replace('/<\/body>/i', $html . '</body>', $content, 1));
        }

        return $response;
    }
}
