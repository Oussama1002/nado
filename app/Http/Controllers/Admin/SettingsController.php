<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * Panneau de configuration unifié (doc 02 — §6.bis.3).
 * Un seul controller pilote tous les groupes de réglages.
 */
class SettingsController extends Controller
{
    /**
     * Métadonnées d'affichage des groupes (ordre + libellé + icône Bootstrap).
     * Seuls les groupes réellement présents en base sont affichés.
     */
    public const GROUPS = [
        'payments'   => ['label' => 'Paiements & Passerelles', 'icon' => 'bi-credit-card'],
        'packs'      => ['label' => 'Packs annonces',          'icon' => 'bi-box-seam'],
        'pro_plans'  => ['label' => 'Abonnements Pro',         'icon' => 'bi-person-badge'],
        'boosts'     => ['label' => 'Boosts & Upsells',        'icon' => 'bi-rocket-takeoff'],
        'auctions'   => ['label' => 'Enchères',                'icon' => 'bi-hammer'],
        'finance'    => ['label' => 'Financement',             'icon' => 'bi-cash-coin'],
        'leads'      => ['label' => 'Leads & Wallet',          'icon' => 'bi-envelope-paper'],
        'ads'        => ['label' => 'Publicité',               'icon' => 'bi-megaphone'],
        'estimation' => ['label' => 'Estimation payante',      'icon' => 'bi-file-earmark-text'],
        'partners'   => ['label' => 'Partenaires & commissions', 'icon' => 'bi-people'],
        'global'     => ['label' => 'Modules & Kill-switches',  'icon' => 'bi-toggles'],
    ];

    /** GET /admin/settings — vue d'ensemble (tuiles par groupe). */
    public function index()
    {
        $counts = Setting::selectRaw('`group`, COUNT(*) as total')
            ->groupBy('group')
            ->pluck('total', 'group');

        return view('admin.settings.index', [
            'groups' => self::GROUPS,
            'counts' => $counts,
        ]);
    }

    /** GET /admin/settings/{group} — formulaire dynamique d'un groupe. */
    public function group(string $group)
    {
        abort_unless(array_key_exists($group, self::GROUPS), 404);

        $settings = Setting::where('group', $group)
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        return view('admin.settings.group', [
            'group'    => $group,
            'meta'     => self::GROUPS[$group],
            'settings' => $settings,
            'groups'   => self::GROUPS,
        ]);
    }

    /** POST /admin/settings/{group} — enregistre toutes les valeurs du groupe. */
    public function update(Request $request, string $group)
    {
        abort_unless(array_key_exists($group, self::GROUPS), 404);

        $settings = Setting::where('group', $group)->get();
        $submitted = $request->input('settings', []);

        foreach ($settings as $setting) {
            if ($setting->type === 'bool') {
                // Une case non cochée n'est pas envoyée → false.
                $value = ! empty($submitted[$setting->key]);
            } else {
                // On ne touche pas une clé absente du formulaire (ex. champ secret laissé vide).
                if (! array_key_exists($setting->key, $submitted)) {
                    continue;
                }
                $value = $submitted[$setting->key];

                if ($setting->is_secret && $value === '') {
                    continue; // ne pas écraser un secret par du vide
                }
            }

            Setting::set($setting->key, $value);
        }

        return redirect()
            ->route('admin.settings.group', $group)
            ->with('success', 'Configuration « ' . self::GROUPS[$group]['label'] . ' » mise à jour.');
    }

    /** POST /admin/settings/toggle/{key} — bascule rapide d'un flag (AJAX). */
    public function toggle(string $key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();
        abort_unless($setting->type === 'bool', 422, 'Ce réglage n\'est pas un interrupteur.');

        $new = ! Setting::enabled($key);
        Setting::set($key, $new);

        if (request()->expectsJson()) {
            return response()->json(['key' => $key, 'enabled' => $new]);
        }

        return redirect()->back()->with('success', 'Réglage mis à jour.');
    }
}
