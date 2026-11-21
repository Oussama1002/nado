<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class VerifyWhatsAppController extends Controller
{
    public function show(Request $request)
    {
        // If no pending client in session, redirect to register
        if (!$request->session()->has('pending_client_id')) {
            return redirect()->route('client.register');
        }

        return view('client.auth.verify-whatsapp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ]);

        $clientId = $request->session()->get('pending_client_id');
        if (!$clientId) {
            return redirect()->route('client.register')
                ->withErrors(['Votre session a expiré. Veuillez vous réinscrire.']);
        }

        // Throttle verify attempts: 5 wrong codes per 10 min per client+IP.
        $verifyKey = 'otp-verify:' . $clientId . ':' . $request->ip();
        if (RateLimiter::tooManyAttempts($verifyKey, 5)) {
            $seconds = RateLimiter::availableIn($verifyKey);
            return back()->withErrors([
                'otp' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
            ]);
        }

        $client = Client::find($clientId);
        if (!$client) {
            return redirect()->route('client.register')
                ->withErrors(['Compte introuvable. Veuillez vous réinscrire.']);
        }

        // Check OTP expiry
        if (!$client->otp_expires_at || now()->isAfter($client->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Le code a expiré. Veuillez en demander un nouveau.']);
        }

        // Check OTP match against the stored hash (constant-time via Hash::check).
        if (!$client->otp_code || !Hash::check($request->otp, $client->otp_code)) {
            RateLimiter::hit($verifyKey, 600);
            return back()->withErrors(['otp' => 'Code incorrect. Vérifiez votre WhatsApp.']);
        }

        RateLimiter::clear($verifyKey);

        // Approve and clear OTP
        $client->update([
            'is_approved'    => true,
            'is_verified'    => true,
            'otp_code'       => null,
            'otp_expires_at' => null,
        ]);

        $request->session()->forget('pending_client_id');

        // Log the client in
        Auth::guard('client')->login($client);

        return redirect()->route('index')
            ->with('success', 'Bienvenue sur NADO ! Votre compte est maintenant activé.');
    }

    public function resend(Request $request)
    {
        $clientId = $request->session()->get('pending_client_id');
        if (!$clientId) {
            return redirect()->route('client.register');
        }

        // Throttle resends: 3 per 10 minutes per client+IP to protect the WhatsApp quota.
        $resendKey = 'otp-resend:' . $clientId . ':' . $request->ip();
        if (RateLimiter::tooManyAttempts($resendKey, 3)) {
            $seconds = RateLimiter::availableIn($resendKey);
            return back()->withErrors([
                'otp' => "Trop de demandes. Réessayez dans {$seconds} secondes.",
            ]);
        }
        RateLimiter::hit($resendKey, 600);

        $client = Client::find($clientId);
        if (!$client) {
            return redirect()->route('client.register');
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $client->update([
            'otp_code'       => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $whatsapp = app(WhatsAppService::class);
        $sent     = $whatsapp->sendOtp($client->phone, $otp);

        if (!$sent) {
            return back()->with('otp_warning',
                'Envoi WhatsApp indisponible. Merci de réessayer dans quelques instants ou de contacter le support.');
        }

        return back()->with('otp_resent', 'Nouveau code envoyé sur WhatsApp.');
    }
}
