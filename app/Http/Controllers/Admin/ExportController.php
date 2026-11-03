<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GenericExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Export générique des listes admin vers Excel (.xlsx).
 * Une seule route pour tous les modules : admin.export({module}).
 */
class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /** Slug du module → modèle Eloquent exporté. */
    private const MODULES = [
        'clients'          => \App\Models\Client::class,
        'enchere-clients'  => \App\Models\Client::class,
        'annonces'         => \App\Models\Annonces::class,
        'brands'           => \App\Models\Brand::class,
        'models'           => \App\Models\Modele::class,
        'ville'            => \App\Models\Ville::class,
        'colors'           => \App\Models\Color::class,
        'songs'            => \App\Models\Song::class,
        'videoads'         => \App\Models\Videosads::class,
        'imgslider'        => \App\Models\Imgslider::class,
        'offres'           => \App\Models\Offre::class,
        'finances'         => \App\Models\Financement::class,
        'reprises'         => \App\Models\Reprise::class,
        'ventes'           => \App\Models\Vente::class,
        'users'            => \App\Models\User::class,
        'avistext'         => \App\Models\Avistext::class,
        'avisvideos'       => \App\Models\Avisvideos::class,
        'stories'          => \App\Models\Story::class,
        'social-posts'     => \App\Models\SocialPost::class,
        'articles'         => \App\Models\Article::class,
        'publications'     => \App\Models\Publication::class,
        'demandes'         => \App\Models\Demande::class,
        'estimations'      => \App\Models\Estimation::class,
        'nadoautoenchere'  => \App\Models\NadoAutoEnchere::class,
        'nadoautoencherebid' => \App\Models\NadoAutoEncheresBid::class,
        'accessoires'      => \App\Models\Accessoire::class,
        'autoenchere'      => \App\Models\Autoenchere::class,
        'entretiens'       => \App\Models\Entretien::class,
    ];

    /** Colonnes jamais exportées (sensibles / inutiles). */
    private const EXCLUDE = ['password', 'remember_token', 'otp_code', 'otp_expires_at'];

    /** Filtres par module : module => [colonne, valeur]. */
    private const FILTER = [
        'enchere-clients' => ['type', 'flotte&enchere'],
    ];

    /** En-têtes en français (colonne => libellé). Les colonnes absentes sont « humanisées ». */
    private const LABELS = [
        'id' => 'ID', 'client_id' => 'Client', 'user_id' => 'Utilisateur',
        'name' => 'Nom', 'nom' => 'Nom', 'email' => 'E-mail',
        'phone' => 'Téléphone', 'telephone' => 'Téléphone', 'company' => 'Société',
        'type' => 'Type', 'logo' => 'Logo',
        'mark' => 'Marque', 'brand_id' => 'Marque', 'modele' => 'Modèle', 'modele_id' => 'Modèle',
        'city' => 'Ville', 'ville_id' => 'Ville', 'color_id' => 'Couleur', 'colors' => 'Couleur',
        'year' => 'Année', 'year_min' => 'Année min', 'year_max' => 'Année max',
        'prix' => 'Prix', 'price_min' => 'Prix min', 'price_max' => 'Prix max',
        'km' => 'Kilométrage', 'km_max' => 'Km max',
        'boite' => 'Boîte', 'box' => 'Boîte', 'carburant' => 'Carburant',
        'etat' => 'État', 'origine' => 'Origine', 'cv' => 'CV fiscaux',
        'matricule' => 'Matricule', 'vin' => 'VIN / Châssis', 'pack_id' => 'Pack',
        'titre' => 'Titre', 'title' => 'Titre', 'description' => 'Description',
        'comment' => 'Commentaire', 'raw_query' => 'Recherche', 'source' => 'Source',
        'status' => 'Statut', 'active' => 'Actif', 'verified' => 'Vérifié',
        'is_approved' => 'Approuvé', 'is_verified' => 'Vérifié',
        'ae_approved' => 'Accès enchères', 'ae_role' => 'Rôle enchères',
        'certifie' => 'Certifié', 'pro' => 'Pro', 'particulie' => 'Particulier',
        'views' => 'Vues', 'views_count' => 'Vues', 'replies_count' => 'Réponses',
        'image' => 'Image', 'images' => 'Images', 'video' => 'Vidéo', 'link' => 'Lien',
        'sort_order' => 'Ordre', 'duration' => 'Durée', 'artist' => 'Artiste',
        'file_path' => 'Fichier', 'cover_path' => 'Pochette', 'video_key' => 'Vidéo',
        'ip' => 'IP', 'ip_hash' => 'IP', 'proposition' => 'Proposition',
        'expires_at' => 'Expire le', 'ends_at' => 'Fin le',
        'created_at' => 'Créé le', 'updated_at' => 'Mis à jour le', 'deleted_at' => 'Supprimé le',
        'email_verified_at' => 'E-mail vérifié le',
    ];

    /**
     * Colonnes clé-étrangère à remplacer par la vraie valeur :
     * colonne => [relation Eloquent, attribut affiché].
     */
    private const RESOLVE = [
        'annonces' => [
            'mark'    => ['brand', 'name'],
            'modele'  => ['modeleRelation', 'name'],
            'pack_id' => ['pack', 'name'],
        ],
        'nadoautoenchere' => [
            'mark'   => ['brand', 'name'],
            'modele' => ['modeleRelation', 'name'],
        ],
        'models' => [
            'brand_id' => ['brand', 'name'],
        ],
        'demandes' => [
            'client_id' => ['client', 'name'],
            'brand_id'  => ['brand', 'name'],
            'modele_id' => ['modele', 'name'],
            'ville_id'  => ['ville', 'name'],
            'color_id'  => ['color', 'name'],
        ],
    ];

    public function export(string $module)
    {
        abort_unless(isset(self::MODULES[$module]), 404);

        /** @var class-string<\Illuminate\Database\Eloquent\Model> $cls */
        $cls = self::MODULES[$module];
        $resolve = self::RESOLVE[$module] ?? [];
        $boolCols = $this->booleanColumns((new $cls)->getTable());

        $query = $cls::query();
        if (isset(self::FILTER[$module])) {
            [$col, $val] = self::FILTER[$module];
            $query->where($col, $val);
        }
        if ($resolve) {
            // Charge les relations nécessaires pour afficher les noms.
            $query->with(array_values(array_unique(array_map(fn ($r) => $r[0], $resolve))));
        }

        $rows = [];
        foreach ($query->get() as $record) {
            // attributesToArray() = colonnes seules (pas les relations imbriquées), $hidden respecté.
            $arr = $record->attributesToArray();
            foreach (self::EXCLUDE as $key) {
                unset($arr[$key]);
            }

            // Remplace les IDs des clés étrangères par la vraie valeur.
            foreach ($resolve as $col => [$rel, $attr]) {
                if (array_key_exists($col, $arr)) {
                    $related = $record->{$rel};
                    if ($related && isset($related->{$attr})) {
                        $arr[$col] = $related->{$attr};
                    }
                }
            }

            // Booléens (tinyint(1)) → Oui / Non (sauf colonnes déjà résolues en nom).
            foreach ($boolCols as $bc) {
                if (array_key_exists($bc, $arr) && ! isset($resolve[$bc])) {
                    $v = $arr[$bc];
                    $arr[$bc] = ($v === null || $v === '') ? '' : ((int) $v === 1 || $v === true ? 'Oui' : 'Non');
                }
            }

            // Les valeurs tableau (JSON castés) ne tiennent pas dans une cellule.
            foreach ($arr as $k => $v) {
                if (is_array($v)) {
                    $arr[$k] = json_encode($v, JSON_UNESCAPED_UNICODE);
                }
            }
            $rows[] = $arr;
        }

        $columns = $rows ? array_keys($rows[0]) : [];
        $headings = array_map(fn ($c) => self::LABELS[$c] ?? Str::ucfirst(str_replace('_', ' ', $c)), $columns);
        $data = array_map('array_values', $rows);

        $filename = $module . '_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new GenericExport($headings, $data), $filename);
    }

    /** Colonnes booléennes (tinyint(1)) de la table, pour l'affichage Oui/Non. */
    private function booleanColumns(string $table): array
    {
        try {
            $cols = DB::select(
                "SELECT COLUMN_NAME FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_TYPE LIKE 'tinyint(1)%'",
                [$table]
            );

            return array_map(fn ($c) => $c->COLUMN_NAME, $cols);
        } catch (\Throwable $e) {
            return [];
        }
    }
}
