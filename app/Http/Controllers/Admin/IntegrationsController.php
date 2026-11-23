<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\WhatsAppNumber;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class IntegrationsController extends Controller
{
    /** GET /admin/integrations */
    public function index(WhatsAppService $whatsapp)
    {
        $numbers    = WhatsAppNumber::orderByDesc('is_default')->orderBy('display_number')->get();
        $templates  = $whatsapp->listTemplates(50);

        return view('admin.integrations.index', [
            'numbers'        => $numbers,
            'templates'      => $templates['ok'] ? $templates['templates'] : [],
            'templatesError' => $templates['error'] ?? null,
            'webhookUrl'     => url('/whatsapp/webhook'),
        ]);
    }

    /** POST /admin/integrations/whatsapp — enregistre la connexion WhatsApp. */
    public function saveWhatsApp(Request $request)
    {
        $data = $request->validate([
            'provider'             => 'required|string|max:20',
            'meta_phone_number_id' => 'nullable|string|max:64',
            'meta_waba_id'         => 'nullable|string|max:64',
            'meta_access_token'    => 'nullable|string|max:2000',
            'meta_template_name'   => 'nullable|string|max:120',
            'meta_template_lang'   => 'nullable|string|max:10',
            'webhook_verify_token' => 'nullable|string|max:255',
            'webhook_app_secret'   => 'nullable|string|max:255',
        ]);

        foreach ($data as $suffix => $value) {
            // Ne pas écraser un secret existant quand le champ est vide.
            $full = 'whatsapp.' . $suffix;
            $setting = Setting::where('key', $full)->first();
            if ($setting && $setting->is_secret && ($value === null || $value === '')) {
                continue;
            }
            Setting::set($full, (string) ($value ?? ''));
        }

        return back()->with('success', 'Configuration WhatsApp enregistrée.');
    }

    /** POST /admin/integrations/whatsapp/test */
    public function testWhatsApp(WhatsAppService $whatsapp)
    {
        $result = $whatsapp->testConnection();

        return back()->with($result['ok'] ? 'success' : 'error',
            $result['ok']
                ? 'Connexion OK — ' . ($result['phone'] ?? '?') . ' · ' . ($result['verified_name'] ?? '')
                : 'Échec : ' . $result['error']);
    }

    /** POST /admin/integrations/numbers — ajout manuel. */
    public function storeNumber(Request $request)
    {
        $data = $request->validate([
            'display_number' => 'required|string|max:32',
            'phone_id'       => 'required|string|max:64|unique:whatsapp_numbers,phone_id',
            'label'          => 'nullable|string|max:120',
            'is_default'     => 'nullable|boolean',
        ]);

        if (! empty($data['is_default'])) {
            WhatsAppNumber::query()->update(['is_default' => false]);
        }
        $data['is_default'] = (bool) ($data['is_default'] ?? false);

        WhatsAppNumber::create($data + ['is_active' => true]);

        return back()->with('success', 'Numéro ajouté.');
    }

    /** POST /admin/integrations/numbers/import — depuis Meta via WABA. */
    public function importNumbers(WhatsAppService $whatsapp)
    {
        $result = $whatsapp->listWabaPhoneNumbers();

        if (! $result['ok']) {
            return back()->with('error', 'Import échoué : ' . $result['error']);
        }

        $added = 0;
        foreach ($result['numbers'] as $n) {
            $existed = WhatsAppNumber::where('phone_id', $n['phone_id'])->exists();
            WhatsAppNumber::updateOrCreate(
                ['phone_id' => $n['phone_id']],
                [
                    'display_number' => $n['display_phone_number'] ?: '—',
                    'verified_name'  => $n['verified_name'],
                    'quality_rating' => $n['quality_rating'],
                    'is_active'      => true,
                ]
            );
            if (! $existed) { $added++; }
        }

        // Si on n'a aucun défaut et qu'au moins un numéro existe, marque le premier.
        if (! WhatsAppNumber::where('is_default', true)->exists() && WhatsAppNumber::count() > 0) {
            WhatsAppNumber::query()->orderBy('id')->first()?->update(['is_default' => true]);
        }

        return back()->with('success', 'Import terminé — ' . count($result['numbers']) . ' numéro(s) ('. $added .' nouveau(x)).');
    }

    /** POST /admin/integrations/numbers/{number}/default */
    public function setDefaultNumber(WhatsAppNumber $number)
    {
        WhatsAppNumber::query()->update(['is_default' => false]);
        $number->update(['is_default' => true]);

        // Aligne la clé settings pour que le reste du code pointe bien dessus.
        Setting::set('whatsapp.meta_phone_number_id', $number->phone_id);

        return back()->with('success', 'Numéro par défaut mis à jour.');
    }

    /** DELETE /admin/integrations/numbers/{number} */
    public function destroyNumber(WhatsAppNumber $number)
    {
        $number->delete();
        return back()->with('success', 'Numéro supprimé.');
    }
}
