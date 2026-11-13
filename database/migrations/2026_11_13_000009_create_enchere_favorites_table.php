<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Favoris d'ENCHÈRES (distincts des favoris d'annonces : table `favorites`).
 * Permet au bouton « Favori » de la fiche enchère de fonctionner.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('enchere_favorites')) {
            return;
        }
        Schema::create('enchere_favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('enchere_id');
            $table->timestamps();
            $table->unique(['client_id', 'enchere_id']);
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('enchere_id')->references('id')->on('nado_auto_encheres')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enchere_favorites');
    }
};
