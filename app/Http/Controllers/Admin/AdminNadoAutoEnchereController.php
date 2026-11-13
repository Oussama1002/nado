<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NadoAutoEnchereRequest;
use App\Models\NadoAutoEnchere;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Color;
use App\Models\Ville;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
class AdminNadoAutoEnchereController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'role:admin']);
    }

    public function index()
    {
        return view('admin.nadoautoenchere.index', [
            'encheres' => NadoAutoEnchere::with('client')->get(),
            'brands' => Brand::all(),
            'city' => Ville::all(),
            'colors' => Color::all()
        ]);
    }

    /** Active / désactive une enchère (toggle depuis la liste admin). */
    public function toggleActive(Request $request, $id)
    {
        $enchere = NadoAutoEnchere::findOrFail($id);
        $enchere->active = $request->boolean('active');
        $enchere->save();

        return response()->json([
            'success' => true,
            'active'  => (bool) $enchere->active,
        ]);
    }

    /**
     * Écran de gestion d'une enchère compétitive : réglages (mise à prix,
     * réserve, incrément, fenêtre), actions cycle de vie, et historique des
     * enchères. (Enchère compétitive — flag enchere.live_bidding.)
     */
    public function manage($id)
    {
        $enchere = NadoAutoEnchere::with(['winner'])->findOrFail($id);

        $bids = $enchere->bids()
            ->with('client')
            ->orderByDesc('id')
            ->get();

        return view('admin.nadoautoenchere.manage', [
            'enchere' => $enchere,
            'bids'    => $bids,
        ]);
    }

    /** Enregistre les paramètres d'enchère (prix + fenêtre). */
    public function setAuction(Request $request, $id)
    {
        $enchere = NadoAutoEnchere::findOrFail($id);

        $data = $request->validate([
            'starting_price' => ['nullable', 'numeric', 'min:0'],
            'reserve_price'  => ['nullable', 'numeric', 'min:0'],
            'min_increment'  => ['nullable', 'numeric', 'min:0'],
            'starts_at'      => ['nullable', 'date'],
            'ends_at'        => ['nullable', 'date', 'after:starts_at'],
        ]);

        $enchere->starting_price = $data['starting_price'] ?? $enchere->starting_price ?? $enchere->prix;
        $enchere->reserve_price  = $data['reserve_price'] ?? null;
        $enchere->min_increment  = $data['min_increment'] ?? $enchere->min_increment ?? 1000;
        if (array_key_exists('starts_at', $data)) $enchere->starts_at = $data['starts_at'] ?: null;
        if (array_key_exists('ends_at', $data))   $enchere->ends_at   = $data['ends_at'] ?: null;

        // Initialise le prix courant si aucune enchère n'a encore été placée.
        if ($enchere->current_price === null || (float) $enchere->current_price <= 0) {
            $enchere->current_price = $enchere->starting_price;
        }

        // Si l'enchère est en brouillon et qu'une fenêtre est définie → planifiée.
        if (in_array($enchere->status, [null, 'draft'], true) && $enchere->starts_at) {
            $enchere->status = 'scheduled';
        }

        $enchere->save();

        return back()->with('success', 'Paramètres d\'enchère enregistrés ✅');
    }

    /**
     * Actions cycle de vie : start (live) / pause (draft) / cancel (cancelled).
     */
    public function auctionAction(Request $request, $id)
    {
        $enchere = NadoAutoEnchere::findOrFail($id);
        $action  = $request->input('action');

        switch ($action) {
            case 'start':
                $enchere->status = 'live';
                if (! $enchere->starts_at) $enchere->starts_at = now();
                if ($enchere->current_price === null || (float) $enchere->current_price <= 0) {
                    $enchere->current_price = $enchere->effectiveStartingPrice();
                }
                $enchere->active = 1;
                break;
            case 'pause':
                $enchere->status = 'draft'; // bloque les enchères (assertOpen → fermé)
                break;
            case 'schedule':
                $enchere->status = 'scheduled';
                break;
            case 'cancel':
                $enchere->status = 'cancelled';
                $enchere->active = 0;
                break;
            default:
                return back()->with('error', 'Action inconnue.');
        }

        $enchere->save();

        return back()->with('success', 'Enchère mise à jour : ' . $action . ' ✅');
    }

    /** Désignation manuelle du gagnant (clôture). */
    public function declareWinner(Request $request, $id)
    {
        $enchere = NadoAutoEnchere::findOrFail($id);

        $data = $request->validate([
            'bid_id' => ['required', 'integer'],
        ]);

        $bid = $enchere->bids()->where('id', $data['bid_id'])->first();
        if (! $bid) {
            return back()->with('error', 'Enchère introuvable pour ce véhicule.');
        }

        $enchere->winner_client_id = $bid->client_id;
        $enchere->winning_bid_id   = $bid->id;
        $enchere->status           = 'ended';
        $enchere->save();

        // Notifie le gagnant + l'équipe (best-effort).
        $winner = Client::find($bid->client_id);
        \App\Services\EnchereNotifier::winnerDeclared($enchere, $winner, (float) ($enchere->current_price ?: $bid->proposition));

        return back()->with('success', 'Gagnant désigné ✅');
    }

    /** Liste des demandes de vente (véhicules soumis par les vendeurs). */
    public function sellRequests(Request $request)
    {
        $q = \App\Models\EnchereSellRequest::with('client')->latest();
        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }

        return view('admin.nadoautoenchere.sell-requests', [
            'requests' => $q->paginate(20)->withQueryString(),
            'status'   => $status,
        ]);
    }

    /** Change le statut d'une demande (new / processed / archived). */
    public function sellRequestStatus(Request $request, $id)
    {
        $req = \App\Models\EnchereSellRequest::findOrFail($id);
        $data = $request->validate(['status' => ['required', 'in:new,processed,archived']]);
        $req->update(['status' => $data['status']]);

        return back()->with('success', 'Statut mis à jour ✅');
    }

    public function sellRequestDestroy($id)
    {
        \App\Models\EnchereSellRequest::findOrFail($id)->delete();
        return back()->with('success', 'Demande supprimée.');
    }

    public function create()
    {
        return view('admin.nadoautoenchere.form', [
            'encheres' => new NadoAutoEnchere,
            'brands' => Brand::all(),
            'city' => Ville::all(),
            'colors' => Color::all(),
            'clients' => $this->vendeurClients(),
        ]);
    }

    /**
     * Clients Flotte & Enchère ayant le rôle vendeur (ce sont eux qui déposent
     * des véhicules aux enchères) — pour le menu déroulant du formulaire.
     */
    private function vendeurClients()
    {
        return Client::where('type', 'flotte&enchere')
            ->where('ae_role', 'vendeur')
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);
    }

    public function store(NadoAutoEnchereRequest $request)
    {
        $data = $request->validated();

        // Champs ajoutés (Caractéristiques, Documents, Localisation).
        // Ils n'ont pas de règles dans NadoAutoEnchereRequest : sans cette fusion,
        // $request->validated() les supprimerait silencieusement avant la sauvegarde.
        $data = array_merge($data, $request->validate([
            'nb_cles'          => ['nullable', 'integer', 'min:1', 'max:2'],
            'pneu_secours'     => ['nullable', 'boolean'],
            'outillage'        => ['nullable', 'boolean'],
            'carte_grise'      => ['nullable', 'boolean'],
            'vignette_annee'   => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'visite_technique' => ['nullable', 'date'],
            'latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'        => ['nullable', 'numeric', 'between:-180,180'],
            'adresse'          => ['nullable', 'string', 'max:255'],
            'maps_url'         => ['nullable', 'string', 'max:500'],
        ]));

        // Un lien court maps.app.goo.gl ne contient aucune coordonnée : on suit la
        // redirection côté serveur pour récupérer l'URL complète et en extraire lat/lng.
        // Résolution automatique du lien Maps : on remplit uniquement les champs
        // laissés vides par l'admin, ce qui permet (1) de refléter un nouveau lien
        // même si d'anciennes coordonnées existent déjà, (2) de respecter une
        // saisie manuelle d'un seul des trois champs.
        if (! empty($data['maps_url'])
            && (empty($data['latitude']) || empty($data['longitude']) || empty($data['adresse']))) {
            $coords = $this->resolveMapsCoordinates($data['maps_url']);
            if ($coords) {
                if (empty($data['latitude'])  && ! empty($coords['lat']))  $data['latitude']  = $coords['lat'];
                if (empty($data['longitude']) && ! empty($coords['lng']))  $data['longitude'] = $coords['lng'];
                if (empty($data['adresse'])   && ! empty($coords['name'])) $data['adresse']   = $coords['name'];
            }
        }
        $data['active'] = 1;
        $data['views'] = 0;

        // Handle checkboxes
        $checkboxes = ['active','garantie','certifie','showroom','finance','particulie', /* ... all other fields ... */ ];
        foreach ($checkboxes as $field) {
            $data[$field] = $request->has($field) ? 1 : 0;
        }

        // Handle images
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $images[] = $this->saveImage($file, $data['mark'].'-'.$data['modele']);
            }
        }
        $data['images'] = $images;

        // Rapport PDF
        if ($request->hasFile('rapport')) {
            $data['rapport'] = $data['mark'].'-'.$data['modele'].'-'.rand(100000,999999).'.'.$request->rapport->extension();
            $request->rapport->move(storage_path('app/public/nadoautoenchere/pdf/'), $data['rapport']);
        }

        NadoAutoEnchere::create($data);

        return to_route('admin.nadoautoenchere.index')->with('success', 'Annonce enregistrée avec succès ✅');
    }

    public function edit($id)
    {
        return view('admin.nadoautoenchere.form', [
            'encheres' => NadoAutoEnchere::findOrFail($id),
            'brands' => Brand::all(),
            'city' => Ville::all(),
            'colors' => Color::all(),
            'clients' => $this->vendeurClients(),
        ]);
    }

    public function update(NadoAutoEnchereRequest $request, $id)
    {
        $data = $request->validated();

        // Champs ajoutés (Caractéristiques, Documents, Localisation).
        // Ils n'ont pas de règles dans NadoAutoEnchereRequest : sans cette fusion,
        // $request->validated() les supprimerait silencieusement avant la sauvegarde.
        $data = array_merge($data, $request->validate([
            'nb_cles'          => ['nullable', 'integer', 'min:1', 'max:2'],
            'pneu_secours'     => ['nullable', 'boolean'],
            'outillage'        => ['nullable', 'boolean'],
            'carte_grise'      => ['nullable', 'boolean'],
            'vignette_annee'   => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'visite_technique' => ['nullable', 'date'],
            'latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'        => ['nullable', 'numeric', 'between:-180,180'],
            'adresse'          => ['nullable', 'string', 'max:255'],
            'maps_url'         => ['nullable', 'string', 'max:500'],
        ]));

        // Un lien court maps.app.goo.gl ne contient aucune coordonnée : on suit la
        // redirection côté serveur pour récupérer l'URL complète et en extraire lat/lng.
        // Résolution automatique du lien Maps : on remplit uniquement les champs
        // laissés vides par l'admin, ce qui permet (1) de refléter un nouveau lien
        // même si d'anciennes coordonnées existent déjà, (2) de respecter une
        // saisie manuelle d'un seul des trois champs.
        if (! empty($data['maps_url'])
            && (empty($data['latitude']) || empty($data['longitude']) || empty($data['adresse']))) {
            $coords = $this->resolveMapsCoordinates($data['maps_url']);
            if ($coords) {
                if (empty($data['latitude'])  && ! empty($coords['lat']))  $data['latitude']  = $coords['lat'];
                if (empty($data['longitude']) && ! empty($coords['lng']))  $data['longitude'] = $coords['lng'];
                if (empty($data['adresse'])   && ! empty($coords['name'])) $data['adresse']   = $coords['name'];
            }
        }

        $checkboxes = ['active','garantie','certifie','showroom','finance','particulie', /* ... all other fields ... */ ];
        foreach ($checkboxes as $field) {
            $data[$field] = $request->has($field) ? 1 : 0;
        }

        $enchere = NadoAutoEnchere::findOrFail($id);

        // Images conservées : le plugin imageUploader renvoie dans `old_images`
        // les ids des miniatures que l'admin a laissées (celles supprimées sont
        // omises, et l'ordre reflète celui de la grille). Les ids sont les index
        // de l'ancien tableau $enchere->images (voir la vue, where id: {{ $index }}).
        $previousImages = (array) ($enchere->images ?? []);
        $keptIndexes    = array_map('intval', (array) $request->input('old_images', []));

        $images = [];
        foreach ($keptIndexes as $i) {
            if (isset($previousImages[$i])) {
                $images[] = $previousImages[$i];
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $images[] = $this->saveImage($file, $data['mark'].'-'.$data['modele']);
            }
        }

        // Supprime les fichiers des photos retirées pour ne pas encombrer le disque.
        $removed = array_diff($previousImages, $images);
        foreach ($removed as $file) {
            $path = storage_path('app/public/nadoautoenchere/'.$file);
            if (is_file($path)) {
                @unlink($path);
            }
        }

        $data['images'] = $images;

        if ($request->hasFile('rapport')) {
            $data['rapport'] = $data['mark'].'-'.$data['modele'].'-'.rand(100000,999999).'.'.$request->rapport->extension();
            $request->rapport->move(storage_path('app/public/nadoautoenchere/pdf/'), $data['rapport']);
        }

        $enchere->update($data);

        return to_route('admin.nadoautoenchere.index')->with('success', 'Annonce mise à jour avec succès ✅');
    }

    /**
     * Endpoint AJAX appelé par le formulaire admin dès qu'un lien Maps est collé.
     * Suit la redirection côté serveur (le navigateur ne peut pas à cause de CORS)
     * et renvoie lat/lng + nom du lieu, à injecter dans les champs.
     */
    public function resolveMaps(Request $request)
    {
        $url = (string) $request->input('url', '');
        if ($url === '') {
            return response()->json(['ok' => false, 'reason' => 'empty'], 422);
        }

        $coords = $this->resolveMapsCoordinates($url);
        if (! $coords) {
            return response()->json(['ok' => false, 'reason' => 'not_found']);
        }

        return response()->json([
            'ok'   => true,
            'lat'  => $coords['lat'],
            'lng'  => $coords['lng'],
            'name' => $coords['name'] ?? null,
        ]);
    }

    public function destroy($id)
    {
        NadoAutoEnchere::where('id', $id)->delete();
        return to_route('admin.nadoautoenchere.index')->with('success', 'Annonce supprimée ✅');
    }

    private function saveImage($file, $title)
    {
        $imageName = Str::slug($title, '-') . '-' . rand(100000, 999999) . '.' . $file->getClientOriginalExtension();
        $file->move(storage_path('app/public/nadoautoenchere/'), $imageName);
        return $imageName;
    }

    /**
     * Extrait la latitude/longitude d'une URL Google Maps.
     * Un lien court (maps.app.goo.gl) ne les contient pas : on suit alors la
     * redirection pour obtenir l'URL complète.
     */
    private function resolveMapsCoordinates(string $url): ?array
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        // 1) Les coordonnées sont déjà dans l'URL fournie.
        $found = $this->extractCoordinates($url);
        if ($found) {
            return $found;
        }

        // 2) Lien court : suivre la redirection.
        if (! function_exists('curl_init')) {
            return null;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_NOBODY         => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; NADO/1.0)',
        ]);
        curl_exec($ch);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        return $finalUrl ? $this->extractCoordinates($finalUrl) : null;
    }

    /**
     * !3d<lat>!4d<lng> = position réelle du lieu (prioritaire)
     * @<lat>,<lng>     = centre de la vue
     * ?q=<lat>,<lng>   = ancien format
     */
    private function extractCoordinates(string $url): ?array
    {
        $name = null;
        // L'URL développée contient /maps/place/<Nom+du+lieu>/@...
        if (preg_match('#/maps/place/([^/@?]+)#', $url, $pm)) {
            $decoded = urldecode($pm[1]);
            $decoded = str_replace('+', ' ', $decoded);
            $name = trim($decoded);
        }

        if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $url, $m)) {
            return ['lat' => $m[1], 'lng' => $m[2], 'name' => $name];
        }
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $m)) {
            return ['lat' => $m[1], 'lng' => $m[2], 'name' => $name];
        }
        if (preg_match('/[?&]q=(-?\d+\.\d+),\s*(-?\d+\.\d+)/', $url, $m)) {
            return ['lat' => $m[1], 'lng' => $m[2], 'name' => $name];
        }

        return null;
    }
}
