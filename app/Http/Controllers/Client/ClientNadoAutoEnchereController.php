<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NadoAutoEnchere;
use App\Models\Annonces;
use App\Models\Brand;
use App\Models\Modele;
use App\Models\Ville;

class ClientNadoAutoEnchereController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();

        // Get all encheres belonging to the authenticated client by phone
        $vehicules = NadoAutoEnchere::where('phone', $client->phone)->latest()->get();
        // Ses annonces classiques (même téléphone).
        $annonces  = Annonces::where('phone', $client->phone)->latest()->get();

        $brands = Brand::all();
        $cities = Ville::pluck('name');

        return view('client.nadoautoencheres.index', compact('vehicules', 'annonces', 'brands', 'cities'));
    }

    /** Show edit form */
    public function edit($id)
    {
        $client = Auth::guard('client')->user();
        $vehicule = NadoAutoEnchere::where('phone', $client->phone)
                                   ->where('id', $id)
                                   ->firstOrFail();

        $brands = Brand::all();
        $modeles = Modele::all();
        $cities = Ville::pluck('name');

        return view('client.nadoautoencheres.edit', compact('vehicule', 'brands', 'modeles', 'cities'));
    }

    /** Update annonce */
    public function update(Request $request, $id)
    {
        $client = Auth::guard('client')->user();
        $vehicule = NadoAutoEnchere::where('phone', $client->phone)
                                   ->where('id', $id)
                                   ->firstOrFail();

        $request->validate([
            'titre' => 'required|string|max:255',
            'prix'  => 'required|numeric',
            'city'  => 'required|string',
        ]);

        $vehicule->update($request->all());

        return redirect()->route('client.nadoautoencheres.index')
                         ->with('success', 'Annonce mise à jour avec succès ✅');
    }

    /** Delete annonce */
    public function destroy($id)
    {
        $client = Auth::guard('client')->user();
        $vehicule = NadoAutoEnchere::where('phone', $client->phone)
                                   ->where('id', $id)
                                   ->firstOrFail();

        $vehicule->delete();

        return redirect()->route('client.nadoautoencheres.index')
                         ->with('success', 'Annonce supprimée avec succès 🗑️');
    }
}
