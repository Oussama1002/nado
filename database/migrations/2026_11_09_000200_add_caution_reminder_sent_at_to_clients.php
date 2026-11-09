<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('clients', 'caution_reminder_sent_at')) {
            Schema::table('clients', function (Blueprint $table) {
                // Stamped when the "pay a caution to validate your account" reminder has
                // been emailed, so the scheduled command never emails the same client twice.
                $table->timestamp('caution_reminder_sent_at')->nullable()->after('ae_approved');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clients', 'caution_reminder_sent_at')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('caution_reminder_sent_at');
            });
        }
    }
};
