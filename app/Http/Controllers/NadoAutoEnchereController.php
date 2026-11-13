<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\NadoAutoEnchere;
use App\Models\Brand;
use App\Models\Modele;
use App\Models\Publication;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProposerPrixMail;
use App\Mail\AdminProposerPrixMail;

use App\Models\Ville;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Models\NadoAutoEncheresBid; // new model
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ClientType; // assuming this is your model

use App\Models\Color;
class NadoAutoEnchereController extends Controller
{
public function index(Request $request)
{
    $rightonindexenchereBanner = Publication::where('placement', 'rightonindexenchere-banner')
        ->where('status', true)->first();

    $query = NadoAutoEnchere::where('active', 1);

    // Brand & Model (IDs)
    if ($request->filled('mark')) {
        $query->where('mark', $request->mark);
    }
    if ($request->filled('modele')) {
        $query->where('modele', $request->modele);
    }

    // Category (phone)
    if ($request->filled('category')) {
        $category = str_replace(' ', '', $request->category);
        $query->whereRaw('REPLACE(phone," ","") LIKE ?', ["%{$category}%"]);
    }

    // Other filters
    if ($request->filled('year'))       $query->where('year', $request->year);
    if ($request->filled('carburant'))  $query->where('carburant', $request->carburant);
    if ($request->filled('box'))        $query->where('box', $request->box);
    if ($request->filled('etat'))       $query->where('etat', $request->etat);
    if ($request->filled('city'))       $query->where('city', $request->city);
    if ($request->filled('prix_min'))   $query->where('prix', '>=', $request->prix_min);
    if ($request->filled('prix_max'))   $query->where('prix', '<=', $request->prix_max);
    if ($request->filled('km_min'))     $query->where('km', '>=', $request->km_min);
    if ($request->filled('km_max'))     $query->where('km', '<=', $request->km_max);

    $encheres = $query->get();

    // Remaining time
    foreach ($encheres as $enchere) {
        $enchere->remaining = $enchere->ends_at ? Carbon::parse($enchere->ends_at)->diff(Carbon::now()) : null;
    }

    // Company context when browsing by category (phone)
    $company = null;
    if ($request->filled('category')) {
        $rawPhone = str_replace(' ', '', $request->category);
        $client = \App\Models\Client::whereRaw('REPLACE(phone," ","") LIKE ?', ["%{$rawPhone}%"])->first();
        if ($client) {
            $company = (object) [
                'name'  => $client->company ?? $client->name ?? 'NADO.ma',
                'city'  => $client->city ?? ($encheres->first()->city ?? '—'),
                'phone' => $client->phone,
            ];
        }
    }

    return view('nadoautoenchere.index', [
        'encheres' => $encheres,
        'company'  => $company,
        'brands'   => Brand::all(),
        'modeles'  => Modele::all(),
        'cities'   => Ville::pluck('name'),
        'rightonindexenchereBanner' => $rightonindexenchereBanner,
    ]);
}

/**
 * Enregistre une « demande de vente » (véhicules soumis depuis le modal
 * « Soumettre mes véhicules » de /nadoautoenchere/vendre). L'UI continue
 * d'ouvrir WhatsApp ; ici on persiste la demande pour la liste admin.
 */
public function storeSellRequest(Request $request)
{
    $data = $request->validate([
        'name'              => ['nullable', 'string', 'max:120'],
        'phone'             => ['nullable', 'string', 'max:30'],
        'message'           => ['nullable', 'string', 'max:1000'],
        'vehicles'          => ['required', 'array', 'min:1', 'max:30'],
        'vehicles.*.marque' => ['nullable', 'string', 'max:80'],
        'vehicles.*.modele' => ['nullable', 'string', 'max:80'],
        'vehicles.*.annee'  => ['nullable', 'string', 'max:10'],
        'vehicles.*.km'     => ['nullable', 'string', 'max:15'],
        'vehicles.*.etat'   => ['nullable', 'string', 'max:40'],
    ]);

    $client = Auth::guard('client')->user();

    $req = \App\Models\EnchereSellRequest::create([
        'client_id' => $client?->id,
        'name'      => $data['name']  ?? $client?->name,
        'phone'     => $data['phone'] ?? $client?->phone,
        'message'   => $data['message'] ?? null,
        'vehicles'  => array_values($data['vehicles']),
        'status'    => 'new',
    ]);

    // Notifie l'équipe (best-effort, après réponse — QUEUE=sync).
    dispatch(function () use ($req) {
        try {
            $lines = ["Nouvelle demande de vente Auto-Enchère (#{$req->id}).", ''];
            $lines[] = 'Vendeur : ' . ($req->name ?: '—') . ' — ' . ($req->phone ?: '—');
            $lines[] = 'Véhicules : ' . $req->vehicleCount();
            foreach ((array) $req->vehicles as $i => $v) {
                $lines[] = '  ' . ($i + 1) . '. ' . trim(($v['marque'] ?? '') . ' ' . ($v['modele'] ?? '') . ' ' . ($v['annee'] ?? ''))
                    . (($v['km'] ?? '') !== '' ? ' — ' . $v['km'] . ' km' : '')
                    . (($v['etat'] ?? '') !== '' ? ' — ' . $v['etat'] : '');
            }
            if ($req->message) { $lines[] = ''; $lines[] = 'Message : ' . $req->message; }
            \Illuminate\Support\Facades\Mail::raw(implode("\n", $lines), function ($m) use ($req) {
                $m->to('contact@nado.ma')->subject('Auto-Enchère — demande de vente #' . $req->id);
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Sell request email failed: ' . $e->getMessage());
        }
    })->afterResponse();

    return response()->json(['success' => true, 'id' => $req->id]);
}

public function searchCompanies(Request $request)
{
    $search = $request->get('q');

    $results = \App\Models\Client::where('company', 'like', "%{$search}%")
        ->select('id', 'company')
        ->limit(10)
        ->get();

    return response()->json($results);
}

 public function categories(Request $request)
{
    $query = NadoAutoEnchere::where('active', 1);

    // 🔹 Apply filters BEFORE grouping
    if ($request->filled('company')) {
        $query->whereHas('client', function($q) use ($request) {
            $q->where('company', 'like', '%' . $request->company . '%');
        });
    }

    if ($request->filled('mark')) {
        $query->where('mark', $request->mark);
    }

    if ($request->filled('modele')) {
        $query->where('modele', $request->modele);
    }

    if ($request->filled('city')) {
        $query->where('city', $request->city);
    }

    // 🔹 Now group by phone AFTER filters
    $categories = $query->select('phone')
        ->selectRaw('COUNT(*) as cars_count')
        ->groupBy('phone')
        ->get();

    // Attach client info for each phone
    foreach ($categories as $category) {
        $firstClient = \App\Models\Client::where('phone', $category->phone)->first();

        $category->client_name = $firstClient->name ?? 'NADO.ma';
        $category->company     = $firstClient->company ?? null;
        $category->logo        = $firstClient->logo ?? 'assets/images/logo.png';
    }

    $brands = Brand::all();
    $cities = Ville::pluck('name');

    return view('nadoautoenchere.categories', compact('categories', 'brands', 'cities'));
}

public function filterCategories(Request $request)
{
    $query = NadoAutoEnchere::where('active', 1);

    if ($request->filled('company')) {
        $query->whereHas('client', function($q) use ($request) {
            $q->where('company', 'like', '%' . $request->company . '%');
        });
    }

   if ($request->filled('mark')) {
    $query->where('mark', $request->mark); // now mark = brand_id
}
if ($request->filled('modele')) {
    $query->where('modele', $request->modele); // now modele = model_id
}

    if ($request->filled('city')) {
        $query->where('city', $request->city);
    }

    $categories = $query->select('phone')
        ->selectRaw('COUNT(*) as cars_count')
        ->groupBy('phone')
        ->get();

    foreach ($categories as $category) {
        $firstClient = \App\Models\Client::where('phone', $category->phone)->first();
        $category->client_name = $firstClient->name ?? 'NADO.ma';
        $category->company     = $firstClient->company ?? null;
        $category->logo        = $firstClient->logo ?? 'assets/images/logo.png';
    }

    // 🔹 Render only the category cards (partial view)
    return view('nadoautoenchere.partials.categories-list', compact('categories'))->render();
}

public function prelogin()
{
    return view('nadoautoenchere.prelogin');
}

public function register()
{
    $client = Auth::guard('client')->user();
    if ($client && $client->type === 'flotte&enchere') {
        return redirect()->route('nadoautoenchere.categories');
    }
    $profile = request('profile') ?: 'entreprise';
    // Rôle transmis par le bouton Vendre/Acheter de la prelogin ; sinon
    // l'utilisateur le choisit dans le formulaire (vendeur/acheteur).
    $role = in_array(request('role'), ['vendeur', 'acheteur'], true) ? request('role') : null;
    // $client (particulier/pro déjà connecté) → le formulaire se pré-remplit et
    // bascule en "upgrade" (pas de ressaisie, pas de mot de passe).
    return view('nadoautoenchere.register', compact('profile', 'role', 'client'));
}

/**
 * Un client déjà connecté (particulier/pro) accède aux enchères sans se
 * réinscrire : on bascule son compte existant en flotte&enchere + rôle.
 */
public function upgrade(Request $request)
{
    $client = Auth::guard('client')->user();
    if (! $client) {
        return redirect()->route('client.login');
    }

    $data = $request->validate([
        'ae_role' => 'required|in:vendeur,acheteur',
        'phone'   => 'required|string|max:20',
        'company' => 'nullable|string|max:255',
    ]);

    $isVendeur = $data['ae_role'] === 'vendeur';

    $client->type        = 'flotte&enchere';
    $client->ae_role     = $data['ae_role'];
    $client->phone       = $data['phone']; // l'Auto-Enchère identifie le client par téléphone
    // Vendeur → accès validé automatiquement ; acheteur → en attente de validation admin.
    $client->ae_approved = $isVendeur;
    if (! empty($data['company'])) {
        $client->company = $data['company'];
    }
    $client->save();

    if ($isVendeur) {
        // Pas de validation admin : le vendeur peut déposer ses véhicules tout de suite.
        return redirect()->route('nadoautoenchere.vendre')
            ->with('success', 'Votre compte Vendeur Auto Enchère est activé. Vous pouvez déposer vos véhicules aux enchères.');
    }

    \App\Services\EnchereNotifier::adminPending($client);

    return redirect()->route('nadoautoenchere.prelogin')
        ->with('ae_pending', "Votre demande d'accès aux enchères a bien été enregistrée. Elle sera active après validation par l'administrateur.");
}


public function proposerPrix(Request $request, $id)
{
    if (!Auth::guard('client')->check()) {
        return redirect()->route('client.login')->with('error', 'Vous devez être connecté pour proposer un prix.');
    }

    $client = Auth::guard('client')->user();
    $enchere = NadoAutoEnchere::findOrFail($id);
    $montant = (float) preg_replace('/[^0-9.]/', '', (string) $request->input('p'));

    // ── Enchère compétitive chronométrée (flag enchere.live_bidding) ──────────
    // Placement atomique via AuctionService (verrou + proxy + anti-snipe).
    if (feature_on('enchere.live_bidding')) {
        try {
            $result = app(\App\Services\AuctionService::class)->placeBid($enchere, $client, $montant);
        } catch (\App\Exceptions\BidException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }

        // On conserve les notifications admin + client existantes (même format).
        $this->dispatchBidNotifications($enchere, $client, $montant);

        // Email de surenchère à l'ancien leader (après réponse, best-effort, gated).
        if (! empty($result['previous_leader_id'])) {
            $prevId   = $result['previous_leader_id'];
            $newPrice = $result['current_price'];
            $encId    = $enchere->id;
            dispatch(function () use ($prevId, $encId, $newPrice) {
                $prev = \App\Models\Client::find($prevId);
                $enc  = NadoAutoEnchere::find($encId);
                if ($prev && $enc) {
                    \App\Services\EnchereNotifier::outbid($prev, $enc, (float) $newPrice);
                }
            })->afterResponse();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success'       => true,
                'is_leader'     => $result['is_leader'],
                'current_price' => $result['current_price'],
                'min_next'      => $result['min_next'],
                'extended'      => $result['extended'],
                'message'       => $result['is_leader']
                    ? __('messages.enchere.you_lead')
                    : __('messages.enchere.you_outbid'),
            ]);
        }

        $msg = $result['is_leader'] ? __('messages.enchere.bid_placed_lead') : __('messages.enchere.bid_placed_outbid');
        return back()->with($result['is_leader'] ? 'success' : 'warning', $msg);
    }

    // ── Legacy : proposition scellée (flag OFF) ───────────────────────────────
    \App\Models\NadoAutoEncheresBid::create([
        'enchere_id'  => $id,
        'client_id'   => $client->id,
        'proposition' => $montant,
    ]);

    $this->dispatchBidNotifications($enchere, $client, $montant);

    return back()->with('success', 'Votre proposition a été envoyée avec succès ✅ Un email de confirmation vous a été envoyé.');
}

/**
 * Notifications admin + client après une enchère (même format que la proposition
 * sur annonce classique : NouvellePropositionNotification, envoi après-réponse
 * pour ne jamais faire attendre le SMTP au visiteur — QUEUE=sync).
 */
private function dispatchBidNotifications(NadoAutoEnchere $enchere, $client, $montant): void
{
    $brandName = $enchere->brand?->name
        ?? (is_numeric($enchere->mark ?? null) ? null : $enchere->mark);
    $modelName = $enchere->modeleRelation?->name
        ?? (is_numeric($enchere->modele ?? null) ? null : $enchere->modele);
    $vehicule = trim(($brandName ?? '').' '.($modelName ?? '')) ?: ($enchere->titre ?: 'Enchère #'.$enchere->id);

    $payload = [
        'title'        => 'Nouvelle enchère',
        'intro'        => 'Un client vient de placer une enchère.',
        'action_text'  => 'Voir l\'enchère',
        'vehicule'     => $vehicule,
        'reference'    => 'EN'.$enchere->id,
        'annee'        => $enchere->year,
        'km'           => $enchere->km,
        'carburant'    => $enchere->carburant,
        'boite'        => $enchere->box ?? $enchere->boite_vitesse ?? null,
        'ville'        => $enchere->city,
        'prix_affiche' => $enchere->prix,
        'proposition'  => $montant,
        'client_nom'   => $client->name ?? null,
        'client_email' => $client->email ?? null,
        'client_tel'   => $client->phone ?? $client->tel ?? null,
        'date'         => now()->format('d/m/Y H:i'),
        'url'          => route('admin.nadoautoencherebid.index'),
    ];

    dispatch(function () use ($payload) {
        try {
            Notification::route('mail', config('mail.admin_address', 'contact@nado.ma'))
                ->notify(new \App\Notifications\NouvellePropositionNotification($payload));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Bid notification failed (admin): '.$e->getMessage());
        }
    })->afterResponse();

    if (!empty($client->email)) {
        $clientPayload = array_merge($payload, [
            'title'       => 'Confirmation de votre enchère',
            'intro'       => 'Bonjour ' . ($client->name ?? '') . ', nous avons bien reçu votre proposition. Notre équipe reviendra vers vous rapidement.',
            'action_text' => 'Voir vos enchères',
            'url'         => route('client.nadoautoencheresbid.index'),
        ]);

        dispatch(function () use ($client, $clientPayload) {
            try {
                Notification::route('mail', $client->email)
                    ->notify(new \App\Notifications\NouvellePropositionNotification($clientPayload));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Bid notification failed (client): '.$e->getMessage());
            }
        })->afterResponse();
    }
}

/**
 * GET nadoautoenchere/{id}/state — endpoint JSON léger pour le polling du front
 * (pas de WebSocket : adapté à l'hébergement mutualisé LiteSpeed, QUEUE=sync).
 * Pour passer au temps réel plus tard : diffuser un événement Echo/Reverb après
 * placeBid() et remplacer le polling par un abonnement — sans toucher au service.
 */
/**
 * Toggle favori d'enchère (table enchere_favorites, propre aux enchères).
 */
public function favorite(Request $request, $id)
{
    if (! Auth::guard('client')->check()) {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'login'], 401);
        }
        return redirect()->route('client.login');
    }

    $client  = Auth::guard('client')->user();
    $enchere = NadoAutoEnchere::findOrFail($id);

    if ($client->enchereFavorites()->where('enchere_id', $enchere->id)->exists()) {
        $client->enchereFavorites()->detach($enchere->id);
        $favorited = false;
    } else {
        $client->enchereFavorites()->attach($enchere->id);
        $favorited = true;
    }

    if ($request->expectsJson()) {
        return response()->json(['success' => true, 'favorited' => $favorited]);
    }

    return back()->with('success', $favorited ? 'Ajouté aux favoris ✅' : 'Retiré des favoris ❌');
}

