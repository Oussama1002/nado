<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Épisodes / podcast Nado Radio (community.radio_episodes) — distincts des Songs
 * (bande-son de fond). Fichier audio sur le disque public.
 *
 * Idempotent. En prod :
 *   php artisan migrate --path=database/migrations/2026_11_13_000005_create_episodes_table.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('episodes')) {
            return;
        }

        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('duration')->nullable(); // ex: "24:10"
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
