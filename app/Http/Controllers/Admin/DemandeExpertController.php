<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandeExpert;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DemandeExpertController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('status');

        $query = DemandeExpert::query()->latest('created_at');
        if (in_array($filter, ['new', 'contacted', 'done', 'spam'], true)) {
            $query->where('status', $filter);
        }

        $counts = DemandeExpert::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        return view('admin.demande-experts.index', [
            'items'  => $query->paginate(20)->withQueryString(),
            'filter' => $filter ?: 'all',
            'counts' => $counts,
            'total'  => DemandeExpert::count(),
        ]);
    }

    public function show(DemandeExpert $demande_expert)
    {
        return view('admin.demande-experts.show', ['item' => $demande_expert]);
    }

    public function updateStatus(Request $request, DemandeExpert $demande_expert)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['new', 'contacted', 'done', 'spam'])],
        ]);

        $demande_expert->update($data);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroy(DemandeExpert $demande_expert)
    {
        $demande_expert->delete();

        return redirect()->route('admin.demandeexperts.index')
            ->with('success', 'Demande supprimée.');
    }
}
