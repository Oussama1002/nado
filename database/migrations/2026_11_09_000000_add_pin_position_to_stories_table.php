<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('stories', 'pin_position')) {
            Schema::table('stories', function (Blueprint $table) {
                // 1, 2 or 3 → featured slot on the homepage; null → not pinned.
                $table->unsignedTinyInteger('pin_position')->nullable()->after('views')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stories', 'pin_position')) {
            Schema::table('stories', function (Blueprint $table) {
                $table->dropColumn('pin_position');
            });
        }
    }
};
