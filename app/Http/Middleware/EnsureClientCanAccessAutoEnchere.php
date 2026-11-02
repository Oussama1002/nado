<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureClientCanAccessAutoEnchere
{
    public function handle($request, Closure $next)
    {
        $client = Auth::guard('client')->user();

        // Si pas connecté ou type ≠ flotte&enchere → redirige vers login
        if (!$client || $client->type !== 'flotte&enchere') {
            // Optional: logout the client to prevent dashboard access
            Auth::guard('client')->logout();

            return redirect()
                ->route('client.login')
                ->with('flotte_enchere_blocked', 'Votre compte doit être de type Flotte & Enchère pour accéder aux enchères.');
        }

        // Compte Auto Enchère en attente de validation admin → pas d'accès direct.
        if (! $client->ae_approved) {
            return redirect()
                ->route('nadoautoenchere.prelogin')
                ->with('ae_pending', "Votre compte Auto Enchère est en attente de validation par l'administrateur.");
        }

        return $next($request);
    }
}
