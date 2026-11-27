<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seed d'une Publication par défaut pour la bannière "Expert NADO".
 *
 * La bannière sur l'accueil (entre logos & derniers véhicules) est gated
 * par l'existence d'une Publication active avec placement
 * "front-expert-banner" : sans cette ligne, elle serait invisible
 * tant qu'aucun admin n'en a créé une. Cette migration la crée une fois
 * à l'activation initiale ; les admins la désactivent/suppriment ensuite
 * librement depuis /admin/publications.
 *
 * Idempotent : ne fait rien si une ligne avec ce placement existe déjà.
 */
return new class extends Migration {
    public function up(): void
    {
        $exists = DB::table('publications')
            ->where('placement', 'front-expert-banner')
            ->exists();
        if ($exists) {
            return;
        }

        DB::table('publications')->insert([
            'title'      => 'Bannière Expert NADO (accueil)',
            'content'    => 'Bannière codée, image non utilisée. Désactiver cette publication pour masquer la bannière.',
            'image'      => null,
            'placement'  => 'front-expert-banner',
            // position : à la fin de la liste existante.
            'position'   => (int) DB::table('publications')->max('position') + 1,
            'status'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('publications')
            ->where('placement', 'front-expert-banner')
            ->delete();
    }
};
