<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Annonces;
use App\Models\NadoAutoEnchere;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\NadoAutoEncheresBid;
use App\Models\Demande;
use App\Models\Brand;
use App\Models\Ville;
use App\Models\Color;
use App\Models\Modele;

class ClientDashboardController extends Controller
{
    public function show($id)
    {
        $client = Auth::guard('client')->user();

        // Try to find in NadoAutoEnchere first
        $vehicule = NadoAutoEnchere::with('media')->find($id);
        if ($vehicule) {
            if (!$client || $client->phone !== $vehicule->phone) {
                $vehicule->increment('views');
            }

            return view('client.dashboard', compact('vehicule'));
        }

        // Otherwise try to find in Annonces
        $annonce = Annonces::with('media')->find($id);
        if ($annonce) {
            if (!$client || $client->phone !== $annonce->phone) {
                $annonce->increment('views');
            }

            return view('client.dashboard', compact('annonce'));
        }

        abort(404, 'Aucune annonce ou véhicule trouvé.');
    }

    public function index()
    {
        $client = Auth::guard('client')->user();

        if ($client->type === 'flotte&enchere') {
            // 🔹 Charger les véhicules (enchères)
            $vehicules = NadoAutoEnchere::where('phone', $client->phone)->get();

            $viewsLabels = $vehicules->map(fn($v) => Str::limit($v->titre, 15))->toArray();
            $viewsData = $vehicules->pluck('views')->toArray();

            $publiees = $vehicules->filter(fn($v) => ($v->active == 1) && strtolower(trim($v->status)) === 'disponible')->count();
            $enAttente = $vehicules->count() - $publiees;

            // Client enchère → ses favoris sont des ENCHÈRES (table enchere_favorites).
            $favoris = method_exists($client, 'enchereFavorites') ? $client->enchereFavorites()->get() : collect();

            // 🔹 Préparer les données des offres (enchères)
            $offersLabels = [];
            $offersData = [];

            foreach ($vehicules as $vehicule) {
                $offerCount = NadoAutoEncheresBid::where('enchere_id', $vehicule->id)->count();
                $offersLabels[] = Str::limit($vehicule->titre, 15);
                $offersData[] = $offerCount;
            }

            return view('client.dashboard', compact(
                'vehicules',
                'publiees',
                'enAttente',
                'favoris',
                'viewsLabels',
                'viewsData',
                'offersLabels',
                'offersData'
            ));
        } else {
            // 🔹 Charger les annonces classiques
            $annonces = Annonces::where('phone', $client->phone)->get();

            $viewsLabels = $annonces->map(fn($a) => Str::limit($a->titre, 15))->toArray();
            $viewsData = $annonces->pluck('views')->toArray();

            $publiees = $annonces->filter(fn($a) => ($a->active == 1) && strtolower(trim($a->status)) === 'disponible')->count();
            $enAttente = $annonces->count() - $publiees;

            $favoris = method_exists($client, 'favorites') ? $client->favorites()->get() : collect();

            // 🔹 Pour éviter l'erreur dans le Blade
            $offersLabels = [];
            $offersData = [];

            return view('client.dashboard', compact(
                'annonces',
                'publiees',
                'enAttente',
                'favoris',
                'viewsLabels',
                'viewsData',
                'offersLabels',
                'offersData'
            ));
        }
    }

    /** Mes demandes (recherches) + les réponses/propositions reçues. */
    public function myDemandes()
    {
        $client = Auth::guard('client')->user();

        $demandes = Demande::where('client_id', $client->id)
            ->with(['brand', 'modele', 'ville'])
            ->latest()
            ->get();

        // Pour chaque demande : les réponses/propositions de TOUT le groupe voiture.
        foreach ($demandes as $d) {
            $memberIds = $d->groupMembers()->pluck('id');
            $d->setRelation('groupReplies',
                \App\Models\DemandeReply::whereIn('demande_id', $memberIds)
                    ->where('status', 'visible')
                    // Masquer les lignes auto-générées par le matcher (notif de correspondance).
                    // NULL-safe : garder les propositions sans message.
                    ->where(fn ($q) => $q->whereNull('message')
                        ->orWhere('message', '!=', \App\Services\DemandeMatcher::MATCH_MESSAGE))
                    ->with(['client', 'annonce'])
                    ->latest()
                    ->get()
            );
        }

        return view('client.demandes', compact('demandes'));
    }

