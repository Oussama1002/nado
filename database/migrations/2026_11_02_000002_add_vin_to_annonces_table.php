<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * N° de châssis (VIN) extrait de la carte grise.
 * Alimente la future fonctionnalité historique véhicule / fraude au compteur.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('annonces', 'vin')) {
            Schema::table('annonces', function (Blueprint $table) {
                $table->string('vin')->nullable()->after('matricule');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('annonces', 'vin')) {
            Schema::table('annonces', function (Blueprint $table) {
                $table->dropColumn('vin');
            });
        }
    }
};
