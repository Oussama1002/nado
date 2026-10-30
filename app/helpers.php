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
