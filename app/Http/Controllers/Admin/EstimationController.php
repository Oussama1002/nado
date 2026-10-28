<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estimation;

class EstimationController extends Controller
{
    /**
     * List the estimations submitted by clients, newest first.
     */
    public function index()
    {
        $estimations = Estimation::with('client')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.estimations.index', compact('estimations'));
    }

    /**
     * Show a single estimation with its full detail.
     */
    public function show($id)
    {
        $estimation = Estimation::with('client')->findOrFail($id);

        return view('admin.estimations.show', compact('estimation'));
    }

    /**
     * Delete an estimation.
     */
    public function destroy($id)
    {
        $estimation = Estimation::findOrFail($id);
        $estimation->delete();

        return redirect()
            ->route('admin.estimations.index')
            ->with('success', 'Estimation supprimée.');
    }
}
