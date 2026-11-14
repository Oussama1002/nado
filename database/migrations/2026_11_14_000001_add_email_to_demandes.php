<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Email (facultatif) du demandeur, pour le notifier quand un véhicule est proposé.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('demandes') && ! Schema::hasColumn('demandes', 'email')) {
            Schema::table('demandes', function (Blueprint $table) {
                $table->string('email')->nullable()->after('telephone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('demandes') && Schema::hasColumn('demandes', 'email')) {
            Schema::table('demandes', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }
};
