<?php

namespace App\Http\Controllers;

use App\Models\Annonces;
use App\Models\Brand;
use App\Models\Media;
use App\Models\Modele;
use App\Models\Pack;
use App\Models\Vente;
use App\Models\Ville;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class VenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
     {
         // /vente redirects to the full marketing+form page at /vente/formulaire
         return redirect()->route('vente.form');
     }
 
     public function form()
     {
         // Charge les données pour le formulaire
         $brands = Brand::all();
         $city = Ville::all();

        $prefill = null;
        $annonceToEdit = null;

        // If a logged-in client wants to prefill the vente modal from an existing annonce
        if (request()->filled('annonce_id') && Auth::guard('client')->check()) {
            $client = Auth::guard('client')->user();
            $annonceId = (int) request('annonce_id');

            $annonceToEdit = Annonces::query()
                ->where('id', $annonceId)
                ->where('phone', $client->phone) // ownership check (annonces linked by phone)
                ->first();

            if ($annonceToEdit) {
                // annonces store the brand / model ids; the vente form's selects use names
                $annonceToEdit->loadMissing(['brand', 'modeleRelation']);
                $prefill = [
                    'annonce_id' => $annonceToEdit->id,
                    'mark'       => $annonceToEdit->brand->name ?? $annonceToEdit->mark,
                    'modele'     => $annonceToEdit->modeleRelation->name ?? $annonceToEdit->modele,
                    'year'       => $annonceToEdit->year,
                    'carburant'  => $annonceToEdit->carburant,
                    'prix'       => $annonceToEdit->prix,
                    'box'        => $annonceToEdit->box,
                    'cv'         => $annonceToEdit->cv,
                    'km'         => $annonceToEdit->km,
                    'etat'       => $annonceToEdit->etat,
                    'type'       => $annonceToEdit->type ?? null,
                    'origine'    => $annonceToEdit->origine,
                    'city'       => $annonceToEdit->city,
                    'name'       => $client->name ?? null,
                    'phone'      => $client->phone ?? null,
                    'email'      => $client->email ?? null,
                    // Current photos, shown in "Photos du véhicule" (removable)
                    'photos'     => array_map(fn ($n) => [
                        'name' => $n,
                        'url'  => asset('storage/ads/' . $n),
                    ], $this->annonceImageNames($annonceToEdit)),
                ];
            }
        }

        return view('vente.vente', compact('brands', 'city', 'prefill'));
     }

     public function particulier()
     {
         return view('vente.particulier');
     }

     public function pro()
     {
         return view('vente.pro');
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
        // 1️⃣ Validate form fields
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:50',
            'email'      => 'nullable|email|max:255',
            'city'       => 'required|string|max:100',
            'mark'       => 'required|string|max:100',
            'modele'     => 'required|string|max:100',
            'year'       => 'required|string|max:10',
            'km'         => 'nullable|string|max:50',
            'carburant'  => 'nullable|string|max:50',
            'box'        => 'nullable|string|max:50',
            'cv'         => 'nullable|integer|min:0',
            'prix'       => 'nullable|numeric|min:0',
            'etat'       => 'nullable|string|max:50',
            'type'       => 'nullable|string|max:50',
            'origine'    => 'nullable|string|max:100',
            'description'=> 'nullable|string|max:5000',
            // Carte grise (pré-remplissage IA) — champs véhicule uniquement
            'matricule'  => 'nullable|string|max:50',
            'vin'        => 'nullable|string|max:50',
            'mode'       => 'required|string|max:100',
            'photos'     => 'nullable|array|max:20',
            'photos.*'   => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'video'      => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:102400',
            // Modifier: existing annonce + the current photos the client kept
            'annonce_id'    => 'nullable|integer',
            'keep_photos'   => 'nullable|array|max:20',
            'keep_photos.*' => 'string|max:255',
        ]);

        // 2️⃣ The form sends brand / model names; annonces store their ids
        //    (Annonces::brand() and modeleRelation() join on id)
        $brand  = Brand::where('name', $validated['mark'])->first();
        $modele = $brand
            ? Modele::where('brand_id', $brand->id)->where('name', $validated['modele'])->first()
            : null;

        // Link to the client account (Annonces::client() joins on phone)
        $client = Auth::guard('client')->user();
        $phone  = $client->phone ?? $validated['phone'];

        // Chosen pack ("gratuit" or a pack name) → pack_id when it exists
        $packId = Pack::where('name', $validated['mode'])->value('id');

        // Modifier: update the client's own annonce instead of creating a duplicate
        $existing = null;
        if (!empty($validated['annonce_id']) && $client) {
            $existing = Annonces::where('id', $validated['annonce_id'])
                ->where('phone', $client->phone) // ownership (annonces linked by phone)
                ->first();
        }

        try {
            // Vehicle fields, same for create and update
            $fields = [
                'titre'     => trim($validated['mark'].' '.$validated['modele'].' '.$validated['year']),
                'mark'      => $brand->id ?? $validated['mark'],
                'modele'    => $modele->id ?? $validated['modele'],
                'year'      => $validated['year'],
                'km'        => $validated['km'] ?? null,
                'carburant' => $validated['carburant'] ?? null,
                'box'       => $validated['box'] ?? null,
                'cv'        => $validated['cv'] ?? null,
                'prix'      => $validated['prix'] ?? null,
                'etat'      => $validated['etat'] ?? null,
                'origine'   => $validated['origine'] ?? null,
                'description'=> $validated['description'] ?? null,
                'matricule' => $validated['matricule'] ?? null,
                'city'      => $validated['city'],
                'pack_id'   => $packId,
                // Pending admin approval — also after a Modifier, so the admin
                // re-checks the changes (set to $existing->active to keep it published)
                'active'    => 0,
            ] + (
                // Occasion / Neuf — column added by 2026_09_21_000001_add_type_to_annonces_table
                // (skipped until that migration has run, instead of failing the insert)
                Schema::hasColumn('annonces', 'type') ? ['type' => $validated['type'] ?? null] : []
            ) + (
                // VIN / châssis — colonne ajoutée par 2026_11_02_000002_add_vin_to_annonces_table
                Schema::hasColumn('annonces', 'vin') ? ['vin' => $validated['vin'] ?? null] : []
            );

            if ($existing) {
                // 3️⃣ Update the existing annonce
                $annonce = $existing;
                $annonce->forceFill($fields)->save();

                // Photos the client kept (only names that really belong to this annonce)
                $current = $this->annonceImageNames($annonce);
                $images  = array_values(array_intersect($current, $validated['keep_photos'] ?? []));

                // Removed photos: drop their media rows
                $removed = array_diff($current, $images);
                if ($removed) {
                    Media::where('annonces_id', $annonce->id)->whereIn('name', $removed)->delete();
                }
            } else {
                // 3️⃣ Create the annonce, pending admin approval (active = 0)
                // forceCreate: keys are built here (not raw input), and some required columns aren't in $fillable
                $annonce = Annonces::forceCreate($this->withRequiredAnnonceDefaults($fields + [
                    'phone'     => $phone,
                    'status'    => 'disponible',
                    // Home page sections filter on these flags ("Annonces particuliers"
                    // needs particulie = 1 and certifie = 0)
                    'particulie' => ($client->type ?? null) === 'pro' ? 0 : 1,
                    'pro'        => ($client->type ?? null) === 'pro' ? 1 : 0,
                    'certifie'   => 0,
                    'verified'  => false,
                    'is_homepage' => 0,
                    'views'     => 0,
                    'images'    => [],
                ]));
                $images = [];
            }

            // 4️⃣ New photos: same storage as the admin (storage/app/public/ads + media rows)
            if ($request->hasFile('photos')) {
                $slug = Str::slug($validated['mark'].'-'.$validated['modele'], '-');
                $manager = new ImageManager(new Driver);
                foreach ($request->file('photos') as $photo) {
                    if (count($images) >= 20) break;
                    $name = $slug.'-'.rand(100000, 999999).'.'.$photo->getClientOriginalExtension();
                    $manager->read($photo)->save(storage_path('app/public/ads/'.$name));

                    $media = new Media;
                    $media->name = $name;
                    $media->annonces_id = $annonce->id;
                    $media->save();

                    $images[] = $name;
                }
            }
            $annonce->images = $images;

            // Video: same folder as client annonces
            if ($request->hasFile('video')) {
                $annonce->video = $request->file('video')->store('annonces/videos', 'public');
            }

            $annonce->save();
        } catch (\Throwable $e) {
            Log::error('Vente modal: annonce creation failed', ['error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Une erreur est survenue lors de l'enregistrement de votre annonce.",
                    // technical detail only when APP_DEBUG=true
                    'error'   => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }
            return back()->withInput()->with('error', "Une erreur est survenue lors de l'enregistrement de votre annonce.");
        }

        Cache::forget('similar_adsAll');
        Cache::forget('similar_adsp');
        Cache::forget('similar_ads_pro');
        Cache::forget('similar_ads');

        $message = $existing
            ? "Votre annonce a été modifiée. Elle sera visible après validation par l'administrateur."
            : "Votre annonce a été soumise. Elle sera visible après validation par l'administrateur.";

        // 5️⃣ AJAX submit from the vente modal → JSON, the modal shows its confirmation page
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('index')->with('success', $message);
    }

    /**
     * Photo filenames of an annonce (storage/app/public/ads). `images` is an array for
     * admin / vente-modal annonces but a twice-encoded JSON string for older client
     * annonces; fall back to the media rows.
     */
    private function annonceImageNames(Annonces $annonce): array
    {
        $images = $annonce->images;
        for ($i = 0; $i < 2 && is_string($images); $i++) {
            $images = json_decode($images, true);
        }
        $names = is_array($images) ? array_values(array_filter($images, 'is_string')) : [];

        if (empty($names)) {
            $names = Media::where('annonces_id', $annonce->id)->pluck('name')->filter()->values()->all();
        }

        return $names;
    }

    /**
     * The annonces table has many NOT NULL columns without a default (nb_Door,
     * premiere_main, colors, garantie, image…) that the vente modal doesn't ask for.
     * Fill every such column that is missing / null with an empty value of the
     * right type — the admin completes them (Modifier) before approving.
     */
    private function withRequiredAnnonceDefaults(array $data): array
    {
        $columns = DB::select("
            SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'annonces'
              AND IS_NULLABLE = 'NO'
              AND COLUMN_DEFAULT IS NULL
              AND EXTRA NOT LIKE '%auto_increment%'
        ");

        foreach ($columns as $col) {
            $name = $col->COLUMN_NAME;
            if (in_array($name, ['created_at', 'updated_at'], true)) continue; // set by Eloquent
            if (array_key_exists($name, $data) && $data[$name] !== null) continue;

            switch (strtolower($col->DATA_TYPE)) {
                case 'tinyint': case 'smallint': case 'mediumint': case 'int': case 'integer':
                case 'bigint': case 'decimal': case 'float': case 'double': case 'bit': case 'year':
                    $value = 0;
                    break;
                case 'date':
                    $value = now()->toDateString();
                    break;
                case 'datetime': case 'timestamp':
                    $value = now();
                    break;
                case 'json':
                    $value = '[]';
                    break;
                case 'enum':
                    // first allowed value, e.g. enum('oui','non') → 'oui'
                    preg_match("/^enum\('((?:[^']|'')*)'/i", $col->COLUMN_TYPE, $m);
                    $value = isset($m[1]) ? str_replace("''", "'", $m[1]) : '';
                    break;
                default:
                    $value = '';
            }

            $data[$name] = $value;
        }

        return $data;
    }

    public function storePro(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:50',
            'email'        => 'nullable|email|max:255',
            'company_name' => 'nullable|string|max:255',
            'ice'          => 'nullable|string|max:50',
            'sector'       => 'nullable|string|max:100',
            'fleet_size'   => 'nullable|string|max:100',
            'message'      => 'nullable|string|max:2000',
        ]);

        $vente = new Vente();
        $vente->name    = $request->name;
        $vente->phone   = $request->phone;
        $vente->email   = $request->email;
        $vente->ice     = $request->ice;
        $vente->jeveux  = 'professionnel B2B';
        $vente->mode    = 'B2B Professionnel';
        $vente->mark    = $request->company_name ?? 'N/A';
        $vente->modele  = $request->sector ?? 'N/A';
        $vente->year    = $request->fleet_size ?? 'N/A';
        $vente->save();

        // Notify contact@nado.ma of the new B2B request
        try {
            $lines = [
                'Nouvelle demande de vente B2B professionnelle',
                '',
                'Raison sociale : ' . ($request->company_name ?: '—'),
                'ICE            : ' . ($request->ice ?: '—'),
                'Secteur        : ' . ($request->sector ?: '—'),
                'Taille flotte  : ' . ($request->fleet_size ?: '—'),
                '',
                'Contact        : ' . $request->name,
                'Téléphone      : ' . $request->phone,
                'Email          : ' . ($request->email ?: '—'),
                '',
                'Message        :',
                ($request->message ?: '—'),
                '',
                '---',
                'Soumise le ' . $vente->created_at->format('d/m/Y H:i'),
                'ID interne : #' . $vente->id,
            ];
            $body = implode("\n", $lines);

            Mail::raw($body, function ($m) use ($request) {
                $m->to('contact@nado.ma')
                  ->subject('Nouvelle demande B2B Pro — ' . ($request->company_name ?: $request->name));
                if ($request->email) {
                    $m->replyTo($request->email, $request->name);
                }
            });
        } catch (\Throwable $e) {
            Log::error('B2B pro mail failed: ' . $e->getMessage());
        }

        return redirect()->route('vente.pro')->with('success', 'Votre demande professionnelle a bien été enregistrée !');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
public function getModels(Request $request)
{
    $brandId = $request->brand_id;

    $models = \App\Models\Modele::where('brand_id', $brandId)->get(['id', 'name']);
    
    return response()->json($models);
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

    public function resizeImage($photo, $title)
    {
        $image = $photo;
        $imageName = Str::slug($title, '-').'-'.rand(100000, 999999).'.'.$image->getClientOriginalExtension();
        $manager = new ImageManager(new Driver);
        $img = $manager->read($image);
        $img->save(storage_path('app/public/vente/'.$imageName));

        return $imageName;
    }
}
