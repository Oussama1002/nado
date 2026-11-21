<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsApp gateway (UltraMsg / CallmeBot).
 *
 * The project uses UltraMsg as an unofficial WhatsApp Web gateway:
 * it accepts a phone number OR a group id (xxxxxxxxxxxx@g.us) in the
 * `to` field, so the same `/messages/chat` endpoint serves both.
 *
 * Feature flag: `whatsapp.enabled` (soft kill-switch, defaults to true to
 * keep the pre-existing OTP flow working if the setting is missing).
 */
class WhatsAppService
{
    /**
     * Normalize a phone number to UltraMsg's expected form (+212XXXXXXXXX).
     * Returns the input unchanged if it looks like a group id (ends with
     * "@g.us"), so callers can pass groups through sendText() too.
     */
    public function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        if ($phone === '') {
            return '';
        }
        if (str_ends_with($phone, '@g.us') || str_ends_with($phone, '@c.us')) {
            return $phone;
        }

        $digits = preg_replace('/\D/', '', $phone);

        // Moroccan local 10-digit (0 6/7 ...)
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = '212' . substr($digits, 1);
        }

        return '+' . ltrim($digits, '+');
    }

    /**
     * Send an OTP message. Kept as a thin wrapper around sendText so the
     * historical call sites don't change.
     */
    public function sendOtp(string $phone, string $otp): bool
    {
        $message = "🔐 Votre code de vérification NADO est : *{$otp}*\n\nCe code expire dans 10 minutes. Ne le partagez jamais.";
        return $this->sendText($phone, $message);
    }

    /**
     * Send any text message to a phone number or group id.
     * Returns true on success, false on any failure (logged, never throws).
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

        $provider = config('services.whatsapp.provider', 'ultramsg');
        try {
            return match ($provider) {
                'ultramsg'  => $this->sendUltraMsg($to, $message),
                'callmebot' => $this->sendCallmeBot($to, $message),
                default     => $this->sendUltraMsg($to, $message),
            };
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed', ['to' => $to, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send a message to a WhatsApp group by group id (xxxxxxxxxxxx@g.us).
     * CallmeBot cannot post to groups; this path requires UltraMsg.
     */
    public function sendToGroup(string $groupId, string $message): bool
    {
        if (! str_ends_with($groupId, '@g.us')) {
            Log::warning('WhatsApp group id must end with @g.us', ['group' => $groupId]);
            return false;
        }
        return $this->sendText($groupId, $message);
    }

    /**
     * Broadcast to the admin destinations configured in services.whatsapp:
     *   - admin_group (optional, xxxxxxxxxxxx@g.us)
     *   - admin_numbers (comma-separated list)
     *
     * Returns the number of destinations that accepted the message.
     * Silent no-op when the integration is disabled or no destination
     * is configured.
     */
    public function broadcastToAdmins(string $message): int
    {
        if (! $this->featureOn()) {
            return 0;
        }

        $sent = 0;
        $group = (string) config('services.whatsapp.admin_group', '');
        if ($group !== '') {
            $sent += $this->sendToGroup($group, $message) ? 1 : 0;
        }

        $numbers = (string) config('services.whatsapp.admin_numbers', '');
        foreach (preg_split('/[,\s;]+/', $numbers, -1, PREG_SPLIT_NO_EMPTY) as $num) {
            $sent += $this->sendText($num, $message) ? 1 : 0;
        }

        return $sent;
    }

    private function featureOn(): bool
    {
        // feature_on() reads the Setting store; default true keeps the
        // legacy OTP flow working when the flag hasn't been seeded yet.
        if (! function_exists('feature_on')) {
            return true;
        }
        try {
            return feature_on('whatsapp.enabled');
        } catch (\Throwable $e) {
            return true;
        }
    }

    private function sendUltraMsg(string $to, string $message): bool
    {
        $instance = config('services.whatsapp.ultramsg_instance');
        $token    = config('services.whatsapp.ultramsg_token');

        if (!$instance || !$token) {
            Log::warning('UltraMsg credentials not configured');
            return false;
        }

        $response = Http::asForm()->post(
            "https://api.ultramsg.com/{$instance}/messages/chat",
            ['token' => $token, 'to' => $to, 'body' => $message]
        );

        if (! $response->successful()) {
            Log::warning('UltraMsg send non-2xx', ['to' => $to, 'status' => $response->status(), 'body' => $response->body()]);
            return false;
        }
        return true;
    }

    private function sendCallmeBot(string $phone, string $message): bool
    {
        $apiKey = config('services.whatsapp.callmebot_apikey');

        if (!$apiKey) {
            Log::warning('CallmeBot API key not configured');
            return false;
        }

        $response = Http::get('https://api.callmebot.com/whatsapp.php', [
            'phone'  => $phone,
            'text'   => $message,
            'apikey' => $apiKey,
        ]);

        return $response->successful();
    }
}
