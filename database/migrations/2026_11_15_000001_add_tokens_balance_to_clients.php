<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('clients', 'tokens_balance')) {
            Schema::table('clients', function (Blueprint $table) {
                // Spendable token balance (1 token = 1 MAD). The authoritative history
                // lives in token_transactions; this column is the cached running total,
                // always mutated inside the same DB transaction as its ledger row.
                $table->integer('tokens_balance')->default(0)->after('company');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clients', 'tokens_balance')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('tokens_balance');
            });
        }
    }
};
