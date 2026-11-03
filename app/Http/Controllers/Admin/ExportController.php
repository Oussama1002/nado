<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GenericExport;
use App\Http\Controllers\Controller;
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

            // Les valeurs tableau (JSON castés) ne tiennent pas dans une cellule.
            foreach ($arr as $k => $v) {
                if (is_array($v)) {
                    $arr[$k] = json_encode($v, JSON_UNESCAPED_UNICODE);
                }
            }
            $rows[] = $arr;
        }

        $headings = $rows ? array_keys($rows[0]) : [];
        $data = array_map('array_values', $rows);

        $filename = $module . '_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new GenericExport($headings, $data), $filename);
    }
}
