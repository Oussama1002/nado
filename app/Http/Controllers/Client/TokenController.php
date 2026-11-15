<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TokenPurchase;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Espace « Tokens » du client : solde, historique (grand livre) et recharge.
 *
 * PHASE 1 : la recharge crée une demande "pending" et renvoie vers une page de
 * confirmation « à régler » (aucune passerelle branchée). PHASE 2 : checkout()
 * redirigera vers CMI, dont le callback marquera la recharge payée puis
 * appellera TokenService::credit().
 */
class TokenController extends Controller
{
    /** Montants de recharge proposés (en tokens = MAD). */
    private const PRESETS = [100, 300, 500, 1000, 2000];

    public function index()
    {
        $client = Auth::guard('client')->user();

        $transactions = $client->tokenTransactions()->paginate(20);

        return view('client.tokens.index', [
            'client'       => $client,
            'balance'      => TokenService::balance($client),
            'transactions' => $transactions,
            'presets'      => self::PRESETS,
            'enabled'      => TokenService::enabled(),
            'currency'     => settings('payments.currency', 'MAD'),
        ]);
    }

    /**
     * Démarre une recharge. Pour l'instant : enregistre une intention de paiement
     * (pending) sans débiter ni créditer — les tokens ne sont crédités qu'une
     * fois le paiement confirmé (phase 2).
     */
    public function checkout(Request $request)
    {
        $client = Auth::guard('client')->user();

        $data = $request->validate([
            'tokens' => ['required', 'integer', 'min:10', 'max:100000'],
        ]);

        $purchase = TokenPurchase::create([
            'client_id'  => $client->id,
            'tokens'     => $data['tokens'],
            'amount_mad' => $data['tokens'], // 1 token = 1 MAD
            'gateway'    => TokenPurchase::GATEWAY_CMI,
            'status'     => TokenPurchase::STATUS_PENDING,
        ]);

        return redirect()
            ->route('client.tokens')
            ->with('info', __('messages.tokens.topup_pending', [
                'tokens' => $purchase->tokens,
            ]));
    }
}
