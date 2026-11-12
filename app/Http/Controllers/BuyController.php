<?php

namespace App\Http\Controllers;

use App\Models\Annonces;
use App\Models\Brand;
use App\Models\Modele;
use App\Models\Offre;
use App\Models\Imgslider;
use App\Models\Accessoire;
use App\Models\Publication;
use App\Models\ClientType; // assuming this is your model
use App\Models\FinancementSetting; // assuming this is your model
use App\Models\Ville;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BuyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $firstBanner = Publication::where('placement', 'first-banner')->where('status', true)->first();
        $secondBanner = Publication::where('placement', 'second-banner')->where('status', true)->first();
        $thirdBanner = Publication::where('placement', 'third-banner')->where('status', true)->first();
        $leftondetailBanner = Publication::where('placement', 'leftondetail-banner')->where('status', true)->first();

        $brands = Cache::remember('brands', 86400, function () {
            return Brand::all();
        });
        $imgslider = Cache::remember('Imgslider', 86400, function () {
            return Imgslider::where('active', 1)->limit(6)->get();
        });

        // Source UNIQUE des filtres (partagée avec les recherches sauvegardées).
        $data = \App\Support\AnnonceFilter::apply(
            Annonces::with(['client', 'brand', 'modeleRelation'])
                ->where('active', 1)
                ->orderBy('status', 'ASC')
                ->orderBy('id', 'DESC'),
            $request->all()
        );

        // Modèles de la marque sélectionnée (données de la vue, pas un filtre).
        $models = collect();
        if ($marque = trim((string) $request->marque)) {
            $brand = Brand::whereRaw('UPPER(name) = ?', [mb_strtoupper(str_replace('-', ' ', $marque))])->first();
            if ($brand) {
                $models = Modele::where('brand_id', $brand->id)
                    ->orderBy('name', 'asc')
                    ->get(['id', 'name']);
            }
        }

        $paginated = $data->paginate(10)->withQueryString();

        // True when the search returned ads but every one of them is sold / unavailable.
        $noAvailable = $paginated->total() > 0
            && $paginated->getCollection()->every(fn ($a) => strtolower((string) $a->status) !== 'disponible');

        // IDs favoris du client connecté (pour l'état initial du cœur sur les cartes).
        $favIds = [];
        if ($client = \Illuminate\Support\Facades\Auth::guard('client')->user()) {
            $favIds = $client->favorites()->pluck('annonces.id')->all();
        }

        return view('annonces.list', [
            'ads'                => $paginated,
            'count'              => $paginated->total(),
            'noAvailable'        => $noAvailable,
            'favIds'             => $favIds,
            'imgslider'          => $imgslider,
            'brands'             => $brands,
            'firstBanner'        => $firstBanner,
            'secondBanner'       => $secondBanner,
            'thirdBanner'        => $thirdBanner,
            'leftondetailBanner' => $leftondetailBanner,
            'ville'              => Ville::all(),
            'searchKeys'         => $request->all(),
            'models'             => $models,
            'formatDate'         => function ($date) {
                return $this->formatDateLabel($date);
            },
        ])->with($paginated->total() === 0 ? ['danger' => 'Aucune voiture trouvée pour cette recherche.'] : []);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $offre = $request->validate([
            'car' => ['required', 'string'],
            'prix' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'idads' => ['required', 'numeric'],
            'offre' => ['required', 'string'],
            'status' => ['nullable'],

        ]);

        Offre::create($offre);

        return back()->with('success', 'Votre demande a ete envoyer');
    }
    public function getModelsByBrand($brandId)
    {
        $models = \App\Models\Modele::where('brand_id', $brandId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json($models);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($marque, $modele, $id)
    {
        $fraisDossier = FinancementSetting::where('key', 'frais_dossier')->value('value') ?? 4375;
        $assuranceAnnuelle = FinancementSetting::where('key', 'assurance_annuelle')->value('value') ?? 235;
        $tauxInteretDefault = FinancementSetting::where('key', 'taux_interet_default')->value('value') ?? 13;

        $leftondetailBanner = Publication::where('placement', 'leftondetail-banner')->where('status', true)->first();

        // Get the annonce (single object), bypass cache or update cache after increment
        $annonce = Annonces::where('active', 1)->where('id', $id)->firstOrFail();

        // Increment views count by 1 for this annonce
        $annonce->increment('views');

        // Cache similar ads (unchanged)
        $similar_adsAll = Cache::remember('similar_adsAll', 86400, function () use ($id) {
            return Annonces::where('id', '!=', $id)->where('status', 'disponible')->limit(6)->get();
        });

        $featuresMap = [
            // Sécurité
            'airbags' => 'Airbags',
            'abs' => 'ABS',
            'esp' => 'ESP',
            'antipatinage' => 'Antipatinage',
            'aide_freinage_urgence' => "Aide au freinage d'urgence",
            'antidemarrage' => 'Antidémarrage électronique',
            'aide_demarrage_cote' => "Aide au démarrage en côte",
            'selecteur_mode_conduite' => 'Sélecteur de mode de conduite',
            'detection_fatigue' => 'Détection de fatigue',
            'maintien_voie' => 'Maintien dans la voie',
            'detecteur_angle_mort' => "Détecteur d'angle mort",
            'detecteur_sous_gonflage' => "Détecteur de sous-gonflage",
            'fermeture_auto' => 'Fermeture de portes auto.',
            'isofix' => 'Préparation ISOFIX',
            'phares_antibrouillard' => 'Phares antibrouillard',
            'alarme' => 'Système d’alarme',

            // Confort
            'climatisation' => 'Climatisation',
            'start_stop' => 'Start & Stop',
            'regulateur_vitesse' => 'Régulateur de vitesse',
            'detecteur_pluie' => 'Détecteur de pluie',
            'allumage_auto' => 'Allumage auto. des feux',
            'frein_main_electriques' => 'Frein à main électrique',
            'aide_stationnement' => 'Aide au stationnement',
            'volant_reglable' => 'Volant réglable',
            'vitres_electriques' => 'Vitres électriques',
            'retros_electriques' => 'Rétros. électriques',
            'retros_rabat_electriques' => 'Rétros. rabattables électriques',
            'coffre_electriques' => 'Coffre électrique',
            'sieges_electriques' => 'Sièges électriques',
            'sieges_mem' => 'Sièges élec. avec mémoire',
            'banquette_rab' => 'Banquette arrière rabattable 1/3-2/3',

            // Multimédia
            'ecran_tactile' => 'Écran tactile',
            'camera_recul' => 'Caméra de recul',
            'park_assist' => 'Park Assist Auto.',
            'cockpit_digital' => 'Cockpit digital',
            'commandes_volant' => 'Commandes au volant',
            'commandes_vocales' => 'Commandes vocales',
            'reconnaissance_panneaux' => 'Reconnaissance de panneaux',
            'affichage_tete_haute' => 'Affichage Tête-Haute',
            'systeme_audio' => 'Système audio',
            'ordinateur_bord' => 'Ordinateur de bord',
            'gps' => 'Navigation GPS',
            'wifi_bord' => 'WiFi à bord',
            'bluetooth' => 'Bluetooth',
            'compatibilite_smartphone' => 'Compatibilité smartphone',
            'apple_carplay_android_auto' => 'Apple CarPlay® & Android Auto®',
            'chargeur_sans_fil' => 'Chargeur/mobile sans fil',

            // Design & Extérieur
            'jantes_aluminium' => 'Jantes aluminium',
            'sellerie_similicuir_tissu' => 'Sellerie Similicuir / Tissu',
            'volant_cuir' => 'Volant cuir',
            'follow_me_home' => 'Follow-me home',
            'lumiere_ambiance' => "Lumière d'ambiance",
            'feux_jour_led' => 'Feux de jour LED',
            'phares_full_led' => 'Phares Full LED',
            'toit_panoramique' => 'Toit Panoramique ouvrant',
            'barres_de_toit' => 'Barres de toit',
            'vitres_surteintees' => 'Vitres sur-teintées',
        ];


        $equipements = [];
        foreach ($featuresMap as $key => $label) {
            if ($annonce->$key) {
                $equipements[] = $label;
            }
        }
        $clientTypes = ClientType::all();
        $tauxInteret = $clientTypes->all()?->taux_interet ?? 13;
        $videoAds = $annonce->videoAd ? collect([$annonce->videoAd]) : collect();




        $accessoires = Accessoire::where('status', true)->latest()->get();

        $historique_entretien = [
            ['date' => '2024-05-10', 'type' => 'Vidange', 'garage' => 'Total Maroc - Casablanca', 'km' => '120 000'],
            ['date' => '2023-12-20', 'type' => 'Remplacement plaquettes', 'garage' => 'Midas Ain Sebaa', 'km' => '110 500'],
            ['date' => '2023-08-01', 'type' => 'Contrôle technique', 'garage' => 'CT Nador Auto', 'km' => '105 000'],
        ];

        return view('annonces.detail', [
            'annonce' => $annonce,
            'accessoires' => $accessoires,
            'leftondetailBanner' => $leftondetailBanner,
            'equipements' => $equipements,
            'similar_ads' => $similar_adsAll,
            'historique' => $historique_entretien,
            'fraisDossier' => $fraisDossier,
            'assuranceAnnuelle' => $assuranceAnnuelle,
            'tauxInteretDefault' => $tauxInteretDefault,
            'tauxInteret' => $tauxInteret,
            'videoAds' => $videoAds, // ✅ Added here

            'clientTypes' => $clientTypes,

            'formatDate' => function ($date) {
                return $this->formatDateLabel($date);
            }
        ]);
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public static function formatDateLabel($date)
    {

        $datediff = time() - strtotime($date);
        $tm = 60;
        $res = round($datediff / $tm);
        $tt = 'minutes';

        if ($res == 0) {
            $res = 1;
        }

        if ($res > 50) {
            $res = round($datediff / ($tm * 60));
            $tt = 'heures';
            if ($res > 23) {
                $res = round($datediff / ($tm * 60 * 24));
                $tt = 'jours';
                if ($res > 30) {
                    $res = round($datediff / ($tm * 60 * 24 * 30));
                    $tt = 'mois';
                    if ($res >= 12) {
                        $res = round($datediff / ($tm * 60 * 24 * 30 * 12));
                        $tt = 'ans';
                    }
                }
            }
        }

        return 'Il y a ' . $res . ' ' . $tt;
    }

    public function incrementViews($ads) // annonces $ads
    {
        Annonces::where('id', $ads)->increment('views');

        // Cache::forget('ads_'.$ads);
    }
}
