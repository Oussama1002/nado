<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Injecte le popup "Publier une demande" avant </body> UNIQUEMENT sur la
 * page d'accueil et /achat (liste des annonces). 30 s après chargement,
 * 7 j de cooldown via cookie. Gated par popups.demandes_noresult.
 *
 * Même pattern que InjectRadioMiniPlayer / InjectPopupAutoEnchere.
 */
class InjectPopupDemandes
{
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $response = $next($request);

        if (! $request->isMethod('GET')
            || $request->ajax()
            || ! $response instanceof Response
            || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        // Scope strict : page d'accueil OU /achat (avec ou sans query string).
        // On exclut la page détail /achat/voiture/* pour ne pas doubler avec
        // d'éventuels autres popups de la page produit.
        $onHome  = $request->is('/');
        $onAchat = $request->is('achat') || ($request->is('achat/*') && ! $request->is('achat/voiture/*'));
        if (! $onHome && ! $onAchat) {
            return $response;
        }

        if (! feature_on('popups.demandes_noresult')) {
            return $response;
        }

        $content = $response->getContent();
        if (! is_string($content) || stripos($content, '</body>') === false) {
            return $response;
        }

        try {
            $html = View::make('partials.popup-demandes')->render();
        } catch (\Throwable $e) {
            $html = '';
        }
        if ($html !== '') {
            $response->setContent(preg_replace('/<\/body>/i', $html . '</body>', $content, 1));
        }

        return $response;
    }
}