    /** Renouveler une demande : la remonte en 1re place + prolonge l'échéance. */
    public function renewDemande(Request $request, $id)
    {
        $client  = Auth::guard('client')->user();
        $demande = Demande::where('client_id', $client->id)->findOrFail($id);

        // created_at = maintenant → premier dans les listes triées par date.
        $demande->created_at = now();
        $demande->expires_at = now()->addDays(30);
        if (in_array($demande->status, [null, 'expired', 'rejected'], true)) {
            $demande->status = 'approved';
        }
        $demande->save();

        return back()->with('success', 'Demande renouvelée ✅');
    }

    /** Formulaire d'édition d'une demande — propriétaire uniquement. */
    public function editDemande($id)
    {
        $client  = Auth::guard('client')->user();
        $demande = Demande::where('client_id', $client->id)->findOrFail($id);

        return view('client.demandes-edit', [
            'demande' => $demande,
            'brands'  => Brand::orderBy('name')->get(),
            'villes'  => Ville::orderBy('name')->get(),
            'colors'  => Color::orderBy('name')->get(),
            'modeles' => $demande->brand_id
                ? Modele::where('brand_id', $demande->brand_id)->orderBy('name')->get()
                : collect(),
        ]);
    }

    /** Enregistre les modifications d'une demande — propriétaire uniquement. */
    public function updateDemande(Request $request, $id)
    {
        $client  = Auth::guard('client')->user();
        $demande = Demande::where('client_id', $client->id)->findOrFail($id);

        $data = $request->validate([
            'nom'        => ['required', 'string', 'max:120'],
            'telephone'  => ['required', 'string', 'regex:/^(?:\+?212|0)[\s\-]?[5-7]\d{8}$/'],
            'email'      => ['nullable', 'email', 'max:160'],
            'brand_id'   => ['nullable', 'exists:brands,id'],
            'modele_id'  => ['nullable', 'exists:models,id'],
            'ville_id'   => ['nullable', 'exists:villes,id'],
            'color_id'   => ['nullable', 'exists:colors,id'],
            'year_min'   => ['nullable', 'integer', 'min:1980', 'max:' . (date('Y') + 1)],
            'year_max'   => ['nullable', 'integer', 'min:1980', 'max:' . (date('Y') + 1)],
            'price_min'  => ['nullable', 'integer', 'min:0'],
            'price_max'  => ['nullable', 'integer', 'min:0'],
            'km_max'     => ['nullable', 'integer', 'min:0'],
            'boite'      => ['nullable', 'in:auto,manuelle,any'],
            'carburant'  => ['nullable', 'string', 'max:40'],
            'comment'    => ['nullable', 'string', 'max:1000'],
        ]);

        if (! empty($data['year_min']) && ! empty($data['year_max']) && $data['year_min'] > $data['year_max']) {
            return back()
                ->withErrors(['year_min' => 'L’année minimale doit être inférieure à l’année maximale.'])
                ->withInput();
        }

        $demande->update([
            'nom'       => $data['nom'],
            'telephone' => $data['telephone'],
            'email'     => $data['email'] ?? $demande->email,
            'brand_id'  => $data['brand_id']  ?? null,
            'modele_id' => $data['modele_id'] ?? null,
            'ville_id'  => $data['ville_id']  ?? null,
            'color_id'  => $data['color_id']  ?? null,
            'year_min'  => $data['year_min']  ?? null,
            'year_max'  => $data['year_max']  ?? null,
            'price_min' => $data['price_min'] ?? null,
            'price_max' => $data['price_max'] ?? null,
            'km_max'    => $data['km_max']    ?? null,
            'boite'     => $data['boite']     ?? 'any',
            'carburant' => $data['carburant'] ?? null,
            'comment'   => $data['comment']   ?? null,
        ]);

        return redirect()->route('client.mes-demandes')->with('success', 'Demande modifiée ✅');
    }

    /** Supprime une demande — propriétaire uniquement. */
    public function destroyDemande($id)
    {
        $client  = Auth::guard('client')->user();
        $demande = Demande::where('client_id', $client->id)->findOrFail($id);
        $demande->delete();

        return redirect()->route('client.mes-demandes')->with('success', 'Demande supprimée 🗑️');
    }

    public function favorites()
    {
        $client = Auth::guard('client')->user();
        $favorites = $client->favorites;
        return view('client.favorite', compact('favorites'));
    }
}