public function state($id)
{
    $enchere = NadoAutoEnchere::findOrFail($id);

    $bidsCount   = $enchere->bids()->count();
    $bidderCount = $enchere->bids()->distinct('client_id')->count('client_id');

    // Leader masqué (anonymat des concurrents) : initiales + id tronqué.
    $leaderId   = app(\App\Services\AuctionService::class)->leaderClientId($enchere->id);
    $leaderMask = null;
    $youLead    = false;
    if ($leaderId) {
        $leader = \App\Models\Client::find($leaderId);
        $name   = $leader->company ?: $leader->name ?: 'Enchérisseur';
        $leaderMask = mb_substr($name, 0, 1) . '••• #' . $leaderId;
        $client = Auth::guard('client')->user();
        $youLead = $client && $client->id === $leaderId;
    }

    return response()->json([
        'current_price' => (float) $enchere->currentHighBid(),
        'min_next'      => (float) $enchere->minNextBid(),
        'bids_count'    => $bidsCount,
        'bidder_count'  => $bidderCount,
        'leader_masked' => $leaderMask,
        'you_lead'      => $youLead,
        'ends_at'       => $enchere->ends_at ? $enchere->ends_at->toIso8601String() : null,
        'status'        => $enchere->status,
        'extended'      => (int) $enchere->extensions_count,
    ]);
}




    public function show($id)
    {
    $enchere = NadoAutoEnchere::findOrFail($id);
      // Current highest bid (or default starting price)
    $currentPrix = $enchere->prix; 

    // Check for maximum bid by users
    $maxBid = $enchere->bids()->max('proposition'); // assuming a bids() relation

    // If a guest bid exceeds the estimated price, update the target
    $prixTarget = max($currentPrix + 1000, $maxBid ?? $currentPrix);
if (is_string($enchere->images)) {
    $enchere->images = json_decode($enchere->images, true) ?: [];
} elseif (is_array($enchere->images)) {
    // déjà un tableau
} else {
    $enchere->images = [];
}
    $enchere->increment('views');

        if ($enchere->ends_at) {
            $now  = Carbon::now();
            $end  = Carbon::parse($enchere->ends_at);
            $enchere->remaining = $end->diff($now);
        } else {
            $enchere->remaining = null;
        }
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
            if ($enchere->$key) {
                $equipements[] = $label;
            }
        }
        $clientTypes = ClientType::all();
        $tauxInteret = $clientTypes->all()?->taux_interet ?? 13;
  // Générer le vrai nom du PDF
