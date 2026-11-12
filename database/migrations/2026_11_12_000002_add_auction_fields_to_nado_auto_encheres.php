<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Auto-Enchère compétitive : transforme l'enchère "proposition scellée" en
 * enchère chronométrée transparente.
 *
 * Ajoute à nado_auto_encheres : prix courant, mise à prix, prix de réserve,
 * incrément minimum, statut du cycle de vie, gagnant + enchère gagnante, et un
 * compteur d'extensions anti-snipe.
 *
 * Idempotent (guards hasColumn). En prod :
 *   php artisan migrate --path=database/migrations/2026_11_12_000002_add_auction_fields_to_nado_auto_encheres.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('nado_auto_encheres')) {
            return;
        }

        Schema::table('nado_auto_encheres', function (Blueprint $table) {
            if (! Schema::hasColumn('nado_auto_encheres', 'starting_price')) {
                $table->decimal('starting_price', 12, 2)->nullable()->after('prix');
            }
            if (! Schema::hasColumn('nado_auto_encheres', 'current_price')) {
                $table->decimal('current_price', 12, 2)->nullable()->after('starting_price');
            }
            if (! Schema::hasColumn('nado_auto_encheres', 'reserve_price')) {
                $table->decimal('reserve_price', 12, 2)->nullable()->after('current_price');
            }
            if (! Schema::hasColumn('nado_auto_encheres', 'min_increment')) {
                $table->decimal('min_increment', 12, 2)->default(1000)->after('reserve_price');
            }
            if (! Schema::hasColumn('nado_auto_encheres', 'status')) {
                $table->enum('status', ['draft', 'scheduled', 'live', 'ended', 'cancelled'])
                    ->default('draft')->after('min_increment')->index();
            }
            if (! Schema::hasColumn('nado_auto_encheres', 'winner_client_id')) {
                $table->unsignedBigInteger('winner_client_id')->nullable()->after('status');
            }
            if (! Schema::hasColumn('nado_auto_encheres', 'winning_bid_id')) {
                $table->unsignedBigInteger('winning_bid_id')->nullable()->after('winner_client_id');
            }
            if (! Schema::hasColumn('nado_auto_encheres', 'extensions_count')) {
                $table->unsignedSmallInteger('extensions_count')->default(0)->after('winning_bid_id');
            }
        });

        // Backfill des lignes existantes : la mise à prix = prix estimé, le prix
        // courant = plus haute enchère connue (sinon mise à prix), et le statut est
        // déduit de active + fenêtre starts_at/ends_at.
        $now = now();
        DB::table('nado_auto_encheres')->orderBy('id')->chunkById(200, function ($rows) use ($now) {
            foreach ($rows as $row) {
                $start   = ($row->starting_price ?? null) ?: ($row->prix ?? 0);
                $highest = DB::table('nado_auto_encheres_bid')
                    ->where('enchere_id', $row->id)
                    ->max('proposition');
                $current = max((float) $start, (float) ($highest ?? 0));

                $status = 'draft';
                if ($row->active) {
                    $endsPast    = $row->ends_at   && $now->gt(\Illuminate\Support\Carbon::parse($row->ends_at));
                    $startsFuture = $row->starts_at && $now->lt(\Illuminate\Support\Carbon::parse($row->starts_at));
                    if ($endsPast) {
                        $status = 'ended';
                    } elseif ($startsFuture) {
                        $status = 'scheduled';
                    } elseif ($row->starts_at || $row->ends_at) {
                        $status = 'live';
                    }
                }

                DB::table('nado_auto_encheres')->where('id', $row->id)->update([
                    'starting_price' => $start,
                    'current_price'  => $current,
                    'status'         => $status,
                ]);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('nado_auto_encheres')) {
            return;
        }

        Schema::table('nado_auto_encheres', function (Blueprint $table) {
            foreach ([
                'extensions_count', 'winning_bid_id', 'winner_client_id',
                'status', 'min_increment', 'reserve_price', 'current_price', 'starting_price',
            ] as $col) {
                if (Schema::hasColumn('nado_auto_encheres', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
