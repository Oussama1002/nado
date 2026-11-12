<?php

namespace App\Http\Controllers;

use App\Models\Annonces;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Véhicules sélectionnés depuis la barre de comparaison (?ids=1,2,3), max 3.
        $ids = collect(explode(',', (string) $request->query('ids')))
            ->map(fn ($v) => (int) trim($v))
            ->filter()
            ->unique()
            ->take(3)
            ->values();

        $compareAds = $ids->isNotEmpty()
            ? Annonces::with(['brand', 'modeleRelation'])
                ->where('active', 1)
                ->whereIn('id', $ids->all())
                ->get()
                ->sortBy(fn ($a) => $ids->search($a->id))
                ->values()
            : collect();

        return view('comparison', [
            'compareAds' => $compareAds,
            'ads'        => Annonces::where('active', 1)->orderBy('id', 'desc')->get(),
            'formatDate' => function ($date) {
                return $this->formatDateLabel($date);
            },
        ]);
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
        //
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

    public function getads(Request $request)
    {
        $id = $request->validate([
            'id' => ['required', 'numeric'],

        ]);
        $ads = Annonces::where('id', $id)->get();

        return response()->json($ads);
    }

    public static function formatDateLabel($date)
    {

        $datediff = time() - strtotime($date);
        $tm = 60;
        $res = round($datediff / $tm);
        $tt = 'minutes';

        if ($res == 0) {
            $res = 1;
        }

        if ($res > 50) {
            $res = round($datediff / ($tm * 60));
            $tt = 'heures';
            if ($res > 23) {
                $res = round($datediff / ($tm * 60 * 24));
                $tt = 'jours';
                if ($res > 30) {
                    $res = round($datediff / ($tm * 60 * 24 * 30));
                    $tt = 'mois';
                    if ($res >= 12) {
                        $res = round($datediff / ($tm * 60 * 24 * 30 * 12));
                        $tt = 'ans';
                    }
                }
            }
        }

        return 'Il y a '.$res.' '.$tt;

    }
}
