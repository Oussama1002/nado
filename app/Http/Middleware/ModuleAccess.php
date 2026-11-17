<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôle d'accès par module pour l'admin.
 *
 * Remplace `role:admin` sur les routes d'admin. Un utilisateur « admin » passe
 * partout ; un « member » ne passe que sur les modules qui lui sont accordés
 * (permission "access.<module>", cf. Admin\UserController + helper admin_can()).
 *
 * Le module est déduit du NOM de la route (ex. "admin.clients.index" → clients),
 * donc le même middleware marche appliqué à un groupe de routes ou dans le
 * __construct d'un contrôleur. Une route non mappée est autorisée (dashboard,
 * profil, uploads…) ; les exports restent réservés aux admins.
 */
class ModuleAccess
{
    /**
     * Segment de nom de route (avant/après "admin.") → clé de module.
     * Les segments non listés ne sont pas restreints (voir handle()).
     */
    private const MAP = [
        'annonces'            => 'annonces',
        'entretiens'          => 'annonces',
        'demandes'            => 'demandes',
        'clients'             => 'clients',
        'ventes'              => 'ventes',
        'reprises'            => 'reprises',
        'finances'            => 'finances',
        'financementsettings' => 'finances',
        'offres'              => 'encheres',
        'autoenchere'         => 'encheres',
        'nadoautoenchere'     => 'encheres',
        'nadoautoencherebid'  => 'encheres',
        'estimations'         => 'estimations',
        'legalchecks'         => 'legalchecks',
        'brands'              => 'catalogue',
        'models'              => 'catalogue',
        'ville'               => 'catalogue',
        'villes'              => 'catalogue',
        'colors'              => 'catalogue',
        'articles'            => 'contenu',
        'social-posts'        => 'contenu',
        'avis-vd'             => 'contenu',
        'avisvideos'          => 'contenu',
        'episodes'            => 'contenu',
        'stories'             => 'contenu',
        'songs'               => 'contenu',
        'imgslider'           => 'contenu',
        'community'           => 'contenu',
        'avistext'            => 'contenu',
        'avisvd'              => 'contenu',
        'modelguides'         => 'contenu',
        'videoads'            => 'publicite',
        'publications'        => 'publicite',
        'discussions'         => 'discussions',
        'users'               => 'users',
        'settings'            => 'settings',
    ];

    /** Segments toujours réservés aux admins (jamais accordables à un member). */
    private const ADMIN_ONLY = ['export'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();

        // Non connecté → laisse auth:admin gérer (ce middleware vient après).
        if (! $user) {
            return $next($request);
        }

        // Les admins passent partout.
        if (self::isFullAdmin($user)) {
            return $next($request);
        }

        $module = $this->moduleForRoute($request);

        // Route non rattachée à un module → non restreinte ici.
        if ($module === null) {
            return $next($request);
        }

        // Réservé aux admins (ex. exports) → bloqué pour un member.
        if ($module === '__admin_only__') {
            abort(403, "Accès réservé aux administrateurs.");
        }

        // Action requise (explore|add|modify|delete) déduite de la route, puis
        // vérifiée via admin_can() — source de vérité unique (helper partagé
        // avec la barre latérale et les boutons des vues).
        $ability = $this->abilityForRoute($request);

        if (admin_can($module, $ability)) {
            return $next($request);
        }

        abort(403, "Vous n'avez pas accès à ce module.");
    }

    /**
     * Déduit l'action (explore|add|modify|delete) depuis le dernier segment du
     * nom de route (actions CRUD standard), sinon depuis le verbe HTTP :
     *   GET/HEAD → explore, DELETE → delete, POST/PUT/PATCH → modify.
     */
    private function abilityForRoute(Request $request): string
    {
        $name   = optional($request->route())->getName();
        $action = $name ? strtolower((string) last(explode('.', $name))) : null;

        switch ($action) {
            case 'index':
            case 'show':
                return 'explore';
            case 'create':
            case 'store':
                return 'add';
            case 'edit':
            case 'update':
                return 'modify';
            case 'destroy':
                return 'delete';
        }

        $method = strtoupper($request->method());
        if (in_array($method, ['GET', 'HEAD'], true)) {
            return 'explore';
        }
        if ($method === 'DELETE') {
            return 'delete';
        }

        return 'modify'; // POST / PUT / PATCH et autres écritures
    }

    private static function isFullAdmin($user): bool
    {
        try {
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return true;
            }
            return method_exists($user, 'hasRole') && $user->hasRole('admin');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Déduit la clé de module depuis le nom de la route courante. */
    private function moduleForRoute(Request $request): ?string
    {
        $name = optional($request->route())->getName();
        if (! $name) {
            return null;
        }

        // "admin.clients.index" → ["admin","clients","index"] ; on prend le
        // 1er segment significatif (après un éventuel "admin.").
        $parts   = explode('.', $name);
        $segment = strtolower($parts[0] === 'admin' && isset($parts[1]) ? $parts[1] : $parts[0]);

        if (in_array($segment, self::ADMIN_ONLY, true)) {
            return '__admin_only__';
        }

        return self::MAP[$segment] ?? null;
    }
}
