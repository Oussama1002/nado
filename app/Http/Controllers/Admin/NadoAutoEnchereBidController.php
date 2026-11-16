<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NadoAutoEncheresBid;
use App\Models\Client;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;


class NadoAutoEnchereBidController extends Controller
{
    public function __construct()
    {
        $this->middleware('module.access');
    }

   public function index()
{
    $encheres = NadoAutoEncheresBid::with([
            'enchere.client',   // auction owner
            'enchere.bids',     // all bids of this enchere
            'client'            // bidder
        ])
        ->latest()
        ->get();

    // Group by enchere_id and find the max proposition
    $bestPropositions = $encheres
        ->groupBy('enchere_id')
        ->map(function ($group) {
            return $group->max('proposition');
        });

    return view('admin.nadoautoencherebid.index', compact('encheres', 'bestPropositions'));
}

    

    public function export()
{
    $bids = NadoAutoEncheresBid::with(['enchere.brand', 'enchere.modeleRelation', 'client'])->get();

    if ($bids->isEmpty()) {
        return redirect()->back()->with('error', 'Aucune offre trouvée.');
    }

    $filename = 'encheres_' . date('Y-m-d_H-i-s') . '.csv';

    $headers = [
        "Content-Type" => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=\"$filename\"",
    ];

    $callback = function() use ($bids) {
        $file = fopen('php://output', 'w');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($file, ['Véhicule', 'Matricule', 'Ville', 'Entreprise', "Nombre D'offres", 'Enchéreur', 'Meilleure Offre (MAD)', 'Vues']);

        $grouped = $bids->groupBy('enchere_id');

        foreach ($grouped as $enchereId => $group) {
            $enchere = $group->first()->enchere;
            $meilleureOffre = $group->max('proposition');
            $nombreOffres   = $group->count();

            foreach ($group as $bid) {
                fputcsv($file, [
                    ($enchere->brand->name ?? 'Marque supprimée') . ' ' . ($enchere->modeleRelation->name ?? 'Modèle supprimé'),
                    $enchere->matricule ?? 'Matricule supprimé',
                    $bid->client->city ?? 'Pas de Ville',
                    $bid->client->company ?? 'Pas Entreprise',
                    $nombreOffres,
                    $bid->client->name ?? 'Client supprimé',
                    number_format($meilleureOffre, 2, ',', ' '),
                    $enchere->views ?? 0,
                ]);
            }
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}



    
    public function show($id)
    {
        $enchere = NadoAutoEncheresBid::with(['enchere', 'client'])->findOrFail($id);
        return view('admin.nadoautoencherebid.show', compact('enchere'));
    }
    
    public function destroy($id)
    {
        NadoAutoEncheresBid::destroy($id);
        return redirect()->route('admin.nadoautoencherebid.index')->with('success', 'Enchère supprimée.');
    }
}
