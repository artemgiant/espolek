<?php

declare(strict_types=1);

namespace App\Services\FioBanka\DTO;

use Carbon\Carbon;

/**
 * Data Transfer Object для транзакції з FioBanka API.
 *
 * Містить типізовані поля з нормальними назвами замість column0, column1, etc.
 */
class TransactionDTO
{
    public function __construct(
        // Обов'язкові поля
        public string $transactionId,      // column22 - ID pohybu
        public Carbon $date,               // column0  - Datum
        public float $amount,              // column1  - Objem
        public string $currency,           // column14 - Měna

        // Дані контрагента
        public ?string $counterAccount,     // column2  - Protiúčet
        public ?string $counterBankCode,    // column3  - Kód banky
        public ?string $counterBankName,    // column12 - Název banky
        public ?string $counterAccountName, // column10 - Název protiúčtu

        // Символи
        public ?string $ks,                 // column4  - Konstantní symbol
        public ?string $vs,                 // column5  - Variabilní symbol
        public ?string $ss,                 // column6  - Specifický symbol

        // Опис транзакції
        public ?string $userIdentification, // column7  - Uživatelská identifikace
        public ?string $recipientMessage,   // column16 - Zpráva pro příjemce
        public ?string $transactionType,    // column8  - Typ
        public ?string $executor,           // column9  - Provedl
        public ?string $comment,            // column25 - Komentář
        public ?string $instructionId,      // column17 - ID pokynu
        public ?string $specification,      // column18 - Upřesnění (напр. "1225.00 EUR")
    ) {}

    /**
     * Конвертує DTO в масив для збереження в БД.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'date' => $this->date->toDateString(),
            'amount' => $this->amount,
            'currency' => $this->currency,
            'counter_account' => $this->counterAccount,
            'counter_bank_code' => $this->counterBankCode,
            'counter_bank_name' => $this->counterBankName,
            'counter_bic' => null, // Завжди null - не приходить з API
            'counter_account_name' => $this->counterAccountName,
            'ks' => $this->ks,
            'vs' => $this->vs,
            'ss' => $this->ss,
            'user_identification' => $this->userIdentification,
            'recipient_message' => $this->recipientMessage,
            'transaction_type' => $this->transactionType,
            'executor' => $this->executor,
            'comment' => $this->comment,
            'instruction_id' => $this->instructionId,
            'specification' => $this->specification,
        ];
    }

    /**
     * Визначає тип операції на основі суми.
     */
    public function getOperationType(): string
    {
        return $this->amount >= 0 ? 'income' : 'expense';
    }
}
