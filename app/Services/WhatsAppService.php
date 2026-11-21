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
        $provider   = settings('whatsapp.provider') ?: config('services.whatsapp.provider', 'meta');

        try {
            return match ($provider) {
                'meta'      => $this->sendMeta($normalized, $otp),
                'ultramsg'  => $this->sendUltraMsg($normalized, $this->freeformMessage($otp)),
                'callmebot' => $this->sendCallmeBot($normalized, $this->freeformMessage($otp)),
                default     => $this->sendMeta($normalized, $otp),
            };
        } catch (\Throwable $e) {
            Log::error('WhatsApp OTP send failed', ['phone' => $normalized, 'error' => $e->getMessage()]);
            return false;
        }
    }

    private function freeformMessage(string $otp): string
    {
        return "🔐 Votre code de vérification NADO est : *{$otp}*\n\nCe code expire dans 10 minutes. Ne le partagez jamais.";
    }

    /**
     * Meta (Facebook) WhatsApp Business Cloud API — graph.facebook.com.
     * Sends via an APPROVED template in the "Authentication" category.
     * The template must have one body variable {{1}} that receives the OTP.
     */
    private function sendMeta(string $phone, string $otp): bool
    {
        $phoneNumberId = settings('whatsapp.meta_phone_number_id') ?: config('services.whatsapp.meta_phone_number_id');
        $accessToken   = settings('whatsapp.meta_access_token')    ?: config('services.whatsapp.meta_access_token');
        $templateName  = settings('whatsapp.meta_template_name')   ?: config('services.whatsapp.meta_template_name', 'otp_verification');
        $templateLang  = settings('whatsapp.meta_template_lang')   ?: config('services.whatsapp.meta_template_lang', 'fr');
        $apiVersion    = config('services.whatsapp.meta_api_version', 'v21.0');

        if (! $phoneNumberId || ! $accessToken) {
            Log::warning('Meta WhatsApp credentials not configured');
            return false;
        }

        // Meta expects E.164 digits WITHOUT the leading '+'.
        $to = ltrim($phone, '+');

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'       => $templateName,
                'language'   => ['code' => $templateLang],
                'components' => [
                    [
                        'type'       => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $otp],
                        ],
                    ],
                    // Required when the template is Authentication-category with
                    // a one-tap "Copy code" URL button; Meta ignores it otherwise.
                    [
                        'type'       => 'button',
                        'sub_type'   => 'url',
                        'index'      => '0',
                        'parameters' => [
                            ['type' => 'text', 'text' => $otp],
                        ],
                    ],
                ],
            ],
        ];

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->asJson()
            ->post("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages", $payload);

        if (! $response->successful()) {
            Log::warning('Meta WhatsApp send failed', [
                'status' => $response->status(),
                'body'   => $response->json() ?? $response->body(),
            ]);
            return false;
        }

        // Successful send returns {"messages":[{"id":"wamid.xxx"}]}.
        $messages = $response->json('messages');
        return is_array($messages) && ! empty($messages);
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
