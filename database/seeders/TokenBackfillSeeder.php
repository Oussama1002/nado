<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Services\TokenService;
use Illuminate\Database\Seeder;

/**
 * One-time backfill: grant the signup bonus to clients that registered BEFORE
 * the token system existed. Idempotent — TokenService::grantSignupBonus() skips
 * any client that already has a signup_bonus ledger row, so re-running is safe.
 *
 *   php artisan db:seed --class=TokenBackfillSeeder --force
 */
class TokenBackfillSeeder extends Seeder
{
    public function run(): void
    {
        if (! TokenService::enabled()) {
            $this->command?->warn('Tokens désactivés (tokens.enabled=false) — aucun bonus accordé.');
            return;
        }

        $granted = 0;

        Client::query()->orderBy('id')->chunkById(200, function ($clients) use (&$granted) {
            foreach ($clients as $client) {
                if (TokenService::grantSignupBonus($client)) {
                    $granted++;
                }
            }
        });

        $this->command?->info("Bonus de bienvenue accordé à {$granted} client(s) existant(s).");
    }
}
