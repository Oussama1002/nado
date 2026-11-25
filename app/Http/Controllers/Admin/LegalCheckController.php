<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalCheck;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

        // Envoie le résultat au vendeur par email ET/OU WhatsApp selon le canal
        // choisi au moment de la demande différée. Best-effort : un échec d'un
        // canal (SMTP KO, WhatsApp en panne) ne bloque pas la revue admin.
        if ($request->boolean('notify_client', true)) {
            $channel = $legalCheck->contact_channel ?: 'email';

            if (in_array($channel, ['email', 'both'], true)) {
                $this->notifyClientResult($legalCheck);
            }
            if (in_array($channel, ['whatsapp', 'both'], true)) {
                $this->notifyClientResultWhatsApp($legalCheck);
            }
        }
    }

    /** WhatsApp best-effort : template "verification_legale_resultat" au vendeur. */
    private function notifyClientResultWhatsApp(LegalCheck $legalCheck): void
    {
        if (! feature_on('whatsapp.enabled')) {
            return;
        }

        $legalCheck->loadMissing('client');
        $phone = $legalCheck->contact_whatsapp ?: optional($legalCheck->client)->phone;
        if (! $phone) {
            return;
        }

        $matricule = $legalCheck->matricule_full ?: '—';
        $verdict = $legalCheck->admin_status === 'confirmed'
            ? ($legalCheck->declared_status === 'opposition'
                ? 'Opposition active detectee sur le registre NARSA'
                : 'Aucune opposition, le vehicule est libre')
            : "Nous n'avons pas pu confirmer la situation legale";

        $lang = (optional($legalCheck->client)->lang === 'ar') ? 'ar' : 'fr';
        if ($lang === 'ar') {
            $verdict = $legalCheck->admin_status === 'confirmed'
                ? ($legalCheck->declared_status === 'opposition'
                    ? 'تم رصد معارضة نشطة على سجل NARSA'
                    : 'لا توجد معارضة، السيارة حرة')
                : 'لم نتمكن من تأكيد الوضعية القانونية';
        }

        try {
            app(WhatsAppService::class)->sendTemplate(
                $phone,
                'verification_legale_resultat',
                $lang,
                [$matricule, $verdict]
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Email best-effort du résultat au vendeur rattaché (si email connu). */
    private function notifyClientResult(LegalCheck $legalCheck): void
    {
        $legalCheck->loadMissing('client');
        $client = $legalCheck->client;
        if (! $client || empty($client->email)) {
            return;
        }

        $matricule = $legalCheck->matricule_full ?: '—';
        $verdict = $legalCheck->admin_status === 'confirmed'
            ? ($legalCheck->declared_status === 'opposition'
                ? 'Opposition active détectée sur le registre NARSA.'
                : 'Aucune opposition — le véhicule est libre.')
            : "Nous n'avons pas pu confirmer la situation légale.";
        $notes = trim((string) $legalCheck->notes);

        try {
            Mail::raw(
                "Bonjour {$client->name},\n\n"
                . "Voici le résultat de votre vérification de situation légale (NARSA).\n\n"
                . "Matricule : {$matricule}\n"
                . "Résultat : {$verdict}\n"
                . ($notes !== '' ? "\nNote de l'équipe : {$notes}\n" : '')
                . "\nIndicatif, selon la base publique NARSA — ce n'est pas un certificat officiel.\n\n"
                . "L'équipe NADO — www.nado.ma",
                function ($m) use ($client) {
                    $m->to($client->email)->subject('Résultat de votre vérification légale (NARSA)');
                }
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
