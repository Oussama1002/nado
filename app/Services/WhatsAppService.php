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
     * Send a WhatsApp OTP message via the Meta (Facebook) WhatsApp Business
     * Cloud API. Requires an APPROVED template in the "Authentication" category
     * with one body variable {{1}} that receives the OTP.
     */
    public function sendOtp(string $phone, string $otp): bool
    {
        // Kill-switch depuis l'admin.
        if (! feature_on('whatsapp.enabled')) {
            Log::warning('WhatsApp OTP disabled via admin settings');
            return false;
        }

        $normalized = $this->normalizePhone($phone);

        try {
            return $this->sendMeta($normalized, $otp);
        } catch (\Throwable $e) {
            Log::error('WhatsApp OTP send failed', ['phone' => $normalized, 'error' => $e->getMessage()]);
            return false;
        }
    }

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
}
