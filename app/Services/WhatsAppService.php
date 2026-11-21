<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsApp gateway — Meta Cloud API (official, graph.facebook.com).
 *
 * Meta's Cloud API only supports 1-to-1 messages: there is no way to
 * post to a WhatsApp group through the official API. Admin broadcasts
 * therefore fan out to a configured list of admin phone numbers.
 *
 * Business-initiated messages (everything we send here — OTPs, email
 * mirrors, demande notifications) require either:
 *   - a pre-approved message template, or
 *   - an open 24h customer-service window (the recipient messaged the
 *     business number in the last 24h).
 *
 * The service picks the path per call:
 *   - sendOtp / broadcastToAdmins use a configured template if one is
 *     set, otherwise fall back to plain text (which will only succeed
 *     inside the 24h window — logged and swallowed on failure).
 *
 * Feature flag: `whatsapp.enabled` (defaults to true for the OTP flow
 * when the setting is missing).
 */
class WhatsAppService
{
    /**
     * Normalize a phone number to Meta's expected form: international
     * digits only, NO leading "+". Moroccan local 0X → 212X.
     */
    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if ($digits === '') {
            return '';
        }
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = '212' . substr($digits, 1);
        }
        return $digits;
    }

    /**
     * Send an OTP. If `whatsapp.otp_template` is configured, uses a
     * Meta-approved authentication template with the OTP as the single
     * body parameter; otherwise falls back to a plain text message
     * (which only succeeds inside an open 24h session).
     */
    public function sendOtp(string $phone, string $otp): bool
    {
        $template = $this->conf('otp_template', '');
        $lang     = $this->conf('otp_template_lang', 'fr');

        if ($template !== '') {
            return $this->sendTemplate($phone, $template, $lang, [$otp]);
        }

        $message = "🔐 Votre code de vérification NADO est : *{$otp}*\n\nCe code expire dans 10 minutes. Ne le partagez jamais.";
        return $this->sendText($phone, $message);
    }

    /**
     * Send a plain text message. Only succeeds within an open 24h
     * customer-service window. Returns true on 2xx, false otherwise
     * (always logged, never throws).
     */
    public function sendText(string $to, string $message): bool
    {
        if (! $this->featureOn()) {
            return false;
        }
        $to = $this->normalizePhone($to);
        if ($to === '') {
            return false;
        }

        return $this->post([
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'text',
            'text'              => [
                'preview_url' => false,
                'body'        => $message,
            ],
        ]);
    }

    /**
     * Send a pre-approved template. $params are the body-parameter
     * values ($params[0] maps to {{1}}, etc.). Meta forbids newlines
     * and tabs inside body parameters — they're stripped here.
     */
    public function sendTemplate(string $to, string $template, string $lang = 'fr', array $params = []): bool
    {
        if (! $this->featureOn()) {
            return false;
        }
        $to = $this->normalizePhone($to);
        if ($to === '' || $template === '') {
            return false;
        }

        $parameters = [];
        foreach ($params as $p) {
            $v = str_replace(["\r", "\n", "\t"], [' ', ' ', ' '], (string) $p);
            $v = preg_replace('/ {4,}/', '   ', $v);
            $v = trim($v);
            $parameters[] = ['type' => 'text', 'text' => $v];
        }

        $components = [];
        if ($parameters !== []) {
            $components[] = ['type' => 'body', 'parameters' => $parameters];
        }

        return $this->post([
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => array_filter([
                'name'       => $template,
                'language'   => ['code' => $lang],
                'components' => $components ?: null,
            ]),
        ]);
    }

    /**
     * Fan out to every admin number configured in services.whatsapp
     * .admin_numbers (comma-separated). Uses `admin_template` if set
     * (recommended — works outside the 24h window), otherwise plain
     * text. Returns the count of successful deliveries.
     */
    public function broadcastToAdmins(string $message): int
    {
        if (! $this->featureOn()) {
            return 0;
        }

        $numbers = $this->conf('admin_numbers', '');
        $list    = preg_split('/[,\s;]+/', $numbers, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if ($list === []) {
            return 0;
        }

        $template = $this->conf('admin_template', '');
        $lang     = $this->conf('admin_template_lang', 'fr');

        $sent = 0;
        foreach ($list as $num) {
            $ok = $template !== ''
                ? $this->sendTemplate($num, $template, $lang, [$message])
                : $this->sendText($num, $message);
            $sent += $ok ? 1 : 0;
        }
        return $sent;
    }

    /**
     * Read a whatsapp config value. DB settings first (admin UI is the
     * primary source), then env via config() so values set before the
     * seeder ran, or kept in env on purpose, still work.
     */
    private function conf(string $key, $default = null)
    {
        if (function_exists('settings')) {
            try {
                $v = settings("whatsapp.{$key}");
                if ($v !== null && $v !== '') {
                    return $v;
                }
            } catch (\Throwable $e) {
                // fall through to config()
            }
        }
        $v = config("services.whatsapp.{$key}");
        return ($v === null || $v === '') ? $default : $v;
    }

    private function featureOn(): bool
    {
        if (! function_exists('feature_on')) {
            return true;
        }
        try {
            return feature_on('whatsapp.enabled');
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Low-level POST to the Cloud API. Returns true on 2xx. Errors are
     * logged with Meta's response body so misconfig is easy to spot in
     * storage/logs/laravel.log.
     */
    private function post(array $payload): bool
    {
        $phoneId = $this->conf('phone_number_id');
        $token   = $this->conf('access_token');
        $version = $this->conf('api_version', 'v21.0');

        if (! $phoneId || ! $token) {
            Log::warning('WhatsApp Cloud API credentials not configured');
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withToken($token)
                ->acceptJson()
                ->post("https://graph.facebook.com/{$version}/{$phoneId}/messages", $payload);

            if (! $response->successful()) {
                Log::warning('WhatsApp Cloud API non-2xx', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'to'     => $payload['to'] ?? null,
                ]);
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp Cloud API send failed', [
                'error' => $e->getMessage(),
                'to'    => $payload['to'] ?? null,
            ]);
            return false;
        }
    }
}
