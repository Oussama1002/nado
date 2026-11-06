<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Revue admin des vérifications de situation légale (opposition NARSA).
 * L'admin regarde la capture d'écran fournie par le vendeur et confirme/rejette.
 */
class LegalCheckController extends Controller
{
    /** GET /admin/legal-checks — liste (filtre statut admin). */
    public function index(Request $request)
    {
        $filter = $request->input('status', 'pending');

        $query = LegalCheck::with(['annonce', 'client'])->latest();
        if (in_array($filter, ['pending', 'confirmed', 'rejected'], true)) {
            $query->where('admin_status', $filter);
        }

        $checks = $query->paginate(30)->withQueryString();

        $counts = LegalCheck::selectRaw('admin_status, COUNT(*) as total')
            ->groupBy('admin_status')
            ->pluck('total', 'admin_status');

        return view('admin.legalchecks.index', compact('checks', 'filter', 'counts'));
    }

    /** GET /admin/legal-checks/{legalCheck} — détail + capture. */
    public function show(LegalCheck $legalCheck)
    {
        $legalCheck->load(['annonce', 'client']);

        return view('admin.legalchecks.show', compact('legalCheck'));
    }

    /** GET /admin/legal-checks/{legalCheck}/screenshot — sert la capture (disque privé). */
    public function screenshot(LegalCheck $legalCheck)
    {
        abort_unless($legalCheck->screenshot_path && Storage::disk('local')->exists($legalCheck->screenshot_path), 404);

        return Storage::disk('local')->response($legalCheck->screenshot_path);
    }

    /** POST /admin/legal-checks/{legalCheck}/confirm */
    public function confirm(Request $request, LegalCheck $legalCheck)
    {
        $this->review($request, $legalCheck, 'confirmed');

        return redirect()->back()->with('success', 'Vérification confirmée.');
    }

    /** POST /admin/legal-checks/{legalCheck}/reject */
    public function reject(Request $request, LegalCheck $legalCheck)
    {
        $this->review($request, $legalCheck, 'rejected');

        return redirect()->back()->with('success', 'Vérification rejetée.');
    }

    private function review(Request $request, LegalCheck $legalCheck, string $status): void
    {
        $legalCheck->update([
            'admin_status' => $status,
            'admin_id'     => Auth::guard('admin')->id(),
            'reviewed_at'  => now(),
            'notes'        => $request->input('notes', $legalCheck->notes),
        ]);
    }
}
