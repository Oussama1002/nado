<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('annonces') && ! Schema::hasColumn('annonces', 'carnet_entretien')) {
            Schema::table('annonces', function (Blueprint $table) {
                $table->boolean('carnet_entretien')->default(0)->after('garantie');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('annonces', 'carnet_entretien')) {
            Schema::table('annonces', function (Blueprint $table) {
                $table->dropColumn('carnet_entretien');
            });
        }
    }
};
