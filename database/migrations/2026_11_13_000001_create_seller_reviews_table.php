<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Avis sur les vendeurs/concessionnaires (community.seller_reviews).
 * Un avis cible le CLIENT propriétaire de l'annonce (le vendeur). Modéré :
 * 'pending' par défaut, visible après approbation admin.
 *
 * unique(reviewer, seller) : un client ne note un vendeur qu'une seule fois.
 *
 * Idempotent. En prod :
 *   php artisan migrate --path=database/migrations/2026_11_13_000001_create_seller_reviews_table.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seller_reviews')) {
            return;
        }

        Schema::create('seller_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reviewer_client_id')->index();
            $table->unsignedBigInteger('seller_client_id')->index();
            $table->unsignedBigInteger('annonce_id')->nullable();
            $table->unsignedTinyInteger('rating'); // 1..5
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->boolean('verified')->default(false); // a contacté le vendeur
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->unique(['reviewer_client_id', 'seller_client_id'], 'seller_reviews_unique_pair');
            $table->index(['seller_client_id', 'status'], 'seller_reviews_seller_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_reviews');
    }
};
