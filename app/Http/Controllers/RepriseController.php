<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Reprise;
use App\Models\Ville;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RepriseController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        // Montre juste la page d’intro (pas besoin de données)
        return view('reprise.index');
    }

    public function form()
    {
        // La page reste publique : la connexion est exigée au moment où
        // l'utilisateur clique sur le bouton de reprise (voir la vue), puis
        // revérifiée côté serveur dans store().
        $brands = Brand::all();
        $city = Ville::ordered()->get();
        $client = Auth::guard('client')->user(); // null si non connecté

        return view('reprise.reprise', compact('brands', 'city', 'client'));
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
        if (! Auth::guard('client')->check()) {
            return redirect()
                ->route('client.login', ['redirect' => route('reprise.form')])
                ->with('error', 'Veuillez vous connecter pour demander une reprise.');
        }

        $client = Auth::guard('client')->user();

        // 'name' et 'phone' ne sont plus validés depuis la requête : ils sont
        // imposés par le compte connecté (un champ caché est falsifiable).
        // 'rendezvous' et 'contacte' ont été retirés du formulaire.
        $reprise = $request->validate([
            'mark' => ['required', 'string'],
            'modele' => ['required', 'string'],
            'year' => ['required', 'string'],
            'km' => ['required', 'integer', 'min:0'],
            'photos' => ['nullable', 'array'],
            'photos.gauche'  => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'photos.droite'  => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'photos.avant'   => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'photos.arriere' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'city' => ['required', 'string'],
        ]);

        // Stocke les photos sur le disque public et ne garde que les noms de fichiers.
        $photos = [];
        if ($request->hasFile('photos')) {
            // Les cles sont les cotes du vehicule : gauche, droite, avant, arriere.
            foreach ($request->file('photos') as $slot => $photo) {
                if (! $photo) {
                    continue; // zone laissee vide
                }
                $filename = Str::slug($reprise['mark'].'-'.$reprise['modele'].'-'.$slot, '-')
                    .'-'.uniqid().'.'.$photo->getClientOriginalExtension();
                $photo->move(storage_path('app/public/reprises/'), $filename);
                $photos[$slot] = $filename;
            }
        }
        $reprise['photos'] = $photos;

        $reprise['name']  = $client->name;
        $reprise['phone'] = $client->phone;

        // Email retiré du formulaire : toujours celui du compte connecté.
        $reprise['email'] = $client->email;

        Reprise::create($reprise);

        return to_route('reprise.form')->with('success', 'Votre demande a ete envoyer');
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
    public function getModels(Request $request)
    {
        $brandId = $request->brand_id;

        $models = \App\Models\Modele::where('brand_id', $brandId)->get(['id', 'name']);

        return response()->json($models);
    }
}
