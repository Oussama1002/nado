<?php

namespace App\Services;

use App\Exceptions\InsufficientTokensException;
use App\Models\Client;
use App\Models\TokenPurchase;
use App\Models\TokenTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * The ONLY place that mutates a client's token balance.
 *
 * 1 token = 1 MAD. Costs and the signup bonus live in the `tokens.*` settings
 * group so admin can tune them with no redeploy (see SettingsSeeder / helpers
 * settings() + feature_on()).
 *
 * Every mutation writes a token_transactions ledger row AND updates the cached
 * clients.tokens_balance column inside one DB transaction, with a row lock, so
 * two concurrent requests can never double-spend.
 */
class TokenService
{
    /** Master on/off — when off, nothing is charged and nothing is granted. */
    public static function enabled(): bool
    {
        return feature_on('tokens.enabled');
    }

    /** Token cost of an action, read from tokens.cost.<key> (default 0 = free). */
    public static function cost(string $key): int
    {
        return (int) settings("tokens.cost.$key", 0);
    }

    /** Current spendable balance. */
    public static function balance(Client $client): int
    {
        return (int) $client->tokens_balance;
    }

    /** Can this client afford `cost` tokens right now? */
    public static function canAfford(Client $client, int $cost): bool
    {
        return self::balance($client) >= $cost;
    }

    /**
     * Grant the one-time signup bonus. Idempotent: a client that already has a
     * signup_bonus ledger row is never granted twice, so it's safe to call from
     * every registration path (and from a model `created` event).
     *
     * @return bool true if a bonus was granted on this call.
     */
    public static function grantSignupBonus(Client $client): bool
    {
        if (! self::enabled()) {
            return false;
        }

        $bonus = (int) settings('tokens.signup_bonus', 0);
        if ($bonus <= 0) {
            return false;
        }

        return (bool) DB::transaction(function () use ($client, $bonus) {
            /** @var Client $locked */
            $locked = Client::whereKey($client->getKey())->lockForUpdate()->first();
            if (! $locked) {
                return false;
            }

            $already = TokenTransaction::where('client_id', $locked->id)
                ->where('type', TokenTransaction::TYPE_SIGNUP_BONUS)
                ->exists();
            if ($already) {
                return false;
            }

            self::applyMovement(
                $locked,
                $bonus,
                TokenTransaction::TYPE_SIGNUP_BONUS,
                'signup_bonus'
            );

            // Keep the passed-in instance in sync for the caller.
            $client->tokens_balance = $locked->tokens_balance;

            return true;
        });
    }

    /**
     * Spend tokens on an action. Throws InsufficientTokensException if the
     * balance can't cover `cost`. A cost of 0 (free / tokens disabled) is a
     * no-op and returns null.
     *
     * @param  Model|null  $reference  the thing being paid for (Estimation, Annonce, …)
     * @return TokenTransaction|null
     */
    public static function spend(
        Client $client,
        int $cost,
        string $reason,
        ?Model $reference = null,
        array $meta = []
    ): ?TokenTransaction {
        if (! self::enabled() || $cost <= 0) {
            return null;
        }

        return DB::transaction(function () use ($client, $cost, $reason, $reference, $meta) {
            /** @var Client $locked */
            $locked = Client::whereKey($client->getKey())->lockForUpdate()->first();
            if (! $locked) {
                throw new InsufficientTokensException($cost, 0);
            }

            if ((int) $locked->tokens_balance < $cost) {
                throw new InsufficientTokensException($cost, (int) $locked->tokens_balance);
            }

            $txn = self::applyMovement(
                $locked,
                -$cost,
                TokenTransaction::TYPE_SPEND,
                $reason,
                $reference,
                $meta
            );

            $client->tokens_balance = $locked->tokens_balance;

            return $txn;
        });
    }

    /**
     * Credit tokens after a confirmed payment. Call this from the gateway
     * callback (or an admin action for cash/bank_transfer) once the purchase is
     * marked paid.
     */
    public static function credit(Client $client, int $tokens, ?TokenPurchase $purchase = null, array $meta = []): ?TokenTransaction
    {
        if ($tokens <= 0) {
            return null;
        }

        return DB::transaction(function () use ($client, $tokens, $purchase, $meta) {
            /** @var Client $locked */
            $locked = Client::whereKey($client->getKey())->lockForUpdate()->first();

            $txn = self::applyMovement(
                $locked,
                $tokens,
                TokenTransaction::TYPE_PURCHASE,
                'purchase',
                $purchase,
                $meta
            );

            $client->tokens_balance = $locked->tokens_balance;

            return $txn;
        });
    }

    /**
     * Refund a previous spend (e.g. a premium annonce that was rejected). Pass
     * the original spend transaction; its (absolute) amount is credited back.
     */
    public static function refund(TokenTransaction $spend, string $reason = 'refund', array $meta = []): ?TokenTransaction
    {
        $amount = abs($spend->amount);
        if ($amount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($spend, $amount, $reason, $meta) {
            /** @var Client $locked */
            $locked = Client::whereKey($spend->client_id)->lockForUpdate()->first();
            if (! $locked) {
                return null;
            }

            return self::applyMovement(
                $locked,
                $amount,
                TokenTransaction::TYPE_REFUND,
                $reason,
                $spend,
                $meta + ['refund_of' => $spend->id]
            );
        });
    }

    /**
     * Admin manual credit/debit. Positive `delta` credits, negative debits
     * (clamped so the balance never goes below zero).
     */
    public static function adjust(Client $client, int $delta, string $note = '', array $meta = []): ?TokenTransaction
    {
        if ($delta === 0) {
            return null;
        }

        return DB::transaction(function () use ($client, $delta, $note, $meta) {
            /** @var Client $locked */
            $locked = Client::whereKey($client->getKey())->lockForUpdate()->first();
            if (! $locked) {
                return null;
            }

            if ($delta < 0) {
                $delta = -min(abs($delta), (int) $locked->tokens_balance);
                if ($delta === 0) {
                    return null;
                }
            }

            $txn = self::applyMovement(
                $locked,
                $delta,
                TokenTransaction::TYPE_ADMIN_ADJUST,
                'admin_adjust',
                null,
                $meta + ['note' => $note]
            );

            $client->tokens_balance = $locked->tokens_balance;

            return $txn;
        });
    }

    /**
     * Low-level: apply a signed movement to an already-locked client and write
     * the ledger row. MUST run inside a transaction on a lockForUpdate()'d
     * client. Returns the created ledger row.
     */
    protected static function applyMovement(
        Client $locked,
        int $amount,
        string $type,
        ?string $reason = null,
        ?Model $reference = null,
        array $meta = []
    ): TokenTransaction {
        $newBalance = (int) $locked->tokens_balance + $amount;

        $locked->tokens_balance = $newBalance;
        $locked->save();

        return TokenTransaction::create([
            'client_id'      => $locked->id,
            'amount'         => $amount,
            'type'           => $type,
            'reason'         => $reason,
            'balance_after'  => $newBalance,
            'reference_type' => $reference ? $reference->getMorphClass() : null,
            'reference_id'   => $reference?->getKey(),
            'meta'           => $meta ?: null,
        ]);
    }
}
