<?php

namespace App\Providers;

use App\Models\Setting;
use App\Observers\SettingObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Charge les helpers globaux settings()/feature_on() même sans
        // "composer dump-autoload" (certains hébergements n'ont pas composer).
        // Les fonctions sont protégées par function_exists → pas de double
        // définition si l'autoload composer les a déjà chargées.
        $helpers = base_path('app/helpers.php');
        if (is_file($helpers)) {
            require_once $helpers;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(125);

        // Journalise chaque changement de configuration (doc 02 — §6.bis.4).
        Setting::observe(SettingObserver::class);
    }
}
