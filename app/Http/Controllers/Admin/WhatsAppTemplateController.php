<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppTemplateDraft;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WhatsAppTemplateController extends Controller
{
    private const CATEGORIES = ['AUTHENTICATION', 'UTILITY', 'MARKETING'];

    /**
     * GET /admin/whatsapp/templates — fusionne les brouillons locaux
     * (statut DRAFT) et les templates Meta (APPROVED / PENDING / REJECTED).
     */
    public function index(WhatsAppService $whatsapp)
    {
        $result = $whatsapp->listTemplates(200);
        $drafts = WhatsAppTemplateDraft::orderBy('name')->orderBy('language')->get();

        return view('admin.whatsapp.templates.index', [
            'ok'        => $result['ok'],
            'templates' => $result['templates'],
            'error'     => $result['error'],
            'drafts'    => $drafts,
        ]);
    }

    /** GET /admin/whatsapp/templates/create — formulaire nouveau brouillon. */
    public function create()
    {
        return view('admin.whatsapp.templates.create', [
            'categories' => self::CATEGORIES,
        ]);
    }

    /**
     * POST /admin/whatsapp/templates — enregistre le template en BROUILLON
     * local. Il n'est pas envoyé à Meta avant un clic explicite sur
     * "Soumettre à Meta" dans la liste.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120', 'regex:/^[a-z0-9_]+$/'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'language' => ['required', 'string', 'max:10'],
            'body'     => [$request->input('category') === 'AUTHENTICATION' ? 'nullable' : 'required', 'string', 'max:1024'],
            'footer'   => ['nullable', 'string', 'max:60'],
        ], [
            'name.regex' => 'Le nom doit être en minuscules, chiffres, underscores (ex. order_confirmation).',
        ]);

        WhatsAppTemplateDraft::updateOrCreate(
            ['name' => $data['name'], 'language' => $data['language']],
            [
                'category' => $data['category'],
                'body'     => $data['body'] ?? '',
                'footer'   => $data['footer'] ?: null,
            ]
        );

        return redirect()->route('admin.whatsapp.templates.index')
            ->with('success', 'Brouillon « ' . $data['name'] . ' · ' . $data['language'] . ' » enregistré. Cliquez sur « Soumettre à Meta » dans la liste quand vous serez prêt.');
    }

    /**
     * POST /admin/whatsapp/templates/drafts/{draft}/submit — pousse le
     * brouillon vers Meta ; en cas de succès, supprime la ligne locale
     * pour que Meta devienne la source de vérité.
     */
    public function submit(WhatsAppTemplateDraft $draft, WhatsAppService $whatsapp)
    {
        $result = $whatsapp->createTemplate([
            'name'     => $draft->name,
            'category' => $draft->category,
            'language' => $draft->language,
            'body'     => $draft->body,
            'footer'   => $draft->footer,
        ]);

        if (! $result['ok']) {
            return back()->with('error', 'Soumission à Meta refusée : ' . ($result['error'] ?? 'erreur inconnue'));
        }

        $name = $draft->name;
        $lang = $draft->language;
        $draft->delete();

        return back()->with('success', 'Brouillon « ' . $name . ' · ' . $lang . ' » soumis à Meta (statut : ' . ($result['status'] ?? 'PENDING') . ').');
    }

    /** DELETE /admin/whatsapp/templates/drafts/{draft} — supprime un brouillon local. */
    public function destroyDraft(WhatsAppTemplateDraft $draft)
    {
        $label = $draft->name . ' · ' . $draft->language;
        $draft->delete();
        return back()->with('success', 'Brouillon « ' . $label . ' » supprimé.');
    }

    /** DELETE /admin/whatsapp/templates/{name} — supprime côté Meta. */
    public function destroy(string $name, WhatsAppService $whatsapp)
    {
        $result = $whatsapp->deleteTemplate($name);

        if (! $result['ok']) {
            return back()->with('error', 'Suppression échouée : ' . ($result['error'] ?? 'erreur inconnue'));
        }

        return back()->with('success', 'Template « ' . $name . ' » supprimé sur Meta.');
    }
}
