<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

/**
 * Active directement toutes les fonctionnalités « Discovery », « Communauté »
 * et l'enchère live — sans passer par la page Réglages. Idempotent : se contente
 * de mettre les flags existants à ON (les lignes sont déjà créées par le seeder).
 *
 * NB : radio.enabled n'est PAS touché (interrupteur global du module Radio, géré
 * à part). community.radio_episodes est activé mais ne s'affiche que si la radio
 * est elle-même active.
 */
return new class extends Migration
{
    /** Flags à activer. */
    private array $flags = [
        // Buyer experience & discovery
        'discovery.favorites_on_cards',
        'discovery.compare',
        'discovery.saved_searches',
        'discovery.price_alerts',
        'discovery.monthly_filter',
        'discovery.smart_search',
        // Community & content
        'community.seller_reviews',
        'community.reviews_on_cards',
        'community.listing_qa',
        'community.model_guides',
        'community.blog_comments',
        'community.radio_episodes',
        // Auto-Enchère live
        'enchere.live_bidding',
    ];

    public function up(): void
    {
        foreach ($this->flags as $key) {
            Setting::set($key, true);   // ne fait rien si la clé n'existe pas
        }
        if (method_exists(Setting::class, 'flushCache')) {
            Setting::flushCache();
        }
    }

    public function down(): void
    {
        foreach ($this->flags as $key) {
            Setting::set($key, false);
        }
        if (method_exists(Setting::class, 'flushCache')) {
            Setting::flushCache();
        }
    }
};
