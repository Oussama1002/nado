<?php

namespace App\Support;

use App\Models\Annonces;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;

/**
 * Source UNIQUE des filtres du catalogue achat.
 *
 * Utilisée par BuyController@index ET par le matcher des recherches
 * sauvegardées (NotifySavedSearches) : les résultats ne divergent jamais.
 *
 * Conserve le comportement historique de BuyController@index, et ajoute
 * (Phase 4) la recherche marque/modèle tolérante (alias FR/AR/EN + lignes où
 * mark/modele contient un ID ou un nom) et le filtre « mensualité max ».
 */
class AnnonceFilter
{
    /** Démarre une requête filtrée sur les annonces actives. */
    public static function query(array $p): Builder
    {
        return self::apply(Annonces::query()->where('active', 1), $p);
    }

    /** Applique les filtres utilisateur à une requête existante. */
    public static function apply(Builder $q, array $p): Builder
    {
        $get = fn ($k) => isset($p[$k]) ? trim((string) $p[$k]) : '';

        // ── Marque / modèle (alias-aware + ID|nom) ────────────────────────────
        if (($marque = $get('marque')) !== '') {
            self::applyBrand($q, str_replace('-', ' ', $marque));
        }
        if (($modele = $get('modele')) !== '') {
            self::applyModel($q, ucwords(str_replace('_', ' ', $modele)));
        }

        // ── Motorisation ──────────────────────────────────────────────────────
        if (($carburant = $get('carburant')) !== '') {
            $q->where('carburant', $carburant);
        }
        if (($box = $get('box')) !== '') {
            $q->where('box', $box);
        }

        // ── Localisation ──────────────────────────────────────────────────────
        if (($ville = $get('ville')) !== '') {
            $q->where('city', str_replace('_', ' ', $ville));
        }

        // ── Année ─────────────────────────────────────────────────────────────
        if (($aMin = (int) ($p['annee_min'] ?? 0)) > 0) {
            $q->where('year', '>=', $aMin);
        }
        if (($aMax = (int) ($p['annee_max'] ?? 0)) > 0) {
            $q->where('year', '<=', $aMax);
        }
        if (($annee = $get('annee')) !== '') {
            $q->where('year', $annee);
        }

        // ── Carrosserie ───────────────────────────────────────────────────────
        if (($bodywork = $get('bodywork')) !== '') {
            $q->where('bodywork', str_replace('-', ' ', $bodywork));
        }

        // ── Budget (prix max) + mensualité max convertie en prix ──────────────
        $prixMax = (isset($p['prix']) && $p['prix'] !== '' && $p['prix'] !== null) ? (int) $p['prix'] : null;

        // Mensualité max → prix max équivalent (même formule que le simulateur),
        // gated par discovery.monthly_filter pour ne rien changer si désactivé.
        if (feature_on('discovery.monthly_filter')) {
            $mensual = (isset($p['mensualite_max']) && $p['mensualite_max'] !== '') ? (int) $p['mensualite_max'] : 0;
            if ($mensual > 0 && function_exists('price_from_monthly')) {
                $budgetFromMonthly = price_from_monthly($mensual);
                $prixMax = $prixMax === null ? $budgetFromMonthly : min($prixMax, $budgetFromMonthly);
            }
        }

        if ($prixMax !== null) {
            $q->where('prix', '<=', $prixMax);
        }

        // ── Kilométrage max ───────────────────────────────────────────────────
        if (isset($p['km']) && $p['km'] !== '' && $p['km'] !== null) {
            $q->where('km', '<=', (int) $p['km']);
        }

        return $q;
    }

    /**
     * Filtre marque : tolérant aux alias (FR/AR/EN) et aux lignes où `mark`
     * contient un ID (lignes récentes) ou un nom (lignes migrées).
     */
    private static function applyBrand(Builder $q, string $marque): void
    {
        // Flag OFF → comportement historique strict (UPPER(mark) = marque).
        if (! feature_on('discovery.smart_search')) {
            $q->whereRaw('UPPER(mark) = ?', [mb_strtoupper($marque)]);
            return;
        }

        // Candidats de noms (saisie + alias canoniques).
        $names = self::expandAliases($marque);
        // Marque(s) en base correspondant à l'un de ces noms → récupère les IDs.
        $brandIds = Brand::query()
            ->where(function ($b) use ($names) {
                foreach ($names as $n) {
                    $b->orWhereRaw('UPPER(name) = ?', [mb_strtoupper($n)]);
                }
            })
            ->pluck('id')
            ->all();

        $namesUpper = array_map('mb_strtoupper', $names);

        $q->where(function ($w) use ($namesUpper, $brandIds) {
            $w->whereIn(\Illuminate\Support\Facades\DB::raw('UPPER(mark)'), $namesUpper);
            if (! empty($brandIds)) {
                $w->orWhereIn('mark', $brandIds);
            }
        });
    }

    /** Filtre modèle : nom exact OU ID (lignes récentes), tolérant. */
    private static function applyModel(Builder $q, string $modele): void
    {
        // Flag OFF → comportement historique strict (modele = valeur exacte).
        if (! feature_on('discovery.smart_search')) {
            $q->where('modele', $modele);
            return;
        }

        $modeleIds = \App\Models\Modele::query()
            ->whereRaw('UPPER(name) = ?', [mb_strtoupper($modele)])
            ->pluck('id')->all();

        $q->where(function ($w) use ($modele, $modeleIds) {
            $w->whereRaw('UPPER(modele) = ?', [mb_strtoupper($modele)]);
            if (! empty($modeleIds)) {
                $w->orWhereIn('modele', $modeleIds);
            }
        });
    }

    /**
     * Étend une saisie marque en ses variantes canoniques (FR/AR/EN) via
     * config/car_aliases.php. Renvoie toujours au moins la saisie elle-même.
     */
    public static function expandAliases(string $input): array
    {
        $input = trim($input);
        $map   = (array) config('car_aliases.brands', []);
        $out   = [$input];
        $needle = mb_strtolower($input);

        foreach ($map as $canonical => $aliases) {
            $all = array_map('mb_strtolower', array_merge([$canonical], (array) $aliases));
            if (in_array($needle, $all, true)) {
                // ajoute le canonique + tous les alias (noms recherchables en base)
                $out = array_merge($out, [$canonical], (array) $aliases);
            }
        }

        // Unicité, valeurs non vides.
        return array_values(array_unique(array_filter(array_map('trim', $out), fn ($v) => $v !== '')));
    }
}
