<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Guides modèles bilingues (community.model_guides) — moteur SEO.
 * Contenu FR + AR stocké côte à côte (rendu selon la locale, fallback FR).
 *
 * Idempotent. En prod :
 *   php artisan migrate --path=database/migrations/2026_11_13_000003_create_model_guides_table.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('model_guides')) {
            return;
        }

        Schema::create('model_guides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id')->nullable()->index();
            $table->unsignedBigInteger('modele_id')->nullable()->index();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('intro')->nullable();
            $table->text('intro_ar')->nullable();
            $table->longText('body_fr')->nullable();
            $table->longText('body_ar')->nullable();
            $table->string('image')->nullable();
            $table->json('specs')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_guides');
    }
};
