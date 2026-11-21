<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     * Low-level POST to Meta Cloud API /messages.
     */
    private function postMessage(array $payload): array
    {
        $phoneNumberId = settings('whatsapp.meta_phone_number_id') ?: config('services.whatsapp.meta_phone_number_id');
        $accessToken   = settings('whatsapp.meta_access_token')    ?: config('services.whatsapp.meta_access_token');
        $apiVersion    = config('services.whatsapp.meta_api_version', 'v21.0');

        if (! $phoneNumberId || ! $accessToken) {
            Log::warning('Meta WhatsApp credentials not configured');
            return ['ok' => false, 'wamid' => null, 'error' => 'credentials missing'];
        }

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->asJson()
            ->post("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages", $payload);

        if (! $response->successful()) {
            $body = $response->json() ?? $response->body();
            Log::warning('Meta WhatsApp API error', ['status' => $response->status(), 'body' => $body]);
            return [
                'ok'    => false,
                'wamid' => null,
                'error' => is_array($body) ? ($body['error']['message'] ?? json_encode($body)) : (string) $body,
            ];
        }

        $messages = $response->json('messages') ?? [];
        $wamid    = $messages[0]['id'] ?? null;

        return ['ok' => (bool) $wamid, 'wamid' => $wamid, 'error' => $wamid ? null : 'no wamid in response'];
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

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->get("https://graph.facebook.com/{$apiVersion}/{$wabaId}/message_templates", [
                'limit' => $limit,
            ]);

        if (! $response->successful()) {
            $body = $response->json() ?? $response->body();
            return [
                'ok'        => false,
                'templates' => [],
                'error'     => is_array($body) ? ($body['error']['message'] ?? json_encode($body)) : (string) $body,
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

        $components = [
            ['type' => 'BODY', 'text' => $data['body']],
        ];
        if (! empty($data['footer'])) {
            $components[] = ['type' => 'FOOTER', 'text' => $data['footer']];
        }

        $payload = [
            'name'       => $data['name'],
            'category'   => $data['category'],
            'language'   => $data['language'],
            'components' => $components,
        ];

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->asJson()
            ->post("https://graph.facebook.com/{$apiVersion}/{$wabaId}/message_templates", $payload);

        if (! $response->successful()) {
            $body = $response->json() ?? $response->body();
            return [
                'ok'     => false,
                'id'     => null,
                'status' => null,
                'error'  => is_array($body) ? ($body['error']['message'] ?? json_encode($body)) : (string) $body,
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

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->delete("https://graph.facebook.com/{$apiVersion}/{$wabaId}/message_templates", [
                'name' => $name,
            ]);

        if (! $response->successful()) {
            $body = $response->json() ?? $response->body();
            return [
                'ok'    => false,
                'error' => is_array($body) ? ($body['error']['message'] ?? json_encode($body)) : (string) $body,
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
