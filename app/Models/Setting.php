<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Configuration pilotable depuis l'admin (doc 02 — §6.bis).
 *
 * Chaque ligne = un réglage typé (prix, taux, durée, feature-flag…).
 * Les lectures passent par le cache ("setting.<key>") pour éviter un hit DB
 * à chaque appel de settings()/feature_on() dans le code.
 */
class Setting extends Model
{
    protected $fillable = [
        'group', 'key', 'value', 'type', 'label',
        'description', 'is_toggle', 'is_secret', 'display_order',
    ];

    protected $casts = [
        'is_toggle'     => 'boolean',
        'is_secret'     => 'boolean',
        'display_order' => 'integer',
    ];

    /** Lit une valeur typée (mise en cache indéfiniment, invalidée à l'écriture). */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting.$key", function () use ($key, $default) {
            $s = static::where('key', $key)->first();

            return $s ? static::castValue($s) : $default;
        });
    }

    /** Écrit une valeur et invalide le cache. Crée la ligne si absente. */
    public static function set(string $key, $value): void
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return;
        }

        $setting->value = static::prepareForStorage($setting->type, $value);
        $setting->save();

        Cache::forget("setting.$key");
    }

    /** Raccourci booléen pour les feature-flags / kill-switches. */
    public static function enabled(string $key): bool
    {
        return (bool) static::get($key, false);
    }

    /** Oublie toutes les clés en cache (utile après un seed ou un import). */
    public static function flushCache(): void
    {
        static::all()->each(fn ($s) => Cache::forget("setting.{$s->key}"));
    }

    /** Transforme la valeur brute stockée selon le type déclaré. */
    public static function castValue(Setting $s)
    {
        return match ($s->type) {
            'bool'            => (bool) ((int) $s->value),
            'int'             => (int) $s->value,
            'decimal', 'percent' => (float) $s->value,
            'json'            => json_decode($s->value ?? 'null', true),
            default           => $s->value,
        };
    }

    /** Normalise une valeur entrante avant stockage (text). */
    public static function prepareForStorage(string $type, $value): ?string
    {
        return match ($type) {
            'bool'  => $value ? '1' : '0',
            'json'  => is_string($value) ? $value : json_encode($value),
            default => $value === null ? null : (string) $value,
        };
    }

    /** Valeur déjà typée pour l'affichage dans les formulaires admin. */
    public function getTypedValueAttribute()
    {
        return static::castValue($this);
    }
}
