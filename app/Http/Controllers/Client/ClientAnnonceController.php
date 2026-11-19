<?php

namespace App\Http\Controllers\Client;
use App\Models\Annonces;
use App\Models\Pack;

use Illuminate\Support\Facades\Auth;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Color;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Http\Requests\AdsRequest;
use Illuminate\Support\Facades\Cache;
use App\Models\Media;
use Illuminate\Support\Str;

use App\Models\Client;

use App\Models\Modele;
use App\Models\Ville;


class ClientAnnonceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    $clientPhone = Auth::guard('client')->user()->phone;

    // brand / modeleRelation: annonces store the brand & model ids in mark / modele
    // media: photo rows (the images column exists in two formats, see photoUrls())
    $annonces = Annonces::with(['brand', 'modeleRelation', 'media'])
        ->where('phone', $clientPhone)
        ->where('status', 'disponible')
        ->latest()
        ->get();

    foreach ($annonces as $annonce) {
        $annonce->brand_name  = $annonce->brand->name ?? (is_numeric($annonce->mark) ? '' : $annonce->mark);
        $annonce->modele_name = $annonce->modeleRelation->name ?? (is_numeric($annonce->modele) ? '' : $annonce->modele);
        $annonce->photo_urls  = $this->photoUrls($annonce);
    }

    return view('client.annonces.index', compact('annonces'));
}


    /**
     * Public URLs of an annonce's photos (storage/app/public/ads).
     * `images` is an array for admin / vente-modal annonces, but a JSON string
     * (encoded twice) for annonces created by store() below — handle both, and fall
     * back to the media rows.
     */
    private function photoUrls(Annonces $annonce): array
    {
        $images = $annonce->images;
        for ($i = 0; $i < 2 && is_string($images); $i++) {
            $images = json_decode($images, true);
        }
        $names = is_array($images) ? array_filter($images, 'is_string') : [];

        if (empty($names)) {
            $names = $annonce->media->pluck('name')->filter()->all();
        }

        return array_values(array_map(fn ($n) => asset('storage/ads/' . $n), $names));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::all();
        $models = modele::all();
        $packs = Pack::all();
        $colors = Color::all();

        $villes = Ville::ordered()->get(); // ← ici on récupère les villes



        // récupère toutes les marques depuis la BDD
        return view('client.annonces.create', compact('brands','models', 'packs', 'villes','colors'));
    }
    public function proposerPrix(Request $request, $id)
{
    Notification::route('mail', 'oussamaelhadi11@gmail.com')
    ->notify(new AdminNotification(
        'Nouvelle autoenchère',
        'Une nouvelle autoenchère a été ajoutée.'
    ));
    $request->validate([
        'prix_propose' => 'required|numeric|min:0'
    ]);

    // Sauvegarder ou traiter le prix proposé (exemple basique)
    // Tu peux créer un modèle `PrixPropose` pour le stocker en base
    return back()->with('success', 'Votre prix a été soumis avec succès.');
}

    /**
     * Store a newly created resource in storage.
     */
