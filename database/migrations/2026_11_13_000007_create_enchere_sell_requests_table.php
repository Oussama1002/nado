<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Demandes de vente (Auto-Enchère) : véhicules soumis par un vendeur via
 * « Soumettre mes véhicules » sur /nadoautoenchere/vendre. Jusqu'ici l'envoi
 * n'ouvrait que WhatsApp ; on persiste désormais la demande pour la lister
 * côté admin.
 *
 * Idempotent. En prod :
 *   php artisan migrate --path=database/migrations/2026_11_13_000007_create_enchere_sell_requests_table.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('enchere_sell_requests')) {
            return;
        }

        Schema::create('enchere_sell_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->json('vehicles')->nullable(); // [{marque,modele,annee,km,etat}, ...]
            $table->enum('status', ['new', 'processed', 'archived'])->default('new')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enchere_sell_requests');
    }
};
