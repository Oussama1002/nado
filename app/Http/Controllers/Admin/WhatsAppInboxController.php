<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppInboxController extends Controller
{
    /** GET /admin/whatsapp — list of conversations (left panel). */
    public function index(Request $request)
    {
        $query = WhatsAppConversation::query()
            ->with('latestMessage')
            ->orderByRaw('COALESCE(last_message_at, created_at) DESC');

        if ($q = trim((string) $request->query('q', ''))) {
            $query->where(function ($w) use ($q) {
                $w->where('display_name', 'like', "%{$q}%")
                  ->orWhere('wa_phone', 'like', "%{$q}%");
            });
        }

        $conversations = $query->paginate(30)->withQueryString();

        return view('admin.whatsapp.index', [
            'conversations' => $conversations,
            'active'        => null,
            'messages'      => collect(),
            'q'             => $request->query('q', ''),
            'templates'     => [],
        ]);
    }

    /** GET /admin/whatsapp/{conversation} — list + thread. */
    public function show(Request $request, WhatsAppConversation $conversation, WhatsAppService $whatsapp)
    {
        $conversation->forceFill(['unread_count' => 0])->save();

        $query = WhatsAppConversation::query()
            ->with('latestMessage')
            ->orderByRaw('COALESCE(last_message_at, created_at) DESC');
        if ($q = trim((string) $request->query('q', ''))) {
            $query->where(function ($w) use ($q) {
                $w->where('display_name', 'like', "%{$q}%")
                  ->orWhere('wa_phone', 'like', "%{$q}%");
            });
        }
        $conversations = $query->paginate(30)->withQueryString();

        // Templates approuvés — seulement si la fenêtre 24h est expirée
        // (sinon le bouton template ne sert à rien et on évite un appel Meta).
        $templates = [];
        if (! $conversation->canSendFreeform()) {
            $result = $whatsapp->listTemplates(100);
            if ($result['ok']) {
                $templates = collect($result['templates'])
                    ->where('status', 'APPROVED')
                    ->map(function ($t) {
                        $body = collect($t['components'] ?? [])->firstWhere('type', 'BODY')['text'] ?? '';
                        preg_match_all('/\{\{(\d+)\}\}/', $body, $m);
                        return [
                            'name'     => $t['name'],
                            'language' => $t['language'] ?? 'fr',
                            'category' => $t['category'] ?? '—',
                            'body'     => $body,
                            'var_count' => $m[1] ? max($m[1]) : 0,
                        ];
                    })
                    ->values()
                    ->all();
            }
        }

        return view('admin.whatsapp.index', [
            'conversations' => $conversations,
            'active'        => $conversation,
            'messages'      => $conversation->messages()->get(),
            'q'             => $request->query('q', ''),
            'templates'     => $templates,
        ]);
    }

    /**
     * POST /admin/whatsapp/start — open a conversation with a new contact
     * by sending a freeform text message. Meta will reject the send if the
     * recipient has never messaged this number (no 24h customer-service window);
     * the error is surfaced in the flash message.
     */
    public function start(Request $request, WhatsAppService $whatsapp)
    {
        $data = $request->validate([
            'phone'        => 'required|string|max:32',
            'display_name' => 'nullable|string|max:120',
            'body'         => 'required|string|max:4096',
        ]);

        $normalized = $whatsapp->normalizePhone($data['phone']);

        $conversation = WhatsAppConversation::firstOrCreate(
            ['wa_phone' => $normalized],
            ['display_name' => $data['display_name'] ?? null]
        );
        if ($data['display_name'] ?? null) {
            $conversation->display_name = $data['display_name'];
            $conversation->save();
        }

        $result = $whatsapp->sendText($normalized, $data['body']);

        $msg = WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction'       => 'out',
            'wamid'           => $result['wamid'] ?? null,
            'type'            => 'text',
            'body'            => $data['body'],
            'status'          => $result['ok'] ? 'sent' : 'failed',
            'error'           => $result['error'] ?? null,
            'sent_at'         => now(),
        ]);

        $conversation->forceFill(['last_message_at' => $msg->sent_at])->save();

        if (! $result['ok']) {
            // Message déjà traduit en français par WhatsAppService (via MetaErrorTranslator).
            return redirect()->route('admin.whatsapp.show', $conversation)
                ->with('error', 'Envoi échoué : ' . ($result['error'] ?? 'erreur inconnue'));
        }

        return redirect()->route('admin.whatsapp.show', $conversation)
            ->with('success', 'Message envoyé — la conversation est ouverte.');
    }

    /** POST /admin/whatsapp/{conversation}/send-template — ré-ouvre hors 24h. */
    public function sendTemplate(Request $request, WhatsAppConversation $conversation, WhatsAppService $whatsapp)
    {
        $data = $request->validate([
            'template_name' => 'required|string|max:120',
            'language'      => 'required|string|max:10',
            'variables'     => 'nullable|array',
            'variables.*'   => 'nullable|string|max:1000',
            'preview'       => 'nullable|string|max:4096',
        ]);

        $variables = array_values($data['variables'] ?? []);
        $variables = array_map(fn ($v) => (string) ($v ?? ''), $variables);

        $result = $whatsapp->sendTemplate(
            $conversation->wa_phone,
            $data['template_name'],
            $data['language'],
            $variables
        );

        // Corps affiché dans le thread : la prévisualisation rendue si possible,
        // sinon on retombe sur l'étiquette "[template: name]".
        $body = $data['preview']
            ?: '[template: ' . $data['template_name'] . ']' . ($variables ? ' · ' . implode(' | ', $variables) : '');

        $msg = WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction'       => 'out',
            'wamid'           => $result['wamid'] ?? null,
            'type'            => 'template',
            'body'            => $body,
            'status'          => $result['ok'] ? 'sent' : 'failed',
            'error'           => $result['error'] ?? null,
            'sent_at'         => now(),
        ]);

        $conversation->forceFill(['last_message_at' => $msg->sent_at])->save();

        if (! $result['ok']) {
            return back()->with('error', 'Envoi du template échoué : ' . ($result['error'] ?? 'erreur inconnue'));
        }

        return back()->with('success', 'Template envoyé — la conversation va être ré-ouverte dès que le client répond.');
    }

    /**
     * POST /admin/whatsapp/{conversation}/reply — send a text and/or media reply.
     * Accepts optional `media` upload (image/audio/video/document). The `body`
     * doubles as caption for image/video/document. `voice=1` marks audio as
     * a voice note.
     */
    public function reply(Request $request, WhatsAppConversation $conversation, WhatsAppService $whatsapp)
    {
        $data = $request->validate([
            'body'  => 'nullable|string|max:4096',
            'media' => 'nullable|file|max:102400', // 100 MB
            'voice' => 'nullable|boolean',
        ]);

        if (empty($data['body']) && ! $request->hasFile('media')) {
            return back()->with('error', 'Votre message est vide.');
        }

        if (! $conversation->canSendFreeform()) {
            return back()->with('error',
                'Fenêtre de 24h expirée — vous devez utiliser un template approuvé pour re-contacter ce client.');
        }

        // Media branch: upload to Meta, then send as image/audio/video/document.
        if ($request->hasFile('media')) {
            $file     = $request->file('media');
            $mime     = $file->getMimeType() ?: 'application/octet-stream';
            $original = $file->getClientOriginalName();

            // Keep a local copy on the public disk so the thread can render it
            // immediately, without re-downloading from Meta.
            $localPath = $file->store('whatsapp', 'public');
            $absolute  = storage_path('app/public/' . $localPath);

            $type = match (true) {
                str_starts_with($mime, 'image/') => 'image',
                str_starts_with($mime, 'audio/') => 'audio',
                str_starts_with($mime, 'video/') => 'video',
                default                          => 'document',
            };

            $up = $whatsapp->uploadMedia($absolute, $mime);
            if (! $up['ok']) {
                WhatsAppMessage::create([
                    'conversation_id' => $conversation->id,
                    'direction'       => 'out',
                    'type'            => $type,
                    'body'            => (string) ($data['body'] ?? ''),
                    'media_path'      => $localPath,
                    'media_mime'      => $mime,
                    'status'          => 'failed',
                    'error'           => $up['error'] ?? 'upload failed',
                    'sent_at'         => now(),
                ]);
                return back()->with('error', 'Envoi échoué : ' . ($up['error'] ?? 'erreur upload'));
            }

            $voice  = $type === 'audio' && $request->boolean('voice');
            $result = $whatsapp->sendMedia(
                $conversation->wa_phone,
                $up['media_id'],
                $type,
                $data['body'] ?? null,
                $voice,
                $type === 'document' ? $original : null,
            );

            $msg = WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'direction'       => 'out',
                'wamid'           => $result['wamid'] ?? null,
                'type'            => $type,
                'body'            => (string) ($data['body'] ?? ''),
                'media_id'        => $up['media_id'],
                'media_path'      => $localPath,
                'media_mime'      => $mime,
                'status'          => $result['ok'] ? 'sent' : 'failed',
                'error'           => $result['error'] ?? null,
                'sent_at'         => now(),
            ]);

            $conversation->forceFill(['last_message_at' => $msg->sent_at])->save();

            if (! $result['ok']) {
                return back()->with('error', 'Envoi échoué : ' . ($result['error'] ?? 'erreur inconnue'));
            }
            return redirect()->route('admin.whatsapp.show', $conversation)
                ->with('success', 'Message envoyé.');
        }

        // Text-only branch.
        $result = $whatsapp->sendText($conversation->wa_phone, $data['body']);

        $msg = WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction'       => 'out',
            'wamid'           => $result['wamid'] ?? null,
            'type'            => 'text',
            'body'            => $data['body'],
            'status'          => $result['ok'] ? 'sent' : 'failed',
            'error'           => $result['error'] ?? null,
            'sent_at'         => now(),
        ]);

        $conversation->forceFill(['last_message_at' => $msg->sent_at])->save();

        if (! $result['ok']) {
            return back()->with('error', 'Envoi échoué : ' . ($result['error'] ?? 'erreur inconnue'));
        }

        return redirect()->route('admin.whatsapp.show', $conversation)
            ->with('success', 'Message envoyé.');
    }
}
