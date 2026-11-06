<?php

namespace App\Http\Controllers;

use App\Models\Annonces;
use App\Models\LegalCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Flux GUIDÉ de vérification de la situation légale (opposition NARSA).
 *
 * Le service officiel NARSA est une SPA protégée par un reCAPTCHA « je ne suis
 * pas un robot » : impossible (et hors de question) à automatiser côté serveur.
 * On guide donc le vendeur pour qu'il lance lui-même le contrôle, puis il
 * déclare le résultat ici avec une capture d'écran comme preuve.
 *
 * Vie privée : on n'enregistre QUE le statut + la capture (disque privé).
 * Jamais le nom/adresse du propriétaire.
 */
class LegalCheckController extends Controller
{
    /** URL publique officielle du service d'opposition NARSA. */
    private const NARSA_URL = 'https://khadamatnarsa.ma/fr/consultation-de-letat-des-oppositions';

    /** GET — page guidée (gated par le flag). */
    public function form(Request $request)
    {
        abort_unless(feature_on('legalcheck.enabled'), 404);

        $client = Auth::guard('client')->user();

        // Rattachement optionnel à une annonce du vendeur connecté.
        $annonce = null;
        if ($request->filled('annonce') && $client) {
            $annonce = Annonces::where('id', $request->integer('annonce'))
                ->where('phone', $client->phone)
                ->first();
        }

        // Dernière vérif existante (pour feedback vendeur), si annonce rattachée.
        $existing = $annonce
            ? $annonce->legalChecks()->latest()->first()
            : null;

        return view('legalcheck.form', [
            'narsaUrl' => self::NARSA_URL,
            'annonce'  => $annonce,
            'existing' => $existing,
        ]);
    }

    /** POST — enregistre la déclaration + la capture (auth:client, throttle, CSRF). */
    public function store(Request $request)
    {
        abort_unless(feature_on('legalcheck.enabled'), 404);

        $client = Auth::guard('client')->user();
        abort_unless($client, 403);

        $validated = $request->validate([
            'matricule_number' => ['required', 'string', 'max:10'],
            'matricule_letter' => ['required', 'string', 'max:8'],
            'matricule_region' => ['required', 'string', 'max:6'],
            'declared_status'  => ['required', 'in:clear,opposition'],
            'screenshot'       => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'annonce_id'       => ['nullable', 'integer'],
        ], [
            'matricule_number.required' => __('messages.legalcheck.err_number'),
            'matricule_letter.required' => __('messages.legalcheck.err_letter'),
            'matricule_region.required' => __('messages.legalcheck.err_region'),
            'declared_status.required'  => __('messages.legalcheck.err_result'),
            'declared_status.in'        => __('messages.legalcheck.err_result'),
            'screenshot.required'       => __('messages.legalcheck.err_screenshot'),
            'screenshot.mimes'          => __('messages.legalcheck.err_screenshot_type'),
            'screenshot.max'            => __('messages.legalcheck.err_screenshot_size'),
        ]);

        // Sanitize léger : chiffres pour number/region, on garde lettres latines + arabes pour la série.
        $number = preg_replace('/[^0-9]/u', '', $validated['matricule_number']);
        $region = preg_replace('/[^0-9]/u', '', $validated['matricule_region']);
        $letter = trim(preg_replace('/[^\p{Arabic}A-Za-z]/u', '', $validated['matricule_letter']));

        if ($number === '' || $region === '' || $letter === '') {
            return back()
                ->withInput()
                ->withErrors(['matricule_number' => __('messages.legalcheck.err_matricule_invalid')]);
        }

        // Format d'affichage FR/AR-safe : « 27314 و 72 ».
        $full = $number . ' ' . $letter . ' ' . $region;

        // Rattachement annonce : seulement si elle appartient au vendeur connecté.
        $annonceId = null;
        if (! empty($validated['annonce_id'])) {
            $owned = Annonces::where('id', $validated['annonce_id'])
                ->where('phone', $client->phone)
                ->exists();
            if ($owned) {
                $annonceId = (int) $validated['annonce_id'];
            }
        }

        // Capture d'écran sur le disque PRIVÉ (jamais public_html/storage).
        $ext  = $request->file('screenshot')->getClientOriginalExtension();
        $name = 'lc-' . $client->id . '-' . time() . '-' . Str::random(6) . '.' . $ext;
        $path = $request->file('screenshot')->storeAs('legalchecks', $name, 'local');

        LegalCheck::create([
            'annonce_id'       => $annonceId,
            'client_id'        => $client->id,
            'matricule_number' => $number,
            'matricule_letter' => $letter,
            'matricule_region' => $region,
            'matricule_full'   => $full,
            'declared_status'  => $validated['declared_status'],
            'screenshot_path'  => $path,
            'checked_at'       => now(),
            'admin_status'     => 'pending',
        ]);

        return redirect()
            ->route('legalcheck.form', $annonceId ? ['annonce' => $annonceId] : [])
            ->with('success', __('messages.legalcheck.submitted'));
    }
}
