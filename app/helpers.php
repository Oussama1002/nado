<?php

use App\Models\Setting;

/**
 * Helpers globaux de configuration (doc 02 — §6.bis.1).
 * Chargés via l'autoload "files" de composer.json.
 */

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
