<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Injecte le mini-player Nado Radio avant </body> sur TOUTES les pages publiques
 * (hors admin), pour qu'il soit réellement site-wide sans toucher chaque layout.
 * Gated : flag radio.enabled + réglage radio.mini_player.
 */
class InjectRadioMiniPlayer
{
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $response = $next($request);

        // Uniquement des pages HTML "normales" côté front.
        if ($request->isMethod('GET')
            && ! $request->ajax()
            && ! $request->is('admin', 'admin/*')
            && $response instanceof Response
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {

            // Gating peu coûteux (valeurs en cache) avant tout rendu.
            if (feature_on('radio.enabled') && (bool) settings('radio.mini_player', false)) {
                $content = $response->getContent();
                if (is_string($content) && stripos($content, '</body>') !== false) {
                    try {
                        $html = View::make('partials.radio-mini-player')->render();
                    } catch (\Throwable $e) {
                        $html = '';
                    }
                    if ($html !== '') {
                        $response->setContent(preg_replace('/<\/body>/i', $html . '</body>', $content, 1));
                    }
                }
            }
        }

        return $response;
    }
}
