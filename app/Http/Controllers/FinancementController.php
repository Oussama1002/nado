<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Financement;
use App\Models\Ville;
use Illuminate\Http\Request;

use App\Models\ClientType;

class FinancementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientTypes = ClientType::all();
        return view('financement', ['brands' => Brand::all(), 'city' => Ville::ordered()->get(), 'clientTypes' => $clientTypes]);
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
        $financement = $request->validate([
            'name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'city' => ['required', 'string'],
            'mark' => ['required', 'string'],
            'modele' => ['required', 'string'],
            'year' => ['required', 'numeric'],
            'situationprof' => ['required', 'string'],
            'creditsencours' => ['required', 'string'],
            'duree' => ['required', 'string'],
            'prixcar' => ['required', 'string'],
            'montant' => ['required', 'string'],
            'creditsdh' => ['required', 'string'],
            'email' => ['required', 'string'],
            'rendezvous' => ['required', 'string'],
            'cin' => ['required', 'string'],

        ]);
        Financement::create($financement);

        return to_route('financement.index')->with('success', 'Votre demande a ete envoyer');
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
}
