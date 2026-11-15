<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A single token top-up attempt. Created "pending" when the client starts a
 * checkout, flipped to "paid" by the gateway callback (or an admin for
 * cash/bank_transfer), which is what triggers TokenService::credit().
 */
class TokenPurchase extends Model
{
    public const STATUS_PENDING   = 'pending';
    public const STATUS_PAID      = 'paid';
    public const STATUS_FAILED    = 'failed';
    public const STATUS_REFUNDED  = 'refunded';
    public const STATUS_CANCELLED = 'cancelled';

    public const GATEWAY_CMI           = 'cmi';
    public const GATEWAY_STRIPE        = 'stripe';
    public const GATEWAY_CASH          = 'cash';
    public const GATEWAY_BANK_TRANSFER = 'bank_transfer';

    protected $fillable = [
        'client_id',
        'tokens',
        'amount_mad',
        'gateway',
        'status',
        'gateway_order_id',
        'meta',
        'paid_at',
    ];

    protected $casts = [
        'tokens'     => 'integer',
        'amount_mad' => 'decimal:2',
        'meta'       => 'array',
        'paid_at'    => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
}
