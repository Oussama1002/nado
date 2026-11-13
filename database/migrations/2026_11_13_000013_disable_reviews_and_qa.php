<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

/**
 * Retire de la fiche annonce « Laisser un avis » (avis vendeurs) et la section
 * « Questions & réponses » en désactivant leurs flags.
 */
return new class extends Migration
{
    private array $flags = [
        'community.seller_reviews',
        'community.reviews_on_cards',
        'community.listing_qa',
    ];

    public function up(): void
    {
        foreach ($this->flags as $key) {
            Setting::set($key, false);
        }
        if (method_exists(Setting::class, 'flushCache')) {
            Setting::flushCache();
        }
    }

    public function down(): void
    {
        foreach ($this->flags as $key) {
            Setting::set($key, true);
        }
        if (method_exists(Setting::class, 'flushCache')) {
            Setting::flushCache();
        }
    }
};
