<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Auto Enchère : rôle du client (vendeur / acheteur).
 * Renseigné à l'inscription — soit via le bouton Vendre/Acheter cliqué sur la
 * prelogin, soit via le choix proposé dans le formulaire si l'utilisateur va
 * directement à la création de compte.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('ae_role', ['vendeur', 'acheteur'])->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('ae_role');
        });
    }
};
