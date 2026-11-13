<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sépare « Sellerie Similicuir / Tissu » : la colonne existante
 * sellerie_similicuir_tissu devient « Sellerie similicuir », et on ajoute
 * sellerie_tissu pour « Sellerie tissu » (option distincte).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('nado_auto_encheres')
            && ! Schema::hasColumn('nado_auto_encheres', 'sellerie_tissu')) {
            Schema::table('nado_auto_encheres', function (Blueprint $table) {
                $table->boolean('sellerie_tissu')->default(false)->after('sellerie_similicuir_tissu');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('nado_auto_encheres')
            && Schema::hasColumn('nado_auto_encheres', 'sellerie_tissu')) {
            Schema::table('nado_auto_encheres', function (Blueprint $table) {
                $table->dropColumn('sellerie_tissu');
            });
        }
    }
};
