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