$rapport = $enchere->rapport ?? null; // column that stores the PDF file name

if ($rapport && file_exists(storage_path('app/public/nadoautoenchere/pdf/' . $rapport))) {
    $pdfExists = true;
    $pdfName = $rapport;
} else {
    $pdfExists = false;
    $pdfName = null;
}

    
    
        $historique_entretien = [
            ['date' => '2024-05-10', 'type' => 'Vidange', 'garage' => 'Total Maroc - Casablanca', 'km' => '120 000'],
            ['date' => '2023-12-20', 'type' => 'Remplacement plaquettes', 'garage' => 'Midas Ain Sebaa', 'km' => '110 500'],
            ['date' => '2023-08-01', 'type' => 'Contrôle technique', 'garage' => 'CT Nador Auto', 'km' => '105 000'],
        ];
    
                $leftondetailBanner = Publication::where('placement', 'leftondetailenchere-banner')->where('status', true)->first();

        // Données enchère compétitive (flag enchere.live_bidding).
        $liveBidding = feature_on('enchere.live_bidding');
        $bidsCount   = $enchere->bids()->count();
        $bidderCount = $enchere->bids()->distinct('client_id')->count('client_id');
        $leaderId    = $liveBidding ? app(\App\Services\AuctionService::class)->leaderClientId($enchere->id) : null;
        $me          = Auth::guard('client')->user();
        $youLead     = $leaderId && $me && $me->id === $leaderId;
        $youBid      = $me ? $enchere->bids()->where('client_id', $me->id)->exists() : false;

        return view('nadoautoenchere.detail', [
        'enchere' => $enchere,
        'equipements' =>$equipements,
        'clientTypes' =>$clientTypes,
'prixTarget' =>$prixTarget,
        'liveBidding' => $liveBidding,
        'bidsCount'   => $bidsCount,
        'bidderCount' => $bidderCount,
        'youLead'     => $youLead,
        'youBid'      => $youBid,
        'leftondetailBanner' => $leftondetailBanner,
                'pdfName' => $pdfName,
        'pdfExists' => $pdfExists,
        'brands'   => Brand::all(),
        'city'     => Ville::all(),
        'colors'   => Color::all(),
        'modeles'  => Modele::all()
    ]);
    }
  public function showPdf($pdf)
{
    $filePath = storage_path('app/public/nadoautoenchere/pdf/' . $pdf);

    if (!file_exists($filePath)) {
        abort(404, 'PDF not found.');
    }

    return response()->file($filePath);
}

}



