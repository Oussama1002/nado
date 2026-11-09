<?php

namespace App\Console\Commands;

use App\Models\NadoAutoEnchere;
use App\Services\EnchereNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Envoie, une seule fois par enchère, un email de rappel à tous les clients
 * « flotte&enchere » approuvés + au vendeur (propriétaire) lorsqu'une enchère
 * ouvre dans moins de 24 h. À planifier toutes les heures (voir Kernel).
 *
 * Test manuel : php artisan nado:enchere-reminders {id} --force
 * (envoie pour une enchère précise en ignorant la fenêtre et le flag).
 */
class SendEnchereReminders extends Command
{
    protected $signature = 'nado:enchere-reminders
        {enchere? : ID d\'une enchère à notifier (test)}
        {--force : Ignorer la fenêtre 24h et le flag "déjà envoyé"}';

    protected $description = 'Rappel email aux clients flotte&enchere + vendeur ~24h avant l\'ouverture d\'une enchère';

    public function handle(): int
    {
        $now = Carbon::now();

        // --- Mode test : une enchère précise ---
        if ($id = $this->argument('enchere')) {
            $enchere = NadoAutoEnchere::find($id);
            if (!$enchere) {
                $this->error("Enchère #{$id} introuvable.");
                return self::FAILURE;
            }
            if (!$this->option('force') && $enchere->reminder_sent_at) {
                $this->warn("Enchère #{$id} : rappel déjà envoyé le {$enchere->reminder_sent_at}. Utilisez --force pour renvoyer.");
                return self::SUCCESS;
            }
            $this->notifyOne($enchere, $now);
            return self::SUCCESS;
        }

        // --- Mode normal : toutes les enchères ouvrant dans les 24 h ---
        $within = $now->copy()->addDay();

        $encheres = NadoAutoEnchere::query()
            ->where('active', 1)
            ->whereNull('reminder_sent_at')
            ->whereNotNull('starts_at')
            ->whereBetween('starts_at', [$now, $within])
            ->get();

        $this->line("Maintenant : {$now} (fuseau " . config('app.timezone') . ')');
        $this->line("Enchères ouvrant dans les 24 h et non encore notifiées : {$encheres->count()}");

        if ($encheres->isEmpty()) {
            $this->info('Rien à envoyer.');
            return self::SUCCESS;
        }

        foreach ($encheres as $enchere) {
            $this->notifyOne($enchere, $now);
        }

        return self::SUCCESS;
    }

    private function notifyOne(NadoAutoEnchere $enchere, Carbon $now): void
    {
        $sent  = EnchereNotifier::openingSoon($enchere);         // clients flotte&enchere approuvés
        $owner = EnchereNotifier::openingSoonToOwner($enchere);  // vendeur (propriétaire)
        $enchere->forceFill(['reminder_sent_at' => $now])->save();
        $this->info("Enchère #{$enchere->id} : {$sent} email(s) clients" . ($owner ? ' + vendeur' : ' (vendeur non notifié)') . '.');
    }
}
