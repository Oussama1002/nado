<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /** Locales the site is served in. */
    public const SUPPORTED = ['fr', 'ar'];

    /**
     * Resolve the active locale from the session, then a cookie, then the
     * configured default (French). Must run after StartSession.
     */
    public function handle(Request $request, Closure $next)
    {
        // ?lang= en priorité (iframes/liens qui forcent la langue), puis le choix
        // explicite du visiteur (session/cookie).
        $locale = $request->query('lang')
            ?? $request->session()->get('locale')
            ?? $request->cookie('locale');

        // Première visite (aucun choix) → langue du navigateur/téléphone (ar sinon fr).
        if (! $locale) {
            $locale = $request->getPreferredLanguage(self::SUPPORTED); // 'fr' | 'ar'
        }

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
