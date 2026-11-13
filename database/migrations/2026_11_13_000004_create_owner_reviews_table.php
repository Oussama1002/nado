<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Avis propriétaires agrégés par modèle (alimentent les guides modèles).
 * Modéré : 'pending' par défaut.
 *
 * Idempotent. En prod :
 *   php artisan migrate --path=database/migrations/2026_11_13_000004_create_owner_reviews_table.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('owner_reviews')) {
            return;
        }

        Schema::create('owner_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('modele_id')->index();
            $table->unsignedBigInteger('reviewer_client_id')->nullable()->index();
            $table->unsignedTinyInteger('rating'); // 1..5
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_reviews');
    }
};
