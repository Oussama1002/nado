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
