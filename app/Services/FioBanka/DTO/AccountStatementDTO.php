<?php

declare(strict_types=1);

namespace App\Services\FioBanka\DTO;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Data Transfer Object для повної відповіді FioBanka API.
 *
 * Містить інформацію про рахунок та колекцію транзакцій.
 */
 class AccountStatementDTO
{
    /**
     * @param Collection<int, TransactionDTO> $transactions
     */
    public function __construct(
        // Інформація про рахунок
        public string $accountId,
        public string $bankId,
        public string $currency,
        public string $iban,
        public string $bic,
        public float $openingBalance,
        public float $closingBalance,
        public Carbon $dateStart,
        public Carbon $dateEnd,
        public ?int $idFrom,
        public ?int $idTo,

        // Колекція транзакцій
        public Collection $transactions,
    ) {}

    /**
     * Перевіряє чи є транзакції у відповіді.
     */
    public function hasTransactions(): bool
    {
        return $this->transactions->isNotEmpty();
    }

    /**
     * Повертає кількість транзакцій.
     */
    public function transactionsCount(): int
    {
        return $this->transactions->count();
    }

    /**
     * Повертає суму всіх транзакцій.
     */
    public function totalAmount(): float
    {
        return $this->transactions->sum(fn (TransactionDTO $t) => $t->amount);
    }
}
