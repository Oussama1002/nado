<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsAppService
{
    /**
     * Normalize a Moroccan phone number to E.164 format (+212XXXXXXXXX).
     */
    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        // Local 10-digit: 06XXXXXXXX or 07XXXXXXXX
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = '212' . substr($digits, 1);
        }

        return '+' . ltrim($digits, '+');
    }

    /**
     * Send a WhatsApp OTP via an approved Authentication-category template.
     * Returns true if Meta accepted the send.
     */
    public function sendOtp(string $phone, string $otp): bool
    {
        if (! feature_on('whatsapp.enabled')) {
            Log::warning('WhatsApp OTP disabled via admin settings');
            return false;
        }

        $normalized = $this->normalizePhone($phone);
        $templateName = settings('whatsapp.meta_template_name') ?: config('services.whatsapp.meta_template_name', 'otp_verification');
        $templateLang = settings('whatsapp.meta_template_lang') ?: config('services.whatsapp.meta_template_lang', 'fr');

        try {
            $result = $this->postMessage([
                'messaging_product' => 'whatsapp',
                'to'                => ltrim($normalized, '+'),
                'type'              => 'template',
                'template'          => [
                    'name'       => $templateName,
                    'language'   => ['code' => $templateLang],
                    'components' => [
                        [
                            'type'       => 'body',
                            'parameters' => [['type' => 'text', 'text' => $otp]],
                        ],
                        [
                            'type'       => 'button',
                            'sub_type'   => 'url',
                            'index'      => '0',
                            'parameters' => [['type' => 'text', 'text' => $otp]],
                        ],
                    ],
                ],
            ]);
            return $result['ok'];
        } catch (\Throwable $e) {
            Log::error('WhatsApp OTP send failed', ['phone' => $normalized, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send an approved template message. Use this to initiate a conversation
     * with a new recipient (freeform is blocked by Meta outside the 24h window).
     *
     * $variables = ordered list of {{1}}, {{2}}, … body parameters.
     * Returns ['ok' => bool, 'wamid' => ?string, 'error' => ?string].
     */
    public function sendTemplate(string $phone, string $templateName, string $languageCode, array $variables = []): array
    {
        if (! feature_on('whatsapp.enabled')) {
            return ['ok' => false, 'wamid' => null, 'error' => 'WhatsApp disabled'];
        }

        $to = ltrim($this->normalizePhone($phone), '+');

        $components = [];
        if (! empty($variables)) {
            $components[] = [
                'type'       => 'body',
                'parameters' => array_map(fn ($v) => ['type' => 'text', 'text' => (string) $v], $variables),
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'     => $templateName,
                'language' => ['code' => $languageCode],
            ],
        ];
        if ($components) {
            $payload['template']['components'] = $components;
        }

        try {
            return $this->postMessage($payload);
        } catch (\Throwable $e) {
            Log::error('WhatsApp template send failed', ['to' => $to, 'template' => $templateName, 'error' => $e->getMessage()]);
            return ['ok' => false, 'wamid' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a freeform text message. Only works inside Meta's 24h window after
     * the recipient's last inbound message. Returns ['ok' => bool, 'wamid' => ?string, 'error' => ?string].
     */
    public function sendText(string $phone, string $body): array
    {
        if (! feature_on('whatsapp.enabled')) {
            return ['ok' => false, 'wamid' => null, 'error' => 'WhatsApp disabled'];
        }

        $to = ltrim($this->normalizePhone($phone), '+');

        try {
            return $this->postMessage([
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $to,
                'type'              => 'text',
                'text'              => ['preview_url' => false, 'body' => $body],
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp text send failed', ['to' => $to, 'error' => $e->getMessage()]);
            return ['ok' => false, 'wamid' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Upload a local file to Meta's /media endpoint and return a media_id
     * usable in sendMedia(). Returns ['ok'=>bool,'media_id'=>?string,'error'=>?string].
     */
    public function uploadMedia(string $absolutePath, string $mime): array
    {
        if (! feature_on('whatsapp.enabled')) {
            return ['ok' => false, 'media_id' => null, 'error' => 'WhatsApp disabled'];
        }
        $phoneNumberId = settings('whatsapp.meta_phone_number_id') ?: config('services.whatsapp.meta_phone_number_id');
        $accessToken   = settings('whatsapp.meta_access_token')    ?: config('services.whatsapp.meta_access_token');
        $apiVersion    = config('services.whatsapp.meta_api_version', 'v21.0');

        if (! $phoneNumberId || ! $accessToken) {
            return ['ok' => false, 'media_id' => null, 'error' => 'Identifiants Meta manquants.'];
        }

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->connectTimeout(5)->timeout(60)
                ->attach('file', file_get_contents($absolutePath), basename($absolutePath), ['Content-Type' => $mime])
                ->post("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/media", [
                    'messaging_product' => 'whatsapp',
                    'type'              => $mime,
                ]);
        } catch (ConnectionException $e) {
            return ['ok' => false, 'media_id' => null, 'error' => "Meta a mis trop de temps à répondre."];
        }

        if (! $response->successful()) {
            Log::warning('Meta WhatsApp media upload error', ['status' => $response->status(), 'body' => $response->json()]);
            return [
                'ok'       => false,
                'media_id' => null,
                'error'    => MetaErrorTranslator::fromResponseBody($response->json())
                    ?? MetaErrorTranslator::toFrench($response->body()),
            ];
        }

        $id = $response->json('id');
        return ['ok' => (bool) $id, 'media_id' => $id, 'error' => $id ? null : 'Réponse Meta sans media_id.'];
    }

    /**
     * Send a media message using an uploaded media_id. $type is one of
     * image | audio | video | document. Optional caption (image/video/document only).
     * Set $voice=true for audio to flag as a voice note.
     */
    public function sendMedia(string $phone, string $mediaId, string $type, ?string $caption = null, bool $voice = false, ?string $filename = null): array
    {
        if (! feature_on('whatsapp.enabled')) {
            return ['ok' => false, 'wamid' => null, 'error' => 'WhatsApp disabled'];
        }

        $to = ltrim($this->normalizePhone($phone), '+');
        $block = ['id' => $mediaId];
        if ($caption !== null && $caption !== '' && in_array($type, ['image', 'video', 'document'], true)) {
            $block['caption'] = $caption;
        }
        if ($type === 'audio' && $voice) {
            $block['voice'] = true;
        }
        if ($type === 'document' && $filename) {
            $block['filename'] = $filename;
        }

        try {
            return $this->postMessage([
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $to,
                'type'              => $type,
                $type               => $block,
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp media send failed', ['to' => $to, 'type' => $type, 'error' => $e->getMessage()]);
            return ['ok' => false, 'wamid' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Low-level POST to Meta Cloud API /messages. Erreur traduite en français.
     */
    private function postMessage(array $payload): array
    {
        $phoneNumberId = settings('whatsapp.meta_phone_number_id') ?: config('services.whatsapp.meta_phone_number_id');
        $accessToken   = settings('whatsapp.meta_access_token')    ?: config('services.whatsapp.meta_access_token');
        $apiVersion    = config('services.whatsapp.meta_api_version', 'v21.0');

        if (! $phoneNumberId || ! $accessToken) {
            Log::warning('Meta WhatsApp credentials not configured');
            return ['ok' => false, 'wamid' => null, 'error' => 'Identifiants Meta manquants (Phone Number ID / Access Token).'];
        }

        try {
            // Timeout court < reverse-proxy pour qu'un ralentissement Meta renvoie
            // notre JSON et non une page d'erreur HTML du front-end.
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->asJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->post("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages", $payload);
        } catch (ConnectionException $e) {
            Log::warning('Meta WhatsApp timeout', ['error' => $e->getMessage()]);
            return ['ok' => false, 'wamid' => null, 'error' => "Meta a mis trop de temps à répondre. Réessayez dans un instant."];
        }

        if (! $response->successful()) {
            $body = $response->json() ?? $response->body();
            Log::warning('Meta WhatsApp API error', ['status' => $response->status(), 'body' => $body]);
            return [
                'ok'    => false,
                'wamid' => null,
                'error' => MetaErrorTranslator::fromResponseBody($body)
                    ?? MetaErrorTranslator::toFrench(is_string($body) ? $body : null),
            ];
        }

        $messages = $response->json('messages') ?? [];
        $wamid    = $messages[0]['id'] ?? null;

        return ['ok' => (bool) $wamid, 'wamid' => $wamid, 'error' => $wamid ? null : "Réponse Meta sans wamid."];
    }

    /**
     * Télécharge un média inbound référencé par son media_id : récupère l'URL
     * signée via GET /{media_id}, puis télécharge le binaire avec le bearer.
     * Enregistre sur le disque public (storage/app/public/whatsapp) et renvoie
     * le chemin relatif stocké en DB.
     */
    public function downloadInboundMedia(string $mediaId): ?string
    {
        [, $accessToken, $apiVersion] = $this->managementCreds();
        if (! $accessToken) {
            return null;
        }

        try {
            $meta = Http::withToken($accessToken)
                ->acceptJson()
                ->connectTimeout(5)->timeout(15)
                ->get("https://graph.facebook.com/{$apiVersion}/{$mediaId}");
        } catch (ConnectionException $e) {
            Log::warning('WhatsApp media metadata fetch timeout', ['media_id' => $mediaId]);
            return null;
        }

        if (! $meta->successful()) {
            Log::warning('WhatsApp media metadata fetch failed', ['status' => $meta->status(), 'body' => $meta->json()]);
            return null;
        }

        $url  = $meta->json('url');
        $mime = $meta->json('mime_type');
        if (! $url) {
            return null;
        }

        try {
            $bin = Http::withToken($accessToken)
                ->connectTimeout(5)->timeout(60)
                ->get($url);
        } catch (ConnectionException $e) {
            Log::warning('WhatsApp media binary timeout', ['media_id' => $mediaId]);
            return null;
        }

        if (! $bin->successful()) {
            return null;
        }

        $ext  = self::extensionForMime($mime);
        $name = $mediaId . '.' . $ext;
        $path = 'whatsapp/' . $name;
        Storage::disk('public')->put($path, $bin->body());

        return $path;
    }

    private static function extensionForMime(?string $mime): string
    {
        return match (true) {
            $mime === null                                  => 'bin',
            str_starts_with($mime, 'image/jpeg')            => 'jpg',
            str_starts_with($mime, 'image/png')             => 'png',
            str_starts_with($mime, 'image/webp')            => 'webp',
            str_starts_with($mime, 'image/')                => 'img',
            str_starts_with($mime, 'audio/ogg')             => 'ogg',
            str_starts_with($mime, 'audio/mpeg')            => 'mp3',
            str_starts_with($mime, 'audio/')                => 'audio',
            str_starts_with($mime, 'video/mp4')             => 'mp4',
            str_starts_with($mime, 'video/')                => 'vid',
            str_contains($mime, 'pdf')                      => 'pdf',
            default                                         => 'bin',
        };
    }

    /* ──────────────────────── Diagnostics / listings ──────────────────────── */

    /**
     * Ping WhatsApp Cloud API with current creds. Returns a simple result object.
     * Succès : {ok:true, phone: "...", verified_name: "..."}
     * Échec  : {ok:false, error: "message FR"}
     */
    public function testConnection(): array
    {
        $phoneNumberId = settings('whatsapp.meta_phone_number_id') ?: config('services.whatsapp.meta_phone_number_id');
        $accessToken   = settings('whatsapp.meta_access_token')    ?: config('services.whatsapp.meta_access_token');
        $apiVersion    = config('services.whatsapp.meta_api_version', 'v21.0');

        if (! $phoneNumberId || ! $accessToken) {
            return ['ok' => false, 'error' => 'Phone Number ID ou Access Token manquant.'];
        }

        try {
            $resp = Http::withToken($accessToken)
                ->acceptJson()
                ->connectTimeout(5)->timeout(10)
                ->get("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}", [
                    'fields' => 'display_phone_number,verified_name,quality_rating,code_verification_status',
                ]);
        } catch (ConnectionException $e) {
            return ['ok' => false, 'error' => "Meta a mis trop de temps à répondre."];
        }

        if (! $resp->successful()) {
            return ['ok' => false, 'error' => MetaErrorTranslator::fromResponseBody($resp->json()) ?? "Erreur Meta inconnue."];
        }

        return [
            'ok'            => true,
            'phone'         => $resp->json('display_phone_number'),
            'verified_name' => $resp->json('verified_name'),
            'quality'       => $resp->json('quality_rating'),
        ];
    }

    /**
     * Liste les numéros rattachés au WABA (pour l'import en admin).
     * Returns {ok, numbers: [{phone_id, display_phone_number, verified_name, quality_rating}], error}.
     */
    public function listWabaPhoneNumbers(): array
    {
        [$wabaId, $accessToken, $apiVersion] = $this->managementCreds();
        if (! $wabaId || ! $accessToken) {
            return ['ok' => false, 'numbers' => [], 'error' => "WABA ID ou Access Token manquant."];
        }

        try {
            $resp = Http::withToken($accessToken)
                ->acceptJson()
                ->connectTimeout(5)->timeout(15)
                ->get("https://graph.facebook.com/{$apiVersion}/{$wabaId}/phone_numbers");
        } catch (ConnectionException $e) {
            return ['ok' => false, 'numbers' => [], 'error' => "Meta a mis trop de temps à répondre."];
        }

        if (! $resp->successful()) {
            return [
                'ok'      => false,
                'numbers' => [],
                'error'   => MetaErrorTranslator::fromResponseBody($resp->json()) ?? "Erreur Meta inconnue.",
            ];
        }

        $numbers = collect($resp->json('data') ?? [])->map(fn ($n) => [
            'phone_id'             => $n['id'] ?? null,
            'display_phone_number' => $n['display_phone_number'] ?? null,
            'verified_name'        => $n['verified_name'] ?? null,
            'quality_rating'       => $n['quality_rating'] ?? null,
        ])->filter(fn ($n) => $n['phone_id'])->values()->all();

        return ['ok' => true, 'numbers' => $numbers, 'error' => null];
    }

    /* ──────────────────────── Template management ──────────────────────── */

    /**
     * List templates on the WhatsApp Business Account.
     * Returns ['ok' => bool, 'templates' => array, 'error' => ?string].
     */
    public function listTemplates(int $limit = 100): array
    {
        [$wabaId, $accessToken, $apiVersion] = $this->managementCreds();
        if (! $wabaId || ! $accessToken) {
            return ['ok' => false, 'templates' => [], 'error' => 'WABA ID or access token missing'];
        }

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->connectTimeout(5)->timeout(15)
                ->get("https://graph.facebook.com/{$apiVersion}/{$wabaId}/message_templates", [
                    'limit' => $limit,
                ]);
        } catch (ConnectionException $e) {
            return ['ok' => false, 'templates' => [], 'error' => "Meta a mis trop de temps à répondre."];
        }

        if (! $response->successful()) {
            return [
                'ok'        => false,
                'templates' => [],
                'error'     => MetaErrorTranslator::fromResponseBody($response->json())
                    ?? MetaErrorTranslator::toFrench($response->body()),
            ];
        }

        return ['ok' => true, 'templates' => $response->json('data') ?? [], 'error' => null];
    }

    /**
     * Create a template on Meta. Body text is required; footer is optional.
     * $category is AUTHENTICATION | UTILITY | MARKETING.
     * Returns ['ok' => bool, 'id' => ?string, 'status' => ?string, 'error' => ?string].
     */
    public function createTemplate(array $data): array
    {
        [$wabaId, $accessToken, $apiVersion] = $this->managementCreds();
        if (! $wabaId || ! $accessToken) {
            return ['ok' => false, 'id' => null, 'status' => null, 'error' => 'WABA ID or access token missing'];
        }

        $category = strtoupper($data['category']);

        if ($category === 'AUTHENTICATION') {
            // Meta génère un corps fixe pour les templates d'auth ; on ne doit
            // PAS envoyer de BODY.text. On déclare seulement les options
            // (recommandation sécurité + bouton copie-code).
            $components = [
                ['type' => 'BODY',   'add_security_recommendation' => true],
                ['type' => 'FOOTER', 'code_expiration_minutes' => 10],
                ['type' => 'BUTTONS', 'buttons' => [
                    ['type' => 'OTP', 'otp_type' => 'COPY_CODE'],
                ]],
            ];
        } else {
            $components = [
                ['type' => 'BODY', 'text' => $data['body']],
            ];
            if (! empty($data['footer'])) {
                $components[] = ['type' => 'FOOTER', 'text' => $data['footer']];
            }
        }

        $payload = [
            'name'       => $data['name'],
            'category'   => $category,
            'language'   => $data['language'],
            'components' => $components,
        ];

        try {
            // Timeout court pour éviter qu'nginx coupe avant nous → JSON clair
            // au lieu d'une HTML page "Bad Gateway".
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->asJson()
                ->connectTimeout(5)->timeout(10)
                ->post("https://graph.facebook.com/{$apiVersion}/{$wabaId}/message_templates", $payload);
        } catch (ConnectionException $e) {
            return [
                'ok'     => false,
                'id'     => null,
                'status' => null,
                'error'  => "Meta a mis trop de temps à répondre. Meta a peut-être tout de même reçu votre template — rafraîchissez la liste dans un instant.",
            ];
        }

        if (! $response->successful()) {
            return [
                'ok'     => false,
                'id'     => null,
                'status' => null,
                'error'  => MetaErrorTranslator::fromResponseBody($response->json())
                    ?? MetaErrorTranslator::toFrench($response->body()),
            ];
        }

        return [
            'ok'     => true,
            'id'     => $response->json('id'),
            'status' => $response->json('status'),
            'error'  => null,
        ];
    }

    /**
     * Delete a template by name (all language variants, per Meta's API semantics).
     */
    public function deleteTemplate(string $name): array
    {
        [$wabaId, $accessToken, $apiVersion] = $this->managementCreds();
        if (! $wabaId || ! $accessToken) {
            return ['ok' => false, 'error' => 'WABA ID or access token missing'];
        }

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->connectTimeout(5)->timeout(10)
                ->delete("https://graph.facebook.com/{$apiVersion}/{$wabaId}/message_templates", [
                    'name' => $name,
                ]);
        } catch (ConnectionException $e) {
            return ['ok' => false, 'error' => "Meta a mis trop de temps à répondre."];
        }

        if (! $response->successful()) {
            return [
                'ok'    => false,
                'error' => MetaErrorTranslator::fromResponseBody($response->json())
                    ?? MetaErrorTranslator::toFrench($response->body()),
            ];
        }

        return ['ok' => true, 'error' => null];
    }

    private function managementCreds(): array
    {
        return [
            settings('whatsapp.meta_waba_id')      ?: config('services.whatsapp.meta_waba_id'),
            settings('whatsapp.meta_access_token') ?: config('services.whatsapp.meta_access_token'),
            config('services.whatsapp.meta_api_version', 'v21.0'),
        ];
    }

    /**
     * Verify the X-Hub-Signature-256 header on an incoming webhook POST.
     * Returns true if (a) no app secret is configured (skip, in dev), or
     * (b) the HMAC matches.
     */
    public function verifyWebhookSignature(string $rawBody, ?string $signatureHeader): bool
    {
        $appSecret = settings('whatsapp.webhook_app_secret') ?: config('services.whatsapp.webhook_app_secret');

        if (! $appSecret) {
            // No secret configured — accept but log (first-run bootstrap).
            Log::info('WhatsApp webhook: no app secret configured, skipping signature check');
            return true;
        }

        if (! $signatureHeader || ! str_starts_with($signatureHeader, 'sha256=')) {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $rawBody, $appSecret);
        return hash_equals($expected, $signatureHeader);
    }
}
