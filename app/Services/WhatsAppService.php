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

        // Already has country code without +
        if (strlen($digits) === 12 && str_starts_with($digits, '212')) {
            // fine
        }

        return '+' . ltrim($digits, '+');
    }

    /**
     * Send a WhatsApp OTP message.
     */
    public function sendOtp(string $phone, string $otp): bool
    {
        // Kill-switch depuis l'admin.
        if (! feature_on('whatsapp.enabled')) {
            Log::warning('WhatsApp OTP disabled via admin settings');
            return false;
        }

        $normalized = $this->normalizePhone($phone);
        $message    = "🔐 Votre code de vérification NADO est : *{$otp}*\n\nCe code expire dans 10 minutes. Ne le partagez jamais.";

        $provider = settings('whatsapp.provider') ?: config('services.whatsapp.provider', 'ultramsg');

        try {
            return match ($provider) {
                'ultramsg'  => $this->sendUltraMsg($normalized, $message),
                'callmebot' => $this->sendCallmeBot($normalized, $message),
                default     => $this->sendUltraMsg($normalized, $message),
            };
        } catch (\Throwable $e) {
            Log::error('WhatsApp OTP send failed', ['phone' => $normalized, 'error' => $e->getMessage()]);
            return false;
        }
    }

    private function sendUltraMsg(string $phone, string $message): bool
    {
        $instance = settings('whatsapp.ultramsg_instance') ?: config('services.whatsapp.ultramsg_instance');
        $token    = settings('whatsapp.ultramsg_token')    ?: config('services.whatsapp.ultramsg_token');

        if (!$instance || !$token) {
            Log::warning('UltraMsg credentials not configured');
            return false;
        }

        $response = Http::asForm()->post(
            "https://api.ultramsg.com/{$instance}/messages/chat",
            ['token' => $token, 'to' => $phone, 'body' => $message]
        );

        return $response->successful();
    }

    private function sendCallmeBot(string $phone, string $message): bool
    {
        $apiKey = settings('whatsapp.callmebot_apikey') ?: config('services.whatsapp.callmebot_apikey');

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
