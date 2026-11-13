<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sépare « Sellerie Similicuir / Tissu » sur les annonces : la colonne
 * sellerie_similicuir_tissu devient « Sellerie similicuir » et on ajoute
 * sellerie_tissu pour « Sellerie tissu » (option distincte).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('annonces')
            && ! Schema::hasColumn('annonces', 'sellerie_tissu')) {
            Schema::table('annonces', function (Blueprint $table) {
                $table->tinyInteger('sellerie_tissu')->nullable()->default(0)->after('sellerie_similicuir_tissu');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('annonces')
            && Schema::hasColumn('annonces', 'sellerie_tissu')) {
            Schema::table('annonces', function (Blueprint $table) {
                $table->dropColumn('sellerie_tissu');
            });
        }
    }
};
