<?php

declare(strict_types=1);

namespace App\Services\FioBanka\Exceptions;

use Exception;

/**
 * Базовий клас для всіх помилок FioBanka API.
 */
class FioBankaException extends Exception
{
    public function __construct(
        string $message = 'FioBanka API error',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
