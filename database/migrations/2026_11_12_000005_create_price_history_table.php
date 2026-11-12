<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Historique des changements de prix des annonces (discovery.price_alerts).
 * Alimenté automatiquement par un hook « updating » sur le modèle Annonces, donc
 * toutes les voies de modification (client, admin, vente) sont couvertes.
 *
 * Idempotent. En prod :
 *   php artisan migrate --path=database/migrations/2026_11_12_000005_create_price_history_table.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('price_history')) {
            return;
        }

        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('annonce_id')->index();
            $table->decimal('old_price', 12, 2)->nullable();
            $table->decimal('new_price', 12, 2)->nullable();
            $table->timestamp('changed_at')->nullable();
            // Pour l'alerte baisse de prix : vrai une fois les favoris notifiés.
            $table->boolean('notified')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_history');
    }
};
