<?php

namespace App\Console\Commands;

use App\Models\NadoAutoEnchere;
use App\Services\EnchereNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Envoie, une seule fois par enchère, un email de rappel à tous les clients
 * « flotte&enchere » approuvés lorsqu'une enchère ouvre dans moins de 24 h.
 * À planifier toutes les heures (voir App\Console\Kernel).
 */
class SendEnchereReminders extends Command
{
    protected $signature = 'nado:enchere-reminders';

    protected $description = 'Rappel email aux clients flotte&enchere ~24h avant l\'ouverture d\'une enchère';

    public function handle(): int
    {
        $now    = Carbon::now();
        $within = $now->copy()->addDay(); // fenêtre : ouverture dans les 24 prochaines heures

        $encheres = NadoAutoEnchere::query()
            ->where('active', 1)
            ->whereNull('reminder_sent_at')
            ->whereNotNull('starts_at')
            ->whereBetween('starts_at', [$now, $within])
            ->get();

        if ($encheres->isEmpty()) {
            $this->info('Aucune enchère à notifier dans les 24 prochaines heures.');
            return self::SUCCESS;
        }

        foreach ($encheres as $enchere) {
            $sent = EnchereNotifier::openingSoon($enchere);           // tous les clients flotte&enchere
            $owner = EnchereNotifier::openingSoonToOwner($enchere);   // le vendeur (propriétaire du véhicule)
            // Marqué même si 0 envoi (aucun client) : évite de réessayer en boucle.
            $enchere->forceFill(['reminder_sent_at' => $now])->save();
            $this->info("Enchère #{$enchere->id} : {$sent} email(s) clients" . ($owner ? ' + vendeur' : '') . '.');
        }

        return self::SUCCESS;
    }
}
