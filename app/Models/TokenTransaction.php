<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * One immutable line in a client's token ledger.
 *
 * The client's tokens_balance column is the cached running total; this table is
 * the source of truth. Never update a row after creation — corrections are new
 * rows (refund / admin_adjust). All writes go through App\Services\TokenService.
 */
class TokenTransaction extends Model
{
    public const TYPE_SIGNUP_BONUS = 'signup_bonus';
    public const TYPE_PURCHASE     = 'purchase';
    public const TYPE_SPEND        = 'spend';
    public const TYPE_REFUND       = 'refund';
    public const TYPE_ADMIN_ADJUST = 'admin_adjust';

    protected $fillable = [
        'client_id',
        'amount',
        'type',
        'reason',
        'balance_after',
        'reference_type',
        'reference_id',
        'meta',
    ];

    protected $casts = [
        'amount'        => 'integer',
        'balance_after' => 'integer',
        'meta'          => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /** Debit (spend) vs credit (grant/purchase/refund). */
    public function isDebit(): bool
    {
        return $this->amount < 0;
    }
}
