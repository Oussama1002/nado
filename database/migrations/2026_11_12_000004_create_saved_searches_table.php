<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recherches sauvegardées + alertes « nouveaux véhicules » (discovery.saved_searches).
 * Les paramètres (params JSON) sont exactement le jeu de filtres AnnonceFilter,
 * pour que l'alerte matche les mêmes résultats que le catalogue.
 *
 * Idempotent. En prod :
 *   php artisan migrate --path=database/migrations/2026_11_12_000004_create_saved_searches_table.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('saved_searches')) {
            return;
        }

        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->index();
            $table->string('label')->nullable();
            $table->json('params')->nullable();
            $table->boolean('notify_new')->default(true);
            $table->enum('channel', ['email'])->default('email');
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};
