<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SavedSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Recherches sauvegardées (discovery.saved_searches) — espace client.
 * Les paramètres stockés sont le jeu de filtres AnnonceFilter (source unique).
 */
class SavedSearchController extends Controller
{
    /** Paramètres de filtre pris en compte (= ceux lus par AnnonceFilter). */
    private const PARAM_KEYS = [
        'marque', 'modele', 'carburant', 'box', 'ville',
        'annee_min', 'annee_max', 'annee', 'bodywork', 'prix', 'km', 'mensualite_max',
    ];

    public function index()
    {
        $searches = SavedSearch::where('client_id', Auth::guard('client')->id())
            ->latest()->get();

        return view('client.saved-searches', compact('searches'));
    }

    public function store(Request $request)
    {
        abort_unless(feature_on('discovery.saved_searches'), 404);

        $params = [];
        foreach (self::PARAM_KEYS as $k) {
            $v = $request->input($k);
            if ($v !== null && $v !== '') {
                $params[$k] = is_string($v) ? trim($v) : $v;
            }
        }

        $search = SavedSearch::create([
            'client_id'  => Auth::guard('client')->id(),
            'label'      => $request->filled('label') ? trim($request->input('label')) : null,
            'params'     => $params,
            'notify_new' => true,
            'channel'    => 'email',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'id' => $search->id, 'message' => __('messages.discovery.search_saved')]);
        }

        return back()->with('success', __('messages.discovery.search_saved'));
    }

    public function rename(Request $request, $id)
    {
        $search = $this->ownedOrFail($id);
        $data = $request->validate(['label' => ['nullable', 'string', 'max:120']]);
        $search->update(['label' => $data['label'] ?? null]);

        return back()->with('success', __('messages.discovery.search_saved'));
    }

    public function toggle($id)
    {
        $search = $this->ownedOrFail($id);
        $search->update(['notify_new' => ! $search->notify_new]);

        return back()->with('success', $search->notify_new
            ? __('messages.discovery.alerts_on')
            : __('messages.discovery.alerts_off'));
    }

    public function destroy($id)
    {
        $this->ownedOrFail($id)->delete();

        return back()->with('success', __('messages.discovery.delete'));
    }

    /** Garantit que la recherche appartient au client connecté. */
    private function ownedOrFail($id): SavedSearch
    {
        return SavedSearch::where('id', $id)
            ->where('client_id', Auth::guard('client')->id())
            ->firstOrFail();
    }
}
