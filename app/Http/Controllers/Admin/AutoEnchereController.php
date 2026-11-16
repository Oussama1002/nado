<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Autoenchere;
use App\Models\Annonces;
use App\Models\Client;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;


class AutoEnchereController extends Controller
{
    public function __construct()
    {
        $this->middleware('module.access');
    }

    public function index()
    {
        $enchers = Autoenchere::with(['annonce', 'client'])->latest()->get();
        return view('admin.autoenchere.index', compact('enchers'));
    }

    public function create()
    {
        $annonces = Annonces::all();
        $clients = Client::role('client')->get(); // Assumes you use spatie/laravel-permission
        return view('admin.autoenchere.form', [
            'enchere' => new Autoenchere(),
            'annonces' => $annonces,
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
       

        $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'client_id' => 'required|exists:clients,id',
            'proposition' => 'required|numeric|min:0',
        ]);

        Autoenchere::create($request->all());

        return redirect()->route('admin.autoenchere.index')->with('success', 'Enchère ajoutée.');
    }

    public function edit($id)
    {
        $enchere = Autoenchere::findOrFail($id);
        $annonces = Annonces::all();
        $clients = Client::role('client')->get();

        return view('admin.autoenchere.form', compact('enchere', 'annonces', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'client_id' => 'required|exists:clients,id',
            'proposition' => 'required|numeric|min:0',
        ]);

        $enchere = Autoenchere::findOrFail($id);
        $enchere->update($request->all());

        return redirect()->route('admin.autoenchere.index')->with('success', 'Enchère mise à jour.');
    }
    public function show($id)
    {
        $enchere = Autoenchere::with(['annonce', 'client'])->findOrFail($id);
        return view('admin.autoenchere.show', compact('enchere'));
    }
    
    public function destroy($id)
    {
        Autoenchere::destroy($id);
        return redirect()->route('admin.autoenchere.index')->with('success', 'Enchère supprimée.');
    }
}
