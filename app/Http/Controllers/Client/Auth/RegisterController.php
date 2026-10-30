<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Notifications\ClientVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('client.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:clients,email',
            'password' => 'required|string|min:6|confirmed',
            'type'     => 'required|in:pro,particulier,flotte&enchere',
            'phone'    => 'required|string|max:20',
            'company'  => 'nullable|string|max:255',
            'ae_role'  => 'nullable|in:vendeur,acheteur',
        ]);

        // Création du compte, inactif jusqu'à la confirmation de l'email
        // (comportement calqué sur le flux "reset password" : l'utilisateur
        // reçoit un lien signé et s'auto-approuve en cliquant dessus).
        $client = Client::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'type'        => $request->type,
            // Rôle Auto Enchère (vendeur/acheteur) — pertinent pour flotte&enchere.
            'ae_role'     => $request->type === 'flotte&enchere' ? $request->ae_role : null,
            'phone'       => $request->phone,
            'company'     => $request->company,
            'logo'        => null,
            // Self-registered clients bypass admin approval entirely.
            // Email verification (is_verified) is the only gate for login.
            'is_approved' => true,
            'is_verified' => false,
        ]);

        $this->sendVerificationEmail($client);

        return redirect()->route('client.verify.pending')
            ->with('verify_email_sent', 'Un email de confirmation a été envoyé à ' . $client->email . '.');
    }

    /**
     * Lien signé affiché dans l'email : expire au bout de 60 minutes et inclut
     * le hash de l'email pour qu'un changement d'email invalide les anciens liens.
     */
    protected function sendVerificationEmail(Client $client): void
    {
        $verifyUrl = URL::temporarySignedRoute(
            'client.verify.email',
            now()->addMinutes(60),
            [
                'id'   => $client->id,
                'hash' => sha1($client->email),
            ]
        );

        // TEMP DEBUG: surface the real SMTP error on screen (FTP-only workflow).
        // Remove this block once email sending works.
        $client->notify(new ClientVerifyEmail($verifyUrl));
    }

    /**
     * Page "vérifiez vos emails" affichée juste après l'inscription.
     */
    public function showVerifyPending()
    {
        return view('client.auth.verify-pending');
    }

    /**
     * Point d'arrivée du lien de l'email : valide la signature + le hash,
     * marque le compte comme vérifié et approuvé, puis connecte l'utilisateur.
     * Protégé par le middleware "signed" dans web.php.
     */
    public function verifyEmail(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        // Second rempart : si l'email a été changé depuis l'envoi, le hash ne
        // correspond plus et le lien doit échouer même s'il est encore signé.
        if (! hash_equals(sha1($client->email), (string) $request->query('hash'))) {
            abort(403, 'Lien de confirmation invalide.');
        }

        if (! $client->is_verified) {
            $client->forceFill([
                'is_verified'    => true,
                'is_approved'    => true,
                'otp_code'       => null,
                'otp_expires_at' => null,
            ])->save();
        }

        // Redirect to login so the user signs in explicitly after activation.
        return redirect()->route('client.login')
            ->with('success', 'Votre compte a été activé avec succès. Veuillez vous connecter maintenant.');
    }

    /**
     * Permet au visiteur de redemander un email si le premier n'est pas arrivé.
     * Protégé par un throttle (voir la route).
     */
    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $client = Client::where('email', $request->email)->first();

        if ($client && ! $client->is_verified) {
            $this->sendVerificationEmail($client);
        }

        // Réponse indépendante de l'existence du compte, pour ne pas divulguer
        // quels emails sont inscrits.
        return back()->with('verify_email_sent',
            'Si un compte existe pour cette adresse, un nouvel email vient d\'être envoyé.');
    }

    /**
     * Inscription rapide sans email : nom + téléphone + mot de passe.
     * Le téléphone sert d'identifiant de connexion.
     */
    public function registerWithPhone(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:clients,phone',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $client = Client::create([
            'name'        => $request->name,
            'email'       => null,
            'password'    => Hash::make($request->password),
            'type'        => 'particulier',
            'phone'       => $request->phone,
            'company'     => null,
            'logo'        => null,
            'is_approved' => true,
            'is_verified' => true,
        ]);

        Auth::guard('client')->login($client);

        return redirect()->route('client.dashboard')
            ->with('success', 'Votre compte a été créé avec succès.');
    }
}
