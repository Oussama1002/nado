<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Enchère refusée (règle métier) : message déjà localisé, prêt à afficher au
 * client. Levée par App\Services\AuctionService.
 */
class BidException extends RuntimeException
{
    public function __construct(string $message, public string $reason = 'invalid')
    {
        parent::__construct($message);
    }
}
