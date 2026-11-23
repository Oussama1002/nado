<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WhatsAppTemplateController extends Controller
{
    private const CATEGORIES = ['AUTHENTICATION', 'UTILITY', 'MARKETING'];

    /** GET /admin/whatsapp/templates — list Meta templates with status. */
    public function index(WhatsAppService $whatsapp)
    {
        $result = $whatsapp->listTemplates(200);

        return view('admin.whatsapp.templates.index', [
            'ok'        => $result['ok'],
            'templates' => $result['templates'],
            'error'     => $result['error'],
        ]);
    }

    /** GET /admin/whatsapp/templates/create — new template form. */
    public function create()
    {
        return view('admin.whatsapp.templates.create', [
            'categories' => self::CATEGORIES,
        ]);
    }

    /** POST /admin/whatsapp/templates — submit to Meta. */
    public function store(Request $request, WhatsAppService $whatsapp)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:512', 'regex:/^[a-z0-9_]+$/'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'language' => ['required', 'string', 'max:10'],
            // Corps requis sauf AUTHENTICATION (corps généré par Meta).
            'body'     => [$request->input('category') === 'AUTHENTICATION' ? 'nullable' : 'required', 'string', 'max:1024'],
            'footer'   => ['nullable', 'string', 'max:60'],
        ], [
            'name.regex' => 'Le nom doit être en minuscules, chiffres, underscores (ex. order_confirmation).',
        ]);

        $result = $whatsapp->createTemplate($data);

        if (! $result['ok']) {
            return back()->withInput()->with('error', 'Création refusée par Meta : ' . ($result['error'] ?? 'erreur inconnue'));
        }

        return redirect()->route('admin.whatsapp.templates.index')
            ->with('success', 'Template « ' . $data['name'] . ' » soumis à Meta (statut : ' . ($result['status'] ?? 'PENDING') . '). Vérifiez son approbation sous quelques minutes.');
    }

    /** DELETE /admin/whatsapp/templates/{name} — remove on Meta. */
    public function destroy(string $name, WhatsAppService $whatsapp)
    {
        $result = $whatsapp->deleteTemplate($name);

        if (! $result['ok']) {
            return back()->with('error', 'Suppression échouée : ' . ($result['error'] ?? 'erreur inconnue'));
        }

        return back()->with('success', 'Template « ' . $name . ' » supprimé.');
    }
}
