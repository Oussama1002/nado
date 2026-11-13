<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Questions / réponses publiques sur une annonce (community.listing_qa).
 * Modéré : 'pending' par défaut, visible après approbation.
 *
 * Idempotent. En prod :
 *   php artisan migrate --path=database/migrations/2026_11_13_000002_create_listing_questions_table.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('listing_questions')) {
            return;
        }

        Schema::create('listing_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('annonce_id')->index();
            $table->unsignedBigInteger('asker_client_id')->nullable()->index();
            $table->text('question');
            $table->text('answer')->nullable();
            $table->unsignedBigInteger('answered_by')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_questions');
    }
};
