<?php

declare(strict_types=1);

namespace App\Services\FioBanka\Exceptions;

/**
 * Помилка невалідного або простроченого токену.
 * 
 * Токен FioBanka дійсний 180 днів з моменту генерації.
 * При отриманні цієї помилки потрібно оновити токен в налаштуваннях.
 */
class InvalidTokenException extends FioBankaException
{
    public function __construct(string $message = 'Invalid or expired API token.')
    {
        parent::__construct($message, 401);
    }
}