public function store(AdsRequest $request)
{
    $ads = $request->validated();

    // Labels for equipment (keep as in your original)
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

$videoPath = null;

if ($request->hasFile('video')) {
    $videoPath = $request->file('video')->store('annonces/videos', 'public');
    $ads['video'] = $videoPath;  // ✅ SAVE IT HERE
}


    $equipements = collect($labels)
        ->filter(fn($label, $field) => !empty($ads->$field) && intval($ads->$field) === 1)
        ->values()
        ->all();

    // Base ad data
    $client = Auth::guard('client')->user();
    $ads['phone'] = $client->phone;
    $ads['verified'] = false;
    $ads['is_homepage'] = 0;
    $ads['active'] = 0;
    $ads['views'] = 0;
    $ads['status'] = 'disponible';
    $ads['pack_id'] = $request->pack_id;

    // Route the annonce to the correct section based on the client's type.
    // Admin annonces are "certifie"; client annonces split between "particulie" and "pro".
    $ads['certifie']   = 0;
    $ads['particulie'] = $client->type === 'particulier' ? 1 : 0;
    $ads['pro']        = in_array($client->type, ['pro', 'flotte&enchere'], true) ? 1 : 0;
  // ✅ FIX: Normalize uploaded_images to array
// ✅ Handle uploaded images from Dropzone
    $uploaded = $request->input('uploaded_images', []); // array of filenames
    $images = [];

if (!empty($uploaded)) {
    foreach ($uploaded as $filename) {
        $tempPath = "temp/{$filename}";
        if (Storage::disk('public')->exists($tempPath)) {
            $finalPath = "ads/{$filename}";
            Storage::disk('public')->move($tempPath, $finalPath);
            $images[] = $filename;
        }
    }
}

    // Handle rapport file
    if ($request->hasFile('rapport')) {
        $rapportName = $ads['mark'].'-'.$ads['modele'].'-'.rand(100000,999999).'.'.$request->rapport->extension();
        $request->rapport->move(storage_path('app/public/pdf/'), $rapportName);
        $ads['rapport'] = $rapportName;
    }

$ads['images'] = $images;

$annonce = Annonces::create(array_merge($ads, [
    'images' => json_encode($images, JSON_UNESCAPED_SLASHES)
]));

    // Resize images like admin side
    if (!empty($images)) {
        foreach ($images as $filename) {
            $filePath = storage_path("app/public/ads/{$filename}");
            if (file_exists($filePath)) {
                $this->resizeImage(new \Illuminate\Http\File($filePath), $ads['mark'].'-'.$ads['modele'], $annonce->id);
            }
        }
    }
    // Save images JSON to DB
  // just ensure DB value is JSON encoded
    // $annonce->images = json_encode($images, JSON_UNESCAPED_SLASHES);
    // $annonce->save();

    // Notifier l'admin : nouvelle annonce à valider sur l'espace d'approbation.
    // Même format d'email que les propositions / enchères (NouvellePropositionNotification),
    // envoi après-réponse pour que l'utilisateur n'attende jamais le SMTP.
    $client    = Auth::guard('client')->user();
    $brandName = $annonce->brand?->name
        ?? (is_numeric($annonce->mark ?? null) ? null : $annonce->mark);
    $modelName = $annonce->modeleRelation?->name
        ?? (is_numeric($annonce->modele ?? null) ? null : $annonce->modele);
    $vehicule  = trim(($brandName ?? '').' '.($modelName ?? '')) ?: ($annonce->titre ?: 'Annonce #'.$annonce->id);

    $notifPayload = [
        // Libellés spécifiques au flux "annonce à valider".
        'title'        => 'Nouvelle annonce à valider',
        'intro'        => 'Un client vient de soumettre une annonce. Elle est en attente de validation.',
        'action_text'  => "Voir dans l'espace d'approbation",
        'vehicule'     => $vehicule,
        'reference'    => 'VC'.$annonce->id,
        'annee'        => $annonce->year,
        'km'           => $annonce->km,
        'carburant'    => $annonce->carburant,
        'boite'        => $annonce->box ?? $annonce->boite_vitesse ?? null,
        'ville'        => $annonce->city,
        'prix_affiche' => $annonce->prix,
        'proposition'  => $annonce->prix, // pas de contre-offre : on affiche le prix demandé
        'client_nom'   => $client->name ?? null,
        'client_email' => $client->email ?? null,
        'client_tel'   => $client->phone ?? $client->tel ?? null,
        'date'         => now()->format('d/m/Y H:i'),
        'url'          => route('admin.annonces.approval'),
    ];

    dispatch(function () use ($notifPayload) {
        try {
            Notification::route('mail', config('mail.admin_address', 'contact@nado.ma'))
                ->notify(new \App\Notifications\NouvellePropositionNotification($notifPayload));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('New-ad notification failed: '.$e->getMessage());
        }
    })->afterResponse();

    // Clear cache
    Cache::forget('similar_adsAll');
    Cache::forget('similar_adsp');
    Cache::forget('similar_ads');

    return redirect()->route('client.dashboard')
        ->with('success', "Votre annonce a été soumise. Elle sera visible après validation par l'administrateur.");
}

  public function resizeImage($imagefile, $title, $idAds)
    {

        $media = new Media;
        $image = $imagefile;
        $name = Str::slug($title, '-').'-'.rand(100000, 999999).'.'.$image->getClientOriginalExtension();
        $manager = new ImageManager(new Driver);
        $img = $manager->read($image);
        $img->save(storage_path('app/public/ads/'.$name));
        $media->name = $name;
        $media->annonces_id = $idAds;
        $media->save();
    }

public function uploadTemp(Request $request)
{
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        // Save to temp folder inside public disk
        $path = $file->storeAs('temp', $filename, 'public');
        return response()->json(['filename' => $filename, 'path' => $path]);
    }
    return response()->json(['error' => 'No file uploaded'], 400);
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
  public function edit($id)
{
    $annonce = Annonces::findOrFail($id);
    $brands = Brand::all();
    $models = Modele::all();
    $packs = Pack::all();
    $colors = Color::all();
    $villes = Ville::ordered()->get();

    return view('client.annonces.edit', compact(
        'annonce',
        'brands',
        'models',
        'packs',
        'colors',
        'villes'
    ));
}


    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, $id)
{
    $annonce = Annonces::findOrFail($id);

    $request->validate([
        'titre' => 'required|string|max:255',
        'prix' => 'required|numeric',
        'pack_id' => 'required|exists:packs,id',
        'mark' => 'required|exists:brands,id',
        'modele' => 'required',
    ]);

    $brand = Brand::find($request->mark);
    $modele = Modele::find($request->modele);

    $brandName = $brand?->name ?? $request->mark;
    $modeleName = $modele?->name ?? $request->modele;
  // Take only the fields you want to update
    $data = $request->only([
        'titre','prix','pack_id','year','km','carburant','box','etat','nb_Door','city','cv','origine','premiere_main','colors'
    ]);
    $data = $request->all();
    $data['active'] = 0;

    $data['mark'] = $brandName;
    $data['modele'] = $modeleName;
$annonce->pack_id = $request->pack_id;
$annonce->save();

    // Upload images only if new files are provided
    foreach (['image','imgrleft','imgrtop','imgrmed','imgrbottom','imgrright','imginterior'] as $imgField) {
        if ($request->hasFile($imgField)) {
            $file = $request->file($imgField);
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/ads', $filename);
            $data[$imgField] = $filename;
        } else {
            // Keep the old image if no new file uploaded
            $data[$imgField] = $annonce->$imgField;
        }
    }

    $annonce->update($data);

    return redirect()->route('client.annonces.index')
        ->with('success', 'Annonce mise à jour. Elle est maintenant en attente de validation par notre équipe.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Only the owner (annonces are linked to clients by phone) can delete
        $annonce = Annonces::where('id', $id)
            ->where('phone', Auth::guard('client')->user()->phone)
            ->firstOrFail();

        $annonce->delete(); // soft delete (Annonces uses SoftDeletes)

        // Home page lists are cached
        Cache::forget('similar_adsAll');
        Cache::forget('similar_adsp');
        Cache::forget('similar_ads_pro');
        Cache::forget('similar_ads');

        return back()->with('success', 'Annonce supprimée.');
    }
}
