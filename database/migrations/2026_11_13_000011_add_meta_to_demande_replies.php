<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stocke la proposition de véhicule de façon structurée (champs + photos)
 * pour l'afficher en carte, au lieu d'un bloc de texte dans `message`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('demande_replies')
            && ! Schema::hasColumn('demande_replies', 'meta')) {
            Schema::table('demande_replies', function (Blueprint $table) {
                $table->json('meta')->nullable()->after('message');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('demande_replies')
            && Schema::hasColumn('demande_replies', 'meta')) {
            Schema::table('demande_replies', function (Blueprint $table) {
                $table->dropColumn('meta');
            });
        }
    }
};
