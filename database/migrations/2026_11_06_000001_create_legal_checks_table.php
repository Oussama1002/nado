<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vérification de la situation légale (opposition NARSA) — flux guidé.
 *
 * Le vendeur lance lui-même le contrôle sur le service officiel NARSA
 * (captcha humain), puis déclare le résultat ici avec une capture d'écran.
 * On ne stocke QUE le statut — jamais le nom/adresse du propriétaire.
 *
 * Idempotent : run individuellement en prod via
 *   php artisan migrate --path=database/migrations/2026_11_06_000001_create_legal_checks_table.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('legal_checks')) {
            return;
        }

        Schema::create('legal_checks', function (Blueprint $table) {
            $table->id();

            // Rattachement optionnel : l'outil marche aussi en standalone.
            $table->unsignedBigInteger('annonce_id')->nullable()->index();
            $table->unsignedBigInteger('client_id')->nullable()->index();

            // Matricule marocain en 3 parties + chaîne normalisée d'affichage.
            $table->string('matricule_number')->nullable();
            $table->string('matricule_letter')->nullable();
            $table->string('matricule_region')->nullable();
            $table->string('matricule_full')->nullable();

            // Résultat déclaré par le vendeur d'après NARSA.
            $table->enum('declared_status', ['clear', 'opposition', 'unknown'])->default('unknown');

            // Preuve (capture d'écran) sur le disque PRIVÉ (storage/app/legalchecks).
            $table->string('screenshot_path')->nullable();

            // Quand le vendeur a lancé le contrôle NARSA.
            $table->timestamp('checked_at')->nullable();

            // Revue admin.
            $table->enum('admin_status', ['pending', 'confirmed', 'rejected'])->default('pending')->index();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // FK souples : nullable + onDelete set null (les annonces ont du soft-delete).
            $table->foreign('annonce_id')->references('id')->on('annonces')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_checks');
    }
};
