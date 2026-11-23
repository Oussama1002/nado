<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Meta WhatsApp Business Cloud API webhook.
 *
 *  GET  /whatsapp/webhook  → verification handshake (hub.challenge).
 *  POST /whatsapp/webhook  → delivery of messages + statuses from Meta.
 *
 * Route is CSRF-exempt and public (no auth) — Meta hits it directly.
 */
class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $expected = settings('whatsapp.webhook_verify_token')
            ?: config('services.whatsapp.webhook_verify_token');

        $mode  = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $chal  = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $expected && hash_equals((string) $expected, (string) $token)) {
            return response($chal, 200);
        }

        return response('forbidden', 403);
    }

    public function receive(Request $request, WhatsAppService $whatsapp)
    {
        $raw = $request->getContent();

        if (! $whatsapp->verifyWebhookSignature($raw, $request->header('X-Hub-Signature-256'))) {
            Log::warning('WhatsApp webhook: invalid signature');
            return response('invalid signature', 403);
        }

        $payload = json_decode($raw, true);
        if (! is_array($payload)) {
            return response('ok');
        }

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                try {
                    $this->processValue($change['value'] ?? []);
                } catch (\Throwable $e) {
                    // Règle d'or des webhooks Meta : TOUJOURS répondre 200 rapidement.
                    // Un 500 → Meta réessaie 3×, puis désactive silencieusement
                    // l'abonnement et plus aucun message n'arrive.
                    Log::error('WhatsApp webhook: processValue failed', [
                        'error' => $e->getMessage(),
                        'line'  => $e->getLine(),
                        'file'  => $e->getFile(),
                    ]);
                }
            }
        }

        return response('ok');
    }

    private function processValue(array $value): void
    {
        $contacts = $value['contacts'] ?? [];
        $nameByPhone = [];
        foreach ($contacts as $c) {
            if (isset($c['wa_id'])) {
                $nameByPhone[$c['wa_id']] = $c['profile']['name'] ?? null;
            }
        }

        // Inbound messages
        foreach ($value['messages'] ?? [] as $msg) {
            $waId = $msg['from'] ?? null;
            if (! $waId) {
                continue;
            }

            DB::transaction(function () use ($msg, $waId, $nameByPhone) {
                $conversation = WhatsAppConversation::firstOrCreate(
                    ['wa_phone' => '+' . ltrim($waId, '+')],
                    ['display_name' => $nameByPhone[$waId] ?? null]
                );

                if (! $conversation->display_name && isset($nameByPhone[$waId])) {
                    $conversation->display_name = $nameByPhone[$waId];
                }

                // Dedupe: Meta may retry the same webhook.
                if (! empty($msg['id']) && WhatsAppMessage::where('wamid', $msg['id'])->exists()) {
                    return;
                }

                $type = $msg['type'] ?? 'text';
                $body = match ($type) {
                    'text'     => $msg['text']['body']       ?? null,
                    'image'    => $msg['image']['caption']   ?? '[image]',
                    'video'    => $msg['video']['caption']   ?? '[vidéo]',
                    'document' => $msg['document']['caption'] ?? ($msg['document']['filename'] ?? '[document]'),
                    'audio'    => '[audio]',
                    'sticker'  => '[sticker]',
                    'location' => '[localisation]',
                    'contacts' => '[contact]',
                    'button'   => $msg['button']['text']     ?? '[bouton]',
                    default    => '[' . $type . ']',
                };
                $mediaId = $msg[$type]['id'] ?? null;

                $sentAt = isset($msg['timestamp'])
                    ? \Carbon\Carbon::createFromTimestamp((int) $msg['timestamp'])
                    : now();

                $created = WhatsAppMessage::create([
                    'conversation_id' => $conversation->id,
                    'direction'       => 'in',
                    'wamid'           => $msg['id'] ?? null,
                    'type'            => $type,
                    'body'            => $body,
                    'media_id'        => $mediaId,
                    'status'          => 'received',
                    'sent_at'         => $sentAt,
                ]);

                $conversation->forceFill([
                    'last_message_at' => $sentAt,
                    'last_inbound_at' => $sentAt,
                    'unread_count'    => $conversation->unread_count + 1,
                ])->save();

                // Téléchargement du média inbound (URL signée Meta, expire en ~5 min).
                // Erreur isolée : ne doit JAMAIS faire rollback le message lui-même.
                if ($mediaId && in_array($type, ['image', 'video', 'audio', 'document', 'sticker'], true)
                    && \Illuminate\Support\Facades\Schema::hasColumn('whatsapp_messages', 'media_path')) {
                    try {
                        $service = app(\App\Services\WhatsAppService::class);
                        if ($path = $service->downloadInboundMedia($mediaId)) {
                            $mime = $msg[$type]['mime_type'] ?? null;
                            $created->update(['media_path' => $path, 'media_mime' => $mime]);
                        }
                    } catch (\Throwable $e) {
                        Log::warning('WhatsApp media download failed', [
                            'media_id' => $mediaId,
                            'error'    => $e->getMessage(),
                        ]);
                    }
                }
            });
        }

        // Outbound status updates (sent/delivered/read/failed)
        foreach ($value['statuses'] ?? [] as $status) {
            if (empty($status['id'])) {
                continue;
            }
            $msg = WhatsAppMessage::where('wamid', $status['id'])->first();
            if (! $msg) {
                continue;
            }
            $msg->status = $status['status'] ?? $msg->status;
            if (! empty($status['errors'])) {
                $msg->error = json_encode($status['errors']);
            }
            $msg->save();
        }
    }
}
