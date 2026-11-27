<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seed des Publications-switch pour les 5 bannières services de l'accueil :
 * demandes, estimation, financement, vérification légale, auto-enchère.
 *
 * Les bannières sont des mini-cartes codées (visuel inline dans front.blade).
 * Chaque Publication-switch n'a pas d'image : seul son flag `status` contrôle
 * la visibilité de sa carte respective. Les admins modifient status / order
 * librement depuis /admin/publications.
 *
 * Idempotent : ne recrée pas une ligne déjà présente pour un placement donné.
 */
return new class extends Migration {
    public function up(): void
    {
        $services = [
            ['front-demandes-banner', 'Bannière service « Demandes »'],
            ['front-estim-banner',    'Bannière service « Estimation »'],
            ['front-finance-banner',  'Bannière service « Financement »'],
            ['front-legal-banner',    'Bannière service « Vérification légale (NARSA) »'],
            ['front-ae-banner',       'Bannière service « Auto-Enchère B2B »'],
        ];

        foreach ($services as [$placement, $title]) {
            $exists = DB::table('publications')->where('placement', $placement)->exists();
            if ($exists) continue;

            DB::table('publications')->insert([
                'title'      => $title,
                'content'    => 'Bannière codée (image non utilisée). Désactiver pour masquer la carte.',
                'image'      => null,
                'placement'  => $placement,
                'position'   => (int) DB::table('publications')->max('position') + 1,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('publications')
            ->whereIn('placement', [
                'front-demandes-banner',
                'front-estim-banner',
                'front-finance-banner',
                'front-legal-banner',
                'front-ae-banner',
            ])
            ->delete();
    }
};
