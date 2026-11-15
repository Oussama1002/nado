<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown by TokenService::spend() when a client's balance can't cover a cost.
 * Controllers catch this to redirect to the top-up page with a friendly message.
 */
class InsufficientTokensException extends RuntimeException
{
    public int $required;
    public int $available;

    public function __construct(int $required, int $available)
    {
        $this->required  = $required;
        $this->available = $available;

        parent::__construct(
            "Insufficient tokens: {$required} required, {$available} available."
        );
    }
}
