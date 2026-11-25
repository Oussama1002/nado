<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * publications.placement était un ENUM figé → impossible d'ajouter de nouveaux
 * emplacements (vente-hero, ae-expertise, reprise-hero, front-estimation) sans
 * migration de schéma. On le convertit en VARCHAR(64) pour que la validation
 * applicative (PublicationController) soit la seule source de vérité.
 */
return new class extends Migration {
    public function up(): void
    {
        // Nécessaire sur anciennes versions de MySQL lorsque la table est
        // en ENUM : un simple Schema::change ne gère pas les ENUM. On passe
        // en SQL brut pour forcer la bascule.
        DB::statement("ALTER TABLE publications MODIFY COLUMN placement VARCHAR(64) NOT NULL");
    }

    public function down(): void
    {
        // Retour volontairement simple : on ne reconstitue pas l'ENUM
        // (si on l'avait fait, on aurait rejeté les lignes avec un placement
        // hors de la liste d'origine). VARCHAR(64) couvre tous les cas.
        DB::statement("ALTER TABLE publications MODIFY COLUMN placement VARCHAR(64) NOT NULL");
    }
};
