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

    public function favorites()
    {
        $client = Auth::guard('client')->user();
        $favorites = $client->favorites;
        return view('client.favorite', compact('favorites'));
    }
}
