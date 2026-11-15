<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('annonces', 'premium_charged_at')) {
            Schema::table('annonces', function (Blueprint $table) {
                // Stamped when the premium-pack token charge has been applied on
                // approval, so re-approving the same annonce never double-charges.
                $table->timestamp('premium_charged_at')->nullable()->after('pack_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('annonces', 'premium_charged_at')) {
            Schema::table('annonces', function (Blueprint $table) {
                $table->dropColumn('premium_charged_at');
            });
        }
    }
};
