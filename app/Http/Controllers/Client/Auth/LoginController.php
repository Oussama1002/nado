<?php
namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\ClientVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    public function showLoginForm(Request $request) {
        if ($request->has('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        }
        return view('client.auth.login');
    }
    protected function authenticated(Request $request, $user)
{
    if (!$user->is_verified) {
        Auth::guard('client')->logout();
        return redirect()->route('client.login')->withErrors(['Veuillez confirmer votre adresse e-mail avant de vous connecter.']);
    }
    if (!$user->is_approved) {
        Auth::guard('client')->logout();
        return redirect()->route('client.login')->withErrors(['Votre compte n\'est pas encore activé par l\'administrateur.']);
    }
}


public function login(Request $request) {
    $identifier = $request->input('email');
    $field      = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    $credentials = [
        $field     => $identifier,
        'password' => $request->input('password'),
    ];

    if (Auth::guard('client')->attempt($credentials)) {
        $client = Auth::guard('client')->user();

        if (!$client->is_verified) {
            Auth::guard('client')->logout();

            // Re-send the verification link so the user is never stuck.
            try {
                $verifyUrl = URL::temporarySignedRoute(
                    'client.verify.email',
                    now()->addMinutes(60),
                    ['id' => $client->id, 'hash' => sha1($client->email)]
                );
                $client->notify(new ClientVerifyEmail($verifyUrl));
            } catch (\Throwable $e) {
                Log::error('Resend on login failed: ' . $e->getMessage());
            }

            return redirect()->route('client.login')->withErrors([
                'Votre compte n\'est pas encore confirmé. Un nouveau lien de confirmation vient de vous être envoyé par e-mail.'
            ]);
        }

        if (!$client->is_approved) {
            Auth::guard('client')->logout();
            return redirect()->route('client.login')->withErrors(['Votre compte n\'est pas encore activé par l\'administrateur.']);
        }

        // Honour the form's own destination (the auto-enchere login posts a hidden
        // "redirect" field). The /nadoautoenchere/login route invalidates the session,
        // so url.intended is gone by then and this field is the only thing left
        // carrying the target. Only same-host URLs are accepted, so the field cannot
        // be used to bounce a logged-in client to an external site.
        // On privilégie la destination demandée (champ "redirect" ou url.intended
        // posée par la page de login) AVANT le renvoi par défaut des comptes enchère,
        // sinon un clic « Discussion avec le vendeur » renvoie vers la prelogin enchère
        // au lieu de la page de l'annonce.
        $target = $request->input('redirect') ?: $request->session()->get('url.intended');
        if ($target && $this->isSafeRedirect($target)) {
            $request->session()->forget('url.intended');
            return redirect()->to($target);
        }

        // "Flotte & enchère" clients land directly on the auto-enchère categories page.
        if ($client->type === 'flotte&enchere') {
            return redirect()->route('nadoautoenchere.categories');
        }

        return redirect()->intended(route('client.dashboard'));
    }

    return back()->withErrors([
        'email' => 'Identifiants invalides.',
    ]);
}

/**
 * Accept only relative paths or absolute URLs on this application's host.
 */
private function isSafeRedirect(string $target): bool
{
    if (str_starts_with($target, '/') && !str_starts_with($target, '//')) {
        return true;
    }

    $host = parse_url($target, PHP_URL_HOST);

    return $host !== null && $host === parse_url(config('app.url'), PHP_URL_HOST);
}

    protected function guard()
{
    return Auth::guard('client');
}

    
}

