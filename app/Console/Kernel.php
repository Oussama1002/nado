<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Rappel email aux clients flotte&enchere ~24h avant l'ouverture d'une enchère.
        $schedule->command('nado:enchere-reminders')->hourly()->withoutOverlapping();

        // Relance "déposez une caution" aux clients flotte&enchere non validés 10j après inscription.
        $schedule->command('nado:caution-reminders')->dailyAt('09:00')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
