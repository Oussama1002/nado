<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('nado_auto_encheres', 'reminder_sent_at')) {
            Schema::table('nado_auto_encheres', function (Blueprint $table) {
                // Stamped when the "24h before opening" reminder email has been sent,
                // so the scheduled command never emails the same auction twice.
                $table->timestamp('reminder_sent_at')->nullable()->after('ends_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('nado_auto_encheres', 'reminder_sent_at')) {
            Schema::table('nado_auto_encheres', function (Blueprint $table) {
                $table->dropColumn('reminder_sent_at');
            });
        }
    }
};
