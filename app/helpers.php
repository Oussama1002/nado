<?php

use App\Models\Setting;

/**
 * Helpers globaux de configuration (doc 02 — §6.bis.1).
 * Chargés via l'autoload "files" de composer.json.
 */

if (! function_exists('admin_can')) {
    /**
     * Contrôle d'accès admin par module et, en option, par action.
     *
     *   admin_can('clients')            → l'admin peut-il voir/toucher le module ?
     *   admin_can('clients', 'delete')  → … peut-il précisément SUPPRIMER ?
     *
     * Un rôle « admin » a tout. Sinon on lit les permissions Spatie :
     *   - granulaire "access.<module>.<ability>" (explore|add|modify|delete) ;
     *   - ancienne "access.<module>" (sans granulaire) = accès complet hérité,
     *     tant qu'aucune permission granulaire n'existe pour ce module.
     * Tolérant aux erreurs (permission inexistante, invité) → false.
     */
    function admin_can(string $module, ?string $ability = null): bool
    {
        $user = auth('admin')->user();
        if (! $user) {
            return false;
        }

        try {
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return true;
            }
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }
            if (! method_exists($user, 'getPermissionNames')) {
                return false;
            }

            $names    = $user->getPermissionNames();
            $base     = 'access.'.$module;
            $hasBase  = $names->contains($base);
            $granular = $names
                ->filter(fn ($p) => str_starts_with($p, $base.'.'))
                ->map(fn ($p) => substr($p, strlen($base.'.')));

            // Pas d'action précise demandée : accès « au module » en général.
            if ($ability === null) {
                return $granular->isNotEmpty() || $hasBase;
            }

            if ($granular->contains($ability)) {
                return true;
            }

            // Fallback hérité : ancienne perm « tout-en-un », sans granulaire.
            return $granular->isEmpty() && $hasBase;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

if (! function_exists('settings')) {
    /**
     * Lit une valeur de configuration typée.
     * Ex : settings('auctions.seller_commission_rate', 3)
     */
    function settings(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('feature_on')) {
    /**
     * Vrai si un module / service est activé.
     * Ex : if (feature_on('auctions.enabled')) { ... }
     */
    function feature_on(string $key): bool
    {
        return Setting::enabled($key);
    }
}

if (! function_exists('monthly_estimate')) {
    /**
     * Mensualité estimée pour un prix donné — FORMULE UNIQUE du site (financement).
     * Annuité : m = C·r / (1 − (1+r)^−n), r = taux annuel/12, n = durée en mois.
     * Reprend la formule du simulateur (detail.blade.php) + les valeurs
     * FinancementSetting, pour que carte catalogue, filtre « mensualité max » et
     * simulateur restent cohérents.
     */
    function monthly_estimate($price, ?int $months = null, float $apport = 0): int
    {
        $price = (float) $price - $apport;
        if ($price <= 0) {
            return 0;
        }
        $months = $months ?: (int) \App\Models\FinancementSetting::getValue('duree_default', 60);
        $months = $months > 0 ? $months : 60;
        $taux   = (float) \App\Models\FinancementSetting::getValue('taux_interet_default', 13);
        $r      = $taux / 100 / 12;

        if ($r > 0) {
            $m = ($price * $r) / (1 - pow(1 + $r, -$months));
        } else {
            $m = $price / $months;
        }
        return (int) round($m);
    }
}

if (! function_exists('price_from_monthly')) {
    /**
     * Inverse de monthly_estimate : prix maximum finançable pour une mensualité
     * donnée (même durée + taux). Sert au filtre « mensualité max ».
     */
    function price_from_monthly($monthly, ?int $months = null): int
    {
        $monthly = (float) $monthly;
        if ($monthly <= 0) {
            return 0;
        }
        $months = $months ?: (int) \App\Models\FinancementSetting::getValue('duree_default', 60);
        $months = $months > 0 ? $months : 60;
        $taux   = (float) \App\Models\FinancementSetting::getValue('taux_interet_default', 13);
        $r      = $taux / 100 / 12;

        if ($r > 0) {
            $price = $monthly * (1 - pow(1 + $r, -$months)) / $r;
        } else {
            $price = $monthly * $months;
        }
        return (int) round($price);
    }
}

if (! function_exists('site_image')) {
    /**
     * Retourne l'URL de l'image illustrative pour l'emplacement $placement.
     * Lit la dernière publication active (`/admin/publications`) de cet
     * emplacement et renvoie son image ; sinon on retombe sur $default.
     *
     *   <img src="{{ site_image('vente-hero', 'https://.../default.jpg') }}">
     *
     * Les valeurs d'emplacement attendues sont celles du champ
     * Publication.placement (vente-hero, ae-expertise, reprise-hero, front-estimation…).
     */
    function site_image(string $placement, string $default = ''): string
    {
        static $cache = [];
        if (! array_key_exists($placement, $cache)) {
            try {
                $pub = \App\Models\Publication::where('placement', $placement)
                    ->where('status', 1)
                    ->orderByDesc('id')
                    ->first();
                $cache[$placement] = $pub && $pub->image ? $pub->image : null;
            } catch (\Throwable $e) {
                $cache[$placement] = null;
            }
        }
        $path = $cache[$placement];
        if (is_string($path) && $path !== '') {
            return asset('storage/' . ltrim($path, '/'));
        }
        return $default;
    }
}

if (! function_exists('client_type_label')) {
    /**
     * Libellé traduit d'un type de client (stocké en français en base).
     * Cherche messages.client_types.<nom> et retombe sur le nom brut si absent,
     * pour que les types ajoutés en admin restent affichés.
     */
    function client_type_label(?string $name): string
    {
        $name = trim((string) $name);
        if ($name === '') {
            return '';
        }

        $key = 'messages.client_types.' . $name;
        $translated = __($key);

        return $translated === $key ? $name : $translated;
    }
}
