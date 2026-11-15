<?php

namespace App\Http\Controllers\Concerns;

use App\Exceptions\InsufficientTokensException;
use App\Models\Client;
use App\Services\TokenService;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared helpers for controllers that charge tokens for an action.
 *
 * Usage inside a controller action:
 *
 *   if ($redirect = $this->requireTokens('estimation')) return $redirect;
 *   // ... do the work ...
 *   $this->chargeTokens($client, 'estimation', $estimation);
 *
 * or, when the work and the charge must be atomic, call
 * TokenService::spend() directly inside your own transaction.
 */
trait ChargesTokens
{
    /**
     * Guard BEFORE doing the work: ensures a client is logged in and can afford
     * the action's cost. Returns a RedirectResponse to send back (login or
     * top-up) when the action must not proceed, or null when it's clear to go.
     */
    protected function requireTokens(string $costKey)
    {
        if (! TokenService::enabled()) {
            return null; // tokens off → everything free
        }

        $cost = TokenService::cost($costKey);
        if ($cost <= 0) {
            return null; // this action is free
        }

        /** @var Client|null $client */
        $client = auth('client')->user();

        if (! $client) {
            return redirect()
                ->guest(route('client.login'))
                ->with('info', __('messages.tokens.login_required'));
        }

        if (! TokenService::canAfford($client, $cost)) {
            return redirect()
                ->route('client.tokens')
                ->with('info', __('messages.tokens.insufficient', [
                    'required'  => $cost,
                    'available' => TokenService::balance($client),
                ]));
        }

        return null;
    }

    /**
     * Charge the client AFTER the work is done. Safe to call even if tokens are
     * off or the cost is 0 (no-op). Re-checks affordability via TokenService,
     * which throws InsufficientTokensException if somehow unaffordable.
     */
    protected function chargeTokens(Client $client, string $costKey, ?Model $reference = null, array $meta = [])
    {
        $cost = TokenService::cost($costKey);

        try {
            return TokenService::spend($client, $cost, $costKey, $reference, $meta);
        } catch (InsufficientTokensException $e) {
            return null;
        }
    }
}
