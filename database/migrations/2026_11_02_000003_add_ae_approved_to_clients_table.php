<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Validation admin de l'accès Auto Enchère, distincte de is_approved (qui gère
 * la connexion). Un compte flotte&enchere ne peut entrer aux enchères que si
 * ae_approved = true.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('clients', 'ae_approved')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->boolean('ae_approved')->default(false)->after('ae_role');
            });

            // Les clients flotte&enchere déjà existants gardent leur accès.
            DB::table('clients')->where('type', 'flotte&enchere')->update(['ae_approved' => true]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clients', 'ae_approved')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('ae_approved');
            });
        }
    }
};
