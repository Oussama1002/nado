<?php

namespace App\Http\Controllers;

use App\Models\Annonces;
use App\Models\Brand;
use App\Models\Demande;
use App\Models\DemandeReply;
use App\Models\Modele;
use App\Models\Ville;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemandeController extends Controller
{
    public function index(Request $request)
    {
        // Public forum: show every approved demande that isn't expired, regardless
        // of the model's public() scope (which may be too restrictive).
        $q = Demande::query()
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($x) {
                $x->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with(['brand', 'modele', 'ville'])
            ->latest();

        if ($b = $request->integer('brand_id'))  $q->where('brand_id', $b);
        if ($m = $request->integer('modele_id')) $q->where('modele_id', $m);
        if ($v = $request->integer('ville_id'))  $q->where('ville_id', $v);
        if ($pmin = $request->integer('price_min')) $q->where('price_max', '>=', $pmin);
        if ($pmax = $request->integer('price_max')) $q->where('price_min', '<=', $pmax);

        // Fusion : mêmes marque+modèle avec des plages d'années proches → une seule
        // demande « collective » qui indique le nombre de personnes intéressées.
        $all   = $q->limit(1000)->get();
        $reps  = $this->mergeDemandes($all);

        $page    = max(1, (int) $request->get('page', 1));
        $perPage = 15;
        $demandes = new \Illuminate\Pagination\LengthAwarePaginator(
            array_slice($reps, ($page - 1) * $perPage, $perPage),
            count($reps),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('demandes.index', [
            'demandes' => $demandes,
            'brands'   => Brand::orderBy('name')->get(),
            'villes'   => Ville::orderBy('name')->get(),
            'filters'  => $request->all(),
        ]);
    }

    /**
     * Fusionne les demandes par (marque, modèle) et par plages d'années proches
     * (chevauchement ou écart <= seuil). Renvoie une demande représentative par
     * groupe, enrichie de group_count (personnes distinctes), group_names et de
     * la plage d'années fusionnée. Les demandes sans marque/modèle restent seules.
     */
    private function mergeDemandes($all): array
    {
        $threshold = 2;                       // années d'écart tolérées
        $curYear   = (int) date('Y') + 1;
        $reps      = [];

        // Regroupe d'abord par voiture (marque+modèle). Sans l'un des deux → seul.
        $byCar = [];
        foreach ($all as $d) {
            $key = ($d->brand_id && $d->modele_id) ? ('car_' . $d->brand_id . '_' . $d->modele_id) : ('solo_' . $d->id);
            $byCar[$key][] = $d;
        }

        foreach ($byCar as $list) {
            // Même voiture (marque+modèle) = un seul groupe, peu importe les années
            // (on affiche l'étendue des années). Les lignes « solo_ » restent seules.
            $clusters = [['items' => $list]];

            foreach ($clusters as $c) {
                $items = collect($c['items']);
                $rep   = $items->sortByDesc('created_at')->first();

                // Une entrée par personne distincte (par client, sinon téléphone).
                $persons = $items
                    ->groupBy(fn ($x) => $x->client_id ?: preg_replace('/\D+/', '', (string) $x->telephone))
                    ->map(fn ($g) => $g->first());

                $rep->group_count    = max(1, $persons->count());
                $rep->group_names    = $persons->pluck('nom')->filter()->values()->all();
                $rep->group_year_min = $items->min('year_min');
                $rep->group_year_max = $items->max('year_max');
                $reps[] = $rep;
            }
        }

        // Tri final : les plus récentes d'abord.
        usort($reps, fn ($a, $b) => $b->created_at <=> $a->created_at);

        return $reps;
    }

    public function show($id)
    {
        $demande = Demande::public()
            ->with(['brand', 'modele', 'ville', 'color', 'replies.client', 'replies.annonce'])
            ->findOrFail($id);
        $demande->increment('views_count');

        $villeName = $demande->ville?->name;
        $matches = Annonces::where('active', 1)
            ->when($demande->brand_id,  fn ($q) => $q->where('mark', $demande->brand_id))
            ->when($demande->modele_id, fn ($q) => $q->where('modele', $demande->modele_id))
            ->when($villeName,          fn ($q) => $q->where('city', $villeName))
            ->when($demande->price_max, fn ($q) => $q->where('prix', '<=', $demande->price_max))
            ->when($demande->price_min, fn ($q) => $q->where('prix', '>=', $demande->price_min))
            ->when($demande->year_min,  fn ($q) => $q->where('year', '>=', $demande->year_min))
            ->when($demande->year_max,  fn ($q) => $q->where('year', '<=', $demande->year_max))
            ->when($demande->km_max,    fn ($q) => $q->where('km',   '<=', $demande->km_max))
            ->latest()->limit(6)->get();

        $villes = Ville::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $prefillModeles = $demande->brand_id
            ? Modele::where('brand_id', $demande->brand_id)->orderBy('name')->get()
            : collect();

        $groupPersons = $demande->groupPersons();

        return view('demandes.show', compact('demande', 'matches', 'villes', 'brands', 'prefillModeles', 'groupPersons'));
    }

    /** Détail d'une proposition de véhicule — page façon « détail annonce ». */
    public function showProposition($demandeId, $replyId)
    {
        $demande = Demande::public()->findOrFail($demandeId);
        $reply   = \App\Models\DemandeReply::where('demande_id', $demande->id)
            ->where('status', 'visible')
            ->findOrFail($replyId);

        abort_unless($reply->proposalData() !== null, 404);

        return view('demandes.proposition', compact('demande', 'reply'));
    }

    public function create(Request $request)
    {
        $prefill = $request->query();

        // Resolve brand/modele/ville names coming from /achat into their IDs.
        if (!empty($prefill['marque'])) {
            $brandName = str_replace('-', ' ', $prefill['marque']);
            $brand = Brand::whereRaw('UPPER(name) = ?', [mb_strtoupper($brandName)])->first();
            if ($brand) $prefill['brand_id'] = $brand->id;
        }
        if (!empty($prefill['modele']) && !empty($prefill['brand_id'])) {
            $modeleName = ucwords(str_replace('_', ' ', $prefill['modele']));
            $modele = Modele::where('brand_id', $prefill['brand_id'])
                ->whereRaw('UPPER(name) = ?', [mb_strtoupper($modeleName)])->first();
            if ($modele) $prefill['modele_id'] = $modele->id;
        }
        if (!empty($prefill['ville'])) {
            $villeName = str_replace('_', ' ', $prefill['ville']);
            $ville = Ville::whereRaw('UPPER(name) = ?', [mb_strtoupper($villeName)])->first();
            if ($ville) $prefill['ville_id'] = $ville->id;
        }

        return view('demandes.create', [
            'brands'  => Brand::orderBy('name')->get(),
            'villes'  => Ville::orderBy('name')->get(),
            'modeles' => !empty($prefill['brand_id'])
                ? Modele::where('brand_id', $prefill['brand_id'])->orderBy('name')->get()
                : collect(),
            'prefill' => $prefill,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'         => ['required', 'string', 'max:120'],
            'telephone'   => ['required', 'string', 'regex:/^(?:\+?212|0)[\s\-]?[5-7]\d{8}$/'],
            'email'       => ['nullable', 'email', 'max:160'],
            'brand_id'    => ['nullable', 'exists:brands,id'],
            'modele_id'   => ['nullable', 'exists:models,id'],
            'ville_id'    => ['nullable', 'exists:villes,id'],
            'color_id'    => ['nullable', 'exists:colors,id'],
            'year_min'    => ['nullable', 'integer', 'min:1980', 'max:' . (date('Y') + 1)],
            'year_max'    => ['nullable', 'integer', 'min:1980', 'max:' . (date('Y') + 1)],
            'price_min'   => ['nullable', 'integer', 'min:0'],
            'price_max'   => ['nullable', 'integer', 'min:0'],
            'km_max'      => ['nullable', 'integer', 'min:0'],
            'boite'       => ['nullable', 'in:auto,manuelle,any'],
            'carburant'   => ['nullable', 'string', 'max:40'],
            'raw_query'   => ['nullable', 'string', 'max:255'],
            'comment'     => ['nullable', 'string', 'max:1000'],
            'source'      => ['nullable', 'in:front,list,direct'],
            'website'     => ['nullable', 'size:0'],
        ]);

        if (!empty($data['year_min']) && !empty($data['year_max']) && $data['year_min'] > $data['year_max']) {
            return back()->withErrors(['year_min' => 'L’année minimale doit être inférieure à l’année maximale.'])->withInput();
        }

        $phone = $this->normalizePhone($data['telephone']);

        $client = Auth::guard('client')->user();

        $demande = Demande::create([
            'client_id'  => $client?->id,
            'nom'        => $data['nom'],
            'telephone'  => $phone,
            'email'      => $data['email'] ?? ($client?->email),
            'brand_id'   => $data['brand_id']  ?? null,
            'modele_id'  => $data['modele_id'] ?? null,
            'ville_id'   => $data['ville_id']  ?? null,
            'color_id'   => $data['color_id']  ?? null,
            'year_min'   => $data['year_min']  ?? null,
            'year_max'   => $data['year_max']  ?? null,
            'price_min'  => $data['price_min'] ?? null,
            'price_max'  => $data['price_max'] ?? null,
            'km_max'     => $data['km_max']    ?? null,
            'boite'      => $data['boite']     ?? 'any',
            'carburant'  => $data['carburant'] ?? null,
            'raw_query'  => $data['raw_query'] ?? null,
            'comment'    => $data['comment']   ?? null,
            'status'     => 'approved',
            'source'     => $data['source']    ?? 'list',
            'ip_hash'    => hash('sha256', $request->ip() . config('app.key')),
            'expires_at' => now()->addDays(30),
        ]);

        return redirect()->route('demandes.confirmation', $demande->id)
            ->with('success', 'Votre demande a été soumise. L’administrateur vous contactera après validation.');
    }

    public function confirmation($id)
    {
        $demande = Demande::findOrFail($id);
        return view('demandes.confirmation', compact('demande'));
    }

    public function storeReply(Request $request, Demande $demande)
    {
        $data = $request->validate([
            'message'       => ['required', 'string', 'max:1000'],
            'annonce_id'    => ['nullable', 'exists:annonces,id'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
        ]);

        DemandeReply::create([
            'demande_id'    => $demande->id,
            'client_id'     => Auth::guard('client')->id(),
            'annonce_id'    => $data['annonce_id']    ?? null,
            'message'       => $data['message'],
            'contact_phone' => $data['contact_phone'] ?? null,
            'status'        => 'visible',
        ]);

        $demande->increment('replies_count');

        return back()->with('success', 'Votre réponse a été envoyée au demandeur.');
    }

    public function report(Request $request, Demande $demande)
    {
        return back()->with('info', 'Merci, votre signalement a été enregistré.');
    }

    public function propose(Request $request, Demande $demande)
    {
        $data = $request->validate([
            'proposer_name'  => ['required', 'string', 'max:120'],
            'proposer_phone' => ['required', 'string', 'regex:/^(?:\+?212|0)[\s\-]?[5-7]\d{8}$/'],
            'mark'           => ['required', 'exists:brands,id'],
            'modele'         => ['required', 'exists:models,id'],
            'year'           => ['required', 'integer', 'min:1980', 'max:' . (date('Y') + 1)],
            'km'             => ['required', 'integer', 'min:0'],
            'prix'           => ['required', 'integer', 'min:0'],
            'city'           => ['nullable', 'string', 'max:60'],
            'carburant'      => ['nullable', 'string', 'max:40'],
            'box'            => ['nullable', 'string', 'max:40'],
            'description'    => ['nullable', 'string', 'max:1500'],
            'images'         => ['nullable', 'array', 'max:8'],
            'images.*'       => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'website'        => ['nullable', 'size:0'],
        ]);

        $proposerPhone = $this->normalizePhone($data['proposer_phone']);

        // Resolve brand/modele IDs back to names for display.
        $data['mark']   = Brand::find($data['mark'])?->name ?? $data['mark'];
        $data['modele'] = Modele::find($data['modele'])?->name ?? $data['modele'];

        // Store uploaded photos (if any) under storage/app/public/propositions/{demande_id}/
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                if (!$img->isValid()) continue;
                $path = $img->store('propositions/' . $demande->id, 'public');
                $imagePaths[] = $path;
            }
        }
        $imageUrls = array_map(fn ($p) => asset('storage/' . $p), $imagePaths);

        // Save as a reply so admins can see it on the demande page. On stocke
        // aussi les données structurées (meta) pour l'affichage en carte, et on
        // NE met PAS le téléphone dans le message (il est masqué sur la carte).
        DemandeReply::create([
            'demande_id'    => $demande->id,
            'client_id'     => Auth::guard('client')->id(),
            'annonce_id'    => null,
            'message'       =>
                "Proposition de véhicule :\n" .
                "- {$data['mark']} {$data['modele']} ({$data['year']})\n" .
                "- {$data['km']} km · " . number_format($data['prix'], 0, ',', ' ') . " DH\n" .
                ($data['city']      ? "- Ville : {$data['city']}\n" : '') .
                ($data['carburant'] ? "- Carburant : {$data['carburant']}\n" : '') .
                ($data['box']       ? "- Boîte : {$data['box']}\n" : '') .
                ($data['description'] ? "\n{$data['description']}\n" : '') .
                "\nContact : {$data['proposer_name']}",
            'contact_phone' => $proposerPhone,
            'status'        => 'visible',
            'meta'          => [
                'proposer_name' => $data['proposer_name'],
                'mark'          => $data['mark'],
                'modele'        => $data['modele'],
                'year'          => $data['year'],
                'km'            => $data['km'],
                'prix'          => $data['prix'],
                'city'          => $data['city'] ?? null,
                'carburant'     => $data['carburant'] ?? null,
                'box'           => $data['box'] ?? null,
                'description'   => $data['description'] ?? null,
                'images'        => $imagePaths,
            ],
        ]);
        $demande->increment('replies_count');

        // Notify admin by email (best-effort — don't block the user if SMTP fails).
        try {
            $demandeUrl = route('demandes.show', $demande->id);
            $body =
                "Un utilisateur a proposé un véhicule pour une demande.\n\n" .
                "DEMANDE #{$demande->id}\n" .
                "- Demandeur : {$demande->nom} ({$demande->telephone})\n" .
                "- Recherche : " . ($demande->brand?->name ?? '—') . ' ' . ($demande->modele?->name ?? '') . "\n" .
                "- Lien : {$demandeUrl}\n\n" .
                "PROPOSITION\n" .
                "- Vendeur : {$data['proposer_name']} — {$proposerPhone}\n" .
                "- Véhicule : {$data['mark']} {$data['modele']} ({$data['year']})\n" .
                "- Km : {$data['km']}\n" .
                "- Prix : " . number_format($data['prix'], 0, ',', ' ') . " DH\n" .
                ($data['city']      ? "- Ville : {$data['city']}\n" : '') .
                ($data['carburant'] ? "- Carburant : {$data['carburant']}\n" : '') .
                ($data['box']       ? "- Boîte : {$data['box']}\n" : '') .
                ($data['description'] ? "\nDescription :\n{$data['description']}\n" : '') .
                (count($imageUrls) ? "\nPhotos (" . count($imageUrls) . ") :\n- " . implode("\n- ", $imageUrls) . "\n" : '');

            $subjectCar = trim(($demande->brand?->name ?? '') . ' ' . ($demande->modele?->name ?? ''));
            if ($subjectCar === '') $subjectCar = 'véhicule';
            $subject = "Nouvelle proposition pour la demande de {$demande->nom} -- {$subjectCar}";

            Mail::raw($body, function ($m) use ($subject, $imagePaths) {
                $m->to('contact@nado.ma')->subject($subject);
                foreach ($imagePaths as $p) {
                    $full = Storage::disk('public')->path($p);
                    if (is_file($full)) $m->attach($full);
                }
            });
        } catch (\Throwable $e) {
            Log::warning('Demande propose email failed: ' . $e->getMessage());
        }

        // Notifier TOUS les chercheurs du même groupe (même voiture + années
        // proches), pas seulement l'auteur de cette demande — par email.
        try {
            $demandeUrl = route('demandes.show', $demande->id);
            $carLabel   = trim(($demande->brand?->name ?? '') . ' ' . ($demande->modele?->name ?? ''));
            if ($carLabel === '') $carLabel = 'votre recherche';

            // Destinataires distincts (nom + email) sur tout le groupe.
            $recipients = $demande->groupPersons()
                ->map(fn ($p) => ['nom' => $p->nom, 'email' => ($p->email ?: $p->client?->email)])
                ->filter(fn ($r) => ! empty($r['email']))
                ->unique('email')
                ->values();

            foreach ($recipients as $r) {
                $sBody =
                    "Bonjour {$r['nom']},\n\n" .
                    "Bonne nouvelle : un vendeur vient de proposer un véhicule pour {$carLabel}.\n\n" .
                    "PROPOSITION\n" .
                    "- {$data['mark']} {$data['modele']} ({$data['year']})\n" .
                    "- {$data['km']} km · " . number_format($data['prix'], 0, ',', ' ') . " DH\n" .
                    ($data['city'] ? "- Ville : {$data['city']}\n" : '') .
                    "\nConsultez la proposition ici :\n{$demandeUrl}\n\n" .
                    "L'équipe NADO.";

                Mail::raw($sBody, function ($m) use ($r) {
                    $m->to($r['email'])->subject('Une proposition a été faite pour votre demande — NADO');
                });
            }
        } catch (\Throwable $e) {
            Log::warning('Demande propose seeker notification failed: ' . $e->getMessage());
        }

        return redirect()->route('demandes.show', $demande->id)
            ->with('propose_success', 'Merci ! Votre proposition a bien été envoyée. Un agent va vous contacter.');
    }

    private function normalizePhone(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw);
        if (Str::startsWith($digits, '212')) return '+' . $digits;
        if (Str::startsWith($digits, '0'))   return '+212' . substr($digits, 1);
        return '+' . $digits;
    }
}
