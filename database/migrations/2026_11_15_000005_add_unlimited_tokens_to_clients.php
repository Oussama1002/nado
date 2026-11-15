<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('clients', 'unlimited_tokens')) {
            Schema::table('clients', function (Blueprint $table) {
                // Admin-granted "full access": the client can use every paid
                // feature without their token balance ever being debited.
                $table->boolean('unlimited_tokens')->default(false)->after('tokens_balance');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clients', 'unlimited_tokens')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('unlimited_tokens');
            });
        }
    }
};
