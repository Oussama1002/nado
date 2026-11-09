<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Services\EnchereNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Relance, une seule fois, les clients « flotte&enchere » toujours non validés
 * (pas de caution) 10 jours après leur inscription, pour les inviter à déposer
 * une caution et activer leur compte. À planifier quotidiennement.
 */
class SendCautionReminders extends Command
{
    protected $signature = 'nado:caution-reminders';

    protected $description = 'Relance les clients flotte&enchere non validés 10 jours après inscription (caution)';

    public function handle(): int
    {
        $cutoff = Carbon::now()->subDays(10);

        $clients = Client::where('type', 'flotte&enchere')
            ->where('ae_approved', 0)
            ->whereNull('caution_reminder_sent_at')
            ->whereNotNull('created_at')
            ->where('created_at', '<=', $cutoff)
            ->get();

        if ($clients->isEmpty()) {
            $this->info('Aucun client flotte&enchere à relancer.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($clients as $client) {
            EnchereNotifier::cautionReminder($client);
            // Marqué dans tous les cas : évite de relancer en boucle le même client.
            $client->forceFill(['caution_reminder_sent_at' => Carbon::now()])->save();
            $sent++;
        }

        $this->info("{$sent} client(s) flotte&enchere relancé(s) pour la caution.");
        return self::SUCCESS;
    }
}
