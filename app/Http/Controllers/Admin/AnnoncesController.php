<?php

namespace App\Http\Controllers\Admin;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdsRequest;
use App\Models\Annonces;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Media;
use App\Models\Ville;
use Illuminate\Http\Request;
use App\Models\Accessoire;
use App\Models\Client;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
// use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Models\Autoenchere;
use Illuminate\Support\Facades\Auth;
class AnnoncesController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy', 'archive', 'restore', 'forceDelete']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function approval()
    {
        $pendingAnnonces = Annonces::where('active', false)->get();
    
        return view('admin.ads.approval', compact('pendingAnnonces'));
    }   
public function proposerPrix(Request $request, $id)
{
    if (!Auth::guard('client')->check()) {
        return redirect()->route('client.login')->with('error', 'Veuillez vous connecter pour proposer un prix.');
    }

    $request->validate([
        'proposition' => 'required|integer|min:1000',
    ]);

    $annonce = Annonces::findOrFail($id);
    $client  = Auth::guard('client')->user();

    Autoenchere::create([
        'annonce_id' => $annonce->id,
        'client_id' => $client->id,
        'proposition' => $request->input('proposition'),
    ]);

    // Relations can be null when the referenced brand/model row is missing, and
    // mark/modele hold ids on newer rows - fall back the same way the views do.
    $brandName = $annonce->brand?->name
        ?? (is_numeric($annonce->mark ?? null) ? null : $annonce->mark);
    $modelName = $annonce->modeleRelation?->name
        ?? (is_numeric($annonce->modele ?? null) ? null : $annonce->modele);
    $vehicule = trim(($brandName ?? '') . ' ' . ($modelName ?? '')) ?: ($annonce->titre ?: 'Vehicule');

    $payload = [
        'vehicule'     => $vehicule,
        'reference'    => 'VC' . $annonce->id,
        'annee'        => $annonce->year,
        'km'           => $annonce->km,
        'carburant'    => $annonce->carburant,
        'boite'        => $annonce->box ?? $annonce->boite_vitesse ?? null,
        'ville'        => $annonce->city,
        'prix_affiche' => $annonce->prix,
        'proposition'  => $request->input('proposition'),
        'client_nom'   => $client->name ?? null,
        'client_email' => $client->email ?? null,
        'client_tel'   => $client->phone ?? $client->tel ?? null,
        'date'         => now()->format('d/m/Y H:i'),
        // Lien du bouton "Voir l'annonce" dans l'email admin -> espace admin
        // des offres (plus utile que la fiche publique pour traiter la demande).
        'url'          => route('admin.autoenchere.index'),
    ];

    // The notification used to run FIRST and synchronously, so a slow or blocked SMTP
    // host hung the whole request (the form appeared to submit forever) and the offer
    // was never saved. Now: save first, then send after the response has been flushed
    // to the browser, with failures logged instead of surfacing to the visitor.
    // afterResponse() needs no queue worker - it runs in the same process on shutdown.
    dispatch(function () use ($payload) {
        try {
            Notification::route('mail', config('mail.admin_address', 'contact@nado.ma'))
                ->notify(new \App\Notifications\NouvellePropositionNotification($payload));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Offer notification failed: ' . $e->getMessage());
        }
    })->afterResponse();

    return back()->with('success', 'Votre proposition a ete envoyee avec succes');
}
public function toggleHomepage(Request $request)
{
    $ad = Annonces::findOrFail($request->id);

    // Update the is_homepage flag
    $ad->is_homepage = $request->is_homepage ? 1 : 0;
    $ad->save();

    // Optionally: clear cache if homepage ads are cached
    Cache::forget('homepage_ads');

    return response()->json(['success' => true]);
}

    public function index()
    {
        return view('admin.ads.index', ['ads' => Annonces::all(), 'brands' => Brand::all(), 'city' => ville::all(), 'colors' => color::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.ads.form', ['ads' => new Annonces, 'brands' => Brand::all(), 'city' => ville::all(), 'colors' => color::all()]);
    }

    
    public function approve($id)
    {
        $ad = Annonces::with('pack')->findOrFail($id);
        $ad->active = true;
        $ad->save();

        // Home page lists are cached 24h — refresh them so the annonce shows right away
        Cache::forget('similar_adsAll');
        Cache::forget('similar_adsp');
        Cache::forget('similar_ads_pro');
        Cache::forget('similar_ads');

        // Premium pack → débiter les tokens du propriétaire À L'APPROBATION, une
        // seule fois (premium_charged_at). Si le propriétaire n'est pas un client
        // ou n'a pas assez de tokens, on approuve quand même mais on prévient
        // l'admin (aucun premium offert en silence).
        $warning = $this->chargePremiumOnApproval($ad);

        $msg = back()->with('success', 'Annonce approuvée avec succès.');
        return $warning ? $msg->with('warning', $warning) : $msg;
    }

    /**
     * Charge the owning client for a premium pack when an annonce is approved.
     * Returns a warning string for the admin when the charge could not be taken
     * (no client account, or insufficient balance), else null.
     */
    private function chargePremiumOnApproval(Annonces $ad): ?string
    {
        if (! \App\Services\TokenService::enabled()) {
            return null;
        }
        if (! $ad->isPremiumPack() || $ad->isPremiumCharged()) {
            return null; // free pack, or already charged
        }

        $cost = \App\Services\TokenService::cost('annonce_premium');
        if ($cost <= 0) {
            return null;
        }

        $client = $ad->client; // resolved by phone
        if (! $client) {
            return "Annonce premium approuvée, mais le propriétaire ({$ad->phone}) n'a pas de compte client — aucun token débité.";
        }

        try {
            \App\Services\TokenService::spend($client, $cost, 'annonce_premium', $ad);
            $ad->premium_charged_at = now();
            $ad->save();
            return null;
        } catch (\App\Exceptions\InsufficientTokensException $e) {
            return "Annonce premium approuvée, mais {$client->name} n'a pas assez de tokens "
                . "({$e->available}/{$e->required}) — débit non effectué.";
        }
    }
    /**
     * Store a newly created resource in storage.
     */
 public function store(AdsRequest $request)
{
    $request->validate([
        'matricule' => 'nullable|unique:annonces,matricule',
    ]);
    $ads = $request->validated();

    $ads['is_homepage'] = $request->has('is_homepage') ? 1 : 0;
    $ads['active'] = 1;
    $ads['views'] = 0;

    // Annonces created by the admin always go to the "certifié" section,
    // never to "particuliers" / "pro" (those come from client annonces)
    $ads['certifie']   = 1;
    $ads['particulie'] = 0;
    $ads['pro']        = 0;

    $ads['images'] = [];

    if ($request->hasFile('rapport')) {
        $rapportName = ($ads['mark'] ?? 'ad').'-'.($ads['modele'] ?? 'model').'-'.rand(100000,999999).'.'.$request->rapport->extension();
        $request->rapport->move(storage_path('app/public/pdf/'), $rapportName);

        $ads['rapport'] = $rapportName; // ✅ ici $ads
    }

    Cache::forget('similar_adsAll');
    Cache::forget('similar_adsp');
    Cache::forget('similar_ads');

    $ad = Annonces::create($ads);

    if ($request->hasFile('uploaded_images')) {
        $slug = Str::slug(($ads['mark'] ?? 'ad').'-'.($ads['modele'] ?? 'model'), '-');
        $images = [];
        foreach ($request->file('uploaded_images') as $file) {
            $images[] = $this->resizeImage($file, $slug, $ad->id);
        }
        $ad->images = $images;
        $ad->save();
    }

    return to_route('annonces.index')->with('success', 'Success store brand');
}


    /**
     * Display the specified resource.
     */
    public function show($id)
{
    $annonce = Annonces::with(['media', 'entretiens'])->findOrFail($id);
 // Map database boolean/integer flags to human-readable labels
$labels = [
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
    'cockpit_digital' => 'Cockpit digital',
    'commandes_volant' => 'Commandes au volant',
    'commandes_vocales' => 'Commandes vocales',
    'reconnaissance_panneaux' => 'Reconnaissance de panneaux',
    'affichage_tete_haute' => 'Affichage Tête-Haute',
    'systeme_audio' => 'Système audio',
    'gps' => 'Navigation GPS',
    'wifi_bord' => 'WiFi à bord',
    'bluetooth' => 'Bluetooth',
    'compatibilite_smartphone' => 'Compatibilité smartphone',
    'apple_carplay_android_auto' => 'Apple CarPlay® & Android Auto®',
    'chargeur_sans_fil' => 'Chargeur/mobile sans fil',

    // Design & Extérieur
    'jantesA' => 'Jantes aluminium',
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


    $equipements = collect($labels)
        ->filter(function ($label, $field) use ($annonce) {
            // Accept truthy values (1, true, non-empty)
            return !empty($annonce->$field) && intval($annonce->$field) === 1;
        })
        ->values()
        ->all();
            $accessoires = Accessoire::where('status', true)->latest()->get();

    // annonces.detail is the public detail view: give it the same data as
    // BuyController::show so it also renders pending (inactive) annonces for the admin
    $clientTypes = \App\Models\ClientType::all();

    return view('annonces.detail', [
        'annonce'            => $annonce,
        'equipements'        => $equipements,
        'accessoires'        => $accessoires,
        'leftondetailBanner' => \App\Models\Publication::where('placement', 'leftondetail-banner')->where('status', true)->first(),
        'similar_ads'        => Annonces::where('id', '!=', $annonce->id)->where('status', 'disponible')->limit(6)->get(),
        'historique'         => [],
        'fraisDossier'       => \App\Models\FinancementSetting::where('key', 'frais_dossier')->value('value') ?? 4375,
        'assuranceAnnuelle'  => \App\Models\FinancementSetting::where('key', 'assurance_annuelle')->value('value') ?? 235,
        'tauxInteretDefault' => \App\Models\FinancementSetting::where('key', 'taux_interet_default')->value('value') ?? 13,
        'tauxInteret'        => 13,
        'videoAds'           => $annonce->videoAd ? collect([$annonce->videoAd]) : collect(),
        'clientTypes'        => $clientTypes,
        'formatDate'         => function ($date) {
            return \App\Http\Controllers\BuyController::formatDateLabel($date);
        },
    ]);
}

public function showAnnonce($id)
{
    $annonces = Annonces::find($id);

        return view('admin.ads.showAnnonce', [
            'ads' => $annonces,
            'brands' => Brand::all(),
            'city' => ville::all(),
            'colors' => color::all(),
        ]);
}
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $annonces = Annonces::find($id);

        return view('admin.ads.form', [
            'ads' => $annonces,
            'brands' => Brand::all(),
            'city' => ville::all(),
            'colors' => color::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(AdsRequest $request, $id)
{
       $ad = Annonces::findOrFail($id);

    $adsData = $request->safe()->except(['phone']); // exclude phone from bulk update
$adsData['is_homepage'] = $request->has('is_homepage') ? 1 : 0;

$checkboxes = [
    'active', 'garantie','certifie','showroom','finance','particulie','pro',
    // Sécurité
    'airbags','abs','esp','antipatinage','aide_freinage_urgence',
    'antidemarrage_electronique','aide_demarrage_cote','selecteur_mode_conduite',
    'detection_fatigue','maintien_dans_la_voie','detecteur_angle_mort',
    'detecteur_sous_gonflage','fermeture_portes_auto','preparation_isofix',
    'phares_antibrouillard','systeme_alarme',

    // Confort
    'climatisation','start_stop','regulateur_vitesse','detecteur_pluie',
    'allumage_auto_feux','frein_main_electrique','aide_stationnement',
    'volant_reglable','retros_electriques','retros_rabat_electriques',
    'coffre_electrique','sieges_electriques','sieges_elec_memoire','banquette_rabattable',

    // Multimédia
    'ecran_tactile','camera_recul','cockpit_digital',
    'commandes_volant','commandes_vocales','reconnaissance_panneaux',
    'affichage_tete_haute','systeme_audio','ordinateur','gps',
    'wifi_bord','bluetooth','compatibilite_smartphone',
    'apple_carplay_android_auto','chargeur_sans_fil',

    // Design & Extérieur
    'jantesA','sellerie_similicuir_tissu','volant_cuir','follow_me_home',
    'lumiere_ambiance','feux_jour_led','phares_full_led','toit_panoramique',
    'barres_de_toit','vitres_surteintees'
];


foreach ($checkboxes as $checkbox) {
    $ad->$checkbox = $request->has($checkbox) ? 1 : 0;
}


   // Update ad without touching phone/client relationship
   // NOTE: 'images' is excluded here on purpose — it is rebuilt below and assigned
   // directly to $ad. Leaving it in $adsData let an empty form field overwrite the
   // rebuilt list with [], silently wiping every photo on the ad.
$adsData = array_merge($adsData, $request->safe()->except(['phone', 'images']));

// Prevent overwriting with null
$adsData['mark']   = $request->input('mark')   ?? $ad->mark;
$adsData['modele'] = $request->input('modele') ?? $ad->modele;

// Dropdown fields. When a migrated annonce holds a value that isn't in the lookup
// table (e.g. colors = "Bleu Ravenna"), the <select> renders with nothing selected
// and the browser submits "". ConvertEmptyStringsToNull turns that into null, which
// then overwrites the stored value — a crash on NOT NULL columns (premiere_main),
// a silent erase on nullable ones (colors). An empty selection means "the form had
// no matching option", not "the user cleared this", so keep what is already stored.
$preserveIfEmpty = [
    'premiere_main', 'colors', 'city', 'carburant',
    'box', 'etat', 'origine', 'bodywork',
];
foreach ($preserveIfEmpty as $field) {
    if (empty($adsData[$field] ?? null)) {
        $adsData[$field] = $ad->$field;
    }
}

// Start from what the form says to keep; fall back to the current stored list.
// Only treat old_images as authoritative when the form actually submitted it —
// otherwise an edit form without that field would drop the existing photos.
if ($request->has('old_images')) {
    $images = (array) $request->input('old_images');
} elseif (is_array($ad->images)) {
    $images = $ad->images;
} elseif (is_string($ad->images) && $ad->images !== '') {
    $images = json_decode($ad->images, true) ?: [];
} else {
    $images = [];
}
$images = array_values(array_filter($images, fn ($i) => is_string($i) && trim($i) !== ''));

if ($request->hasFile('uploaded_images')) {
    $slug = Str::slug(($adsData['mark'] ?? $ad->mark ?? 'ad').'-'.($adsData['modele'] ?? $ad->modele ?? 'model'), '-');
    foreach ($request->file('uploaded_images') as $file) {
        $images[] = $this->resizeImage($file, $slug, $ad->id);
    }
}

$ad->images = $images;

// Handle rapport
if ($request->hasFile('rapport')) {
    $rapportName = ($adsData['mark'] ?? $ad->mark ?? 'ad').'-'.($adsData['modele'] ?? $ad->modele ?? 'model').'-'.rand(100000,999999).'.'.$request->rapport->extension();
    $request->rapport->move(storage_path('app/public/pdf/'), $rapportName);
    $adsData['rapport'] = $rapportName;
}

$ad->update($adsData);

// Home page lists are cached 24h — refresh them so the changes show right away
Cache::forget('similar_adsAll');
Cache::forget('similar_adsp');
Cache::forget('similar_ads_pro');
Cache::forget('similar_ads');

return to_route('annonces.index')->with('success', 'Success update brand');

}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Annonces::where('id', $id)->delete();

        return to_route('annonces.index')->with('success', 'Annonce deleted successfully');
    }

    /**
     * List the deleted (archived) annonces.
     */
    public function archive()
    {
        return view('admin.ads.archive', [
            'ads' => Annonces::onlyTrashed()->with(['brand', 'modeleRelation'])->latest('deleted_at')->get(),
        ]);
    }

    /**
     * Restore an archived annonce.
     */
    public function restore($id)
    {
        Annonces::onlyTrashed()->findOrFail($id)->restore();

        return to_route('admin.annonces.archive')->with('success', 'Annonce restaurée avec succès');
    }

    /**
     * Permanently delete an archived annonce and its images.
     */
    public function forceDelete($id)
    {
        $annonce = Annonces::onlyTrashed()->findOrFail($id);
        Media::where('annonces_id', $annonce->id)->delete();
        $annonce->forceDelete();

        return to_route('admin.annonces.archive')->with('success', 'Annonce supprimée définitivement');
    }

    /**
     * Remove the specified resource from storage image.
     */
    public function deleteImage($id)
    {
        $media = Media::find($id);
        $media->delete();

        return '1';
    }

    public function resizeImage($imagefile, $title, $idAds): string
    {
        $name = Str::slug($title, '-').'-'.rand(100000, 999999).'.'.$imagefile->getClientOriginalExtension();
        $manager = new ImageManager(new Driver);
        $manager->read($imagefile)->save(storage_path('app/public/ads/'.$name));
        $media = new Media;
        $media->name = $name;
        $media->annonces_id = $idAds;
        $media->save();
        return $name;
    }

    public function ImageCar($photo, $title)
    {
        $image = $photo;
        $imageName = Str::slug($title, '-').'-'.rand(100000, 999999).'.'.$image->getClientOriginalExtension();
        $manager = new ImageManager(new Driver);
        $img = $manager->read($image);
        $img->save(storage_path('app/public/ads/'.$imageName));

        return $imageName;
    }
}
