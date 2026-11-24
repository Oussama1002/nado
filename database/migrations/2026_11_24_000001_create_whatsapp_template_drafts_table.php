<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Brouillons locaux de templates WhatsApp.
 *
 * Permet de préparer plusieurs templates (nom + langue + corps + catégorie +
 * footer) côté admin et de les soumettre à Meta un par un via un bouton
 * "Soumettre à Meta". Dès qu'un draft est soumis avec succès, il est supprimé
 * de la table — Meta devient la source de vérité.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_template_drafts', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120);
            $t->string('category', 32);                 // AUTHENTICATION | UTILITY | MARKETING
            $t->string('language', 10);                 // fr, ar, en_US…
            $t->text('body');
            $t->string('footer', 60)->nullable();
            $t->timestamps();

            $t->unique(['name', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_template_drafts');
    }
};
