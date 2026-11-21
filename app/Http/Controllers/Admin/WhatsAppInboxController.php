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
        ]);
    }

    /** GET /admin/whatsapp/{conversation} — list + thread. */
    public function show(Request $request, WhatsAppConversation $conversation)
    {
        $conversation->forceFill(['unread_count' => 0])->save();

        $query = WhatsAppConversation::query()
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
            'active'        => $conversation,
            'messages'      => $conversation->messages()->get(),
            'q'             => $request->query('q', ''),
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
            return redirect()->route('admin.whatsapp.show', $conversation)
                ->with('error', 'Envoi échoué : ' . ($result['error'] ?? 'erreur inconnue')
                    . ' — rappel : Meta n\'autorise un message libre que si le client vous a écrit dans les 24 dernières heures.');
        }

        return redirect()->route('admin.whatsapp.show', $conversation)
            ->with('success', 'Message envoyé — la conversation est ouverte.');
    }

    /** POST /admin/whatsapp/{conversation}/reply — send a text reply. */
    public function reply(Request $request, WhatsAppConversation $conversation, WhatsAppService $whatsapp)
    {
        $data = $request->validate([
            'body' => 'required|string|max:4096',
        ]);

        if (! $conversation->canSendFreeform()) {
            return back()->with('error',
                'Fenêtre de 24h expirée — vous devez utiliser un template approuvé pour re-contacter ce client.');
        }

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
