<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enchères automatiques (proxy bids) : un enchérisseur peut fixer un montant
 * maximum ; le système relance automatiquement juste au-dessus des concurrents
 * jusqu'à ce plafond.
 *
 * `proposition` reste le MONTANT de l'enchère (on ne duplique pas en `amount`).
 * On ajoute `is_auto` (enchère posée par le moteur proxy) et `max_amount`
 * (plafond du proxy), plus un index (enchere_id, proposition) pour retrouver
 * vite la meilleure enchère.
 *
 * Idempotent (guards hasColumn). En prod :
 *   php artisan migrate --path=database/migrations/2026_11_12_000003_add_proxy_fields_to_nado_auto_encheres_bid.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('nado_auto_encheres_bid')) {
            return;
        }

        Schema::table('nado_auto_encheres_bid', function (Blueprint $table) {
            if (! Schema::hasColumn('nado_auto_encheres_bid', 'is_auto')) {
                $table->boolean('is_auto')->default(false)->after('proposition');
            }
            if (! Schema::hasColumn('nado_auto_encheres_bid', 'max_amount')) {
                $table->decimal('max_amount', 12, 2)->nullable()->after('is_auto');
            }
        });

        // Index composite pour les lectures "meilleure enchère" (best-effort :
        // ignore si déjà présent sur une base ré-exécutée).
        try {
            Schema::table('nado_auto_encheres_bid', function (Blueprint $table) {
                $table->index(['enchere_id', 'proposition'], 'nae_bid_enchere_prop_idx');
            });
        } catch (\Throwable $e) {
            // index déjà existant → on ignore
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('nado_auto_encheres_bid')) {
            return;
        }

        Schema::table('nado_auto_encheres_bid', function (Blueprint $table) {
            try {
                $table->dropIndex('nae_bid_enchere_prop_idx');
            } catch (\Throwable $e) {
                // ignore
            }
            foreach (['max_amount', 'is_auto'] as $col) {
                if (Schema::hasColumn('nado_auto_encheres_bid', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
