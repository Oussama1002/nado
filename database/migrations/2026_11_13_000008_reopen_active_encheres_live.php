<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rouvre les enchères existantes pour qu'elles soient tout de suite ouvertes aux
 * offres (modèle « ouvert » comme l'ancien système), maintenant que l'enchère
 * live est activée.
 *
 * Pour chaque enchère active NON annulée :
 *   - statut → 'live' (sinon l'UI affiche « Terminé » et masque le formulaire) ;
 *   - si la date de fin est absente OU déjà passée → on la met à NULL
 *     (enchère sans minuteur, ouverte jusqu'à clôture manuelle par l'admin).
 *   - une date de fin FUTURE est conservée (enchère chronométrée volontaire).
 *
 * Réversible (down = ne fait rien de destructif ; on ne restaure pas les dates
 * effacées car elles étaient périmées).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('nado_auto_encheres')) {
            return;
        }

        $now = Carbon::now();

        DB::table('nado_auto_encheres')
            ->where('active', 1)
            ->where(function ($q) {
                $q->whereNull('status')->orWhereNotIn('status', ['cancelled']);
            })
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($now) {
                foreach ($rows as $row) {
                    $update = ['status' => 'live'];

                    $endsPast = empty($row->ends_at)
                        || $now->greaterThan(Carbon::parse($row->ends_at));
                    if ($endsPast) {
                        $update['ends_at'] = null;               // ouverte, sans minuteur
                    }

                    // Une date de début future bloquerait l'ouverture → on la neutralise.
                    if (! empty($row->starts_at) && $now->lessThan(Carbon::parse($row->starts_at))) {
                        $update['starts_at'] = null;
                    }

                    DB::table('nado_auto_encheres')->where('id', $row->id)->update($update);
                }
            });
    }

    public function down(): void
    {
        // Pas de restauration : les dates effacées étaient périmées.
    }
};
