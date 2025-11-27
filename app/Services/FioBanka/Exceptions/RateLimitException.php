<?php

declare(strict_types=1);

namespace App\Services\FioBanka\Exceptions;

/**
 * Помилка ліміту запитів (HTTP 409).
 * 
 * FioBanka дозволяє лише 1 запит на 30 секунд для одного токену.
 * При отриманні цієї помилки job повинен зробити retry.
 */
class RateLimitException extends FioBankaException
{
    public function __construct(string $message = 'Rate limit exceeded. Wait 30 seconds before next request.')
    {
        parent::__construct($message, 409);
    }
}
