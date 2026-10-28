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
        $locale = $request->session()->get('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale');

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
