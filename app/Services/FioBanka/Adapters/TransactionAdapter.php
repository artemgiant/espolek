<?php

declare(strict_types=1);

namespace App\Services\FioBanka\Adapters;

use App\Services\FioBanka\DTO\AccountStatementDTO;
use App\Services\FioBanka\DTO\TransactionDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Адаптер для перетворення JSON відповіді FioBanka API в DTO.
 * 
 * Маппінг колонок:
 * - column0  → date (Datum)
 * - column1  → amount (Objem)
 * - column2  → counter_account (Protiúčet)
 * - column3  → counter_bank_code (Kód banky)
 * - column4  → ks (Konstantní symbol)
 * - column5  → vs (Variabilní symbol)
 * - column6  → ss (Specifický symbol)
 * - column7  → user_identification (Uživatelská identifikace)
 * - column8  → transaction_type (Typ)
 * - column9  → executor (Provedl)
 * - column10 → counter_account_name (Název protiúčtu)
 * - column12 → counter_bank_name (Název banky)
 * - column14 → currency (Měna)
 * - column16 → recipient_message (Zpráva pro příjemce)
 * - column17 → instruction_id (ID pokynu)
 * - column18 → specification (Upřesnění)
 * - column22 → transaction_id (ID pohybu)
 * - column25 → comment (Komentář)
 */
class TransactionAdapter
{
    /**
     * Перетворює повну відповідь API в AccountStatementDTO.
     *
     * @param array<string, mixed> $response
     */
    public static function fromApiResponse(array $response): AccountStatementDTO
    {
        $statement = $response['accountStatement'];
        $info = $statement['info'];
        $transactionList = $statement['transactionList']['transaction'] ?? [];

        // Парсимо транзакції
        $transactions = collect($transactionList)
            ->map(fn (array $item) => self::parseTransaction($item));

        return new AccountStatementDTO(
            accountId: $info['accountId'],
            bankId: $info['bankId'],
            currency: $info['currency'],
            iban: $info['iban'],
            bic: $info['bic'],
            openingBalance: (float) $info['openingBalance'],
            closingBalance: (float) $info['closingBalance'],
            dateStart: self::parseDate($info['dateStart']),
            dateEnd: self::parseDate($info['dateEnd']),
            idFrom: $info['idFrom'] ?? null,
            idTo: $info['idTo'] ?? null,
            transactions: $transactions,
        );
    }

    /**
     * Парсить одну транзакцію з API формату.
     *
     * @param array<string, mixed> $data
     */
    private static function parseTransaction(array $data): TransactionDTO
    {
        return new TransactionDTO(
            transactionId: (string) self::getValue($data, 'column22'),
            date: self::parseDate(self::getValue($data, 'column0')),
            amount: (float) self::getValue($data, 'column1'),
            currency: (string) self::getValue($data, 'column14'),
            counterAccount: self::getValue($data, 'column2'),
            counterBankCode: self::getValue($data, 'column3'),
            counterBankName: self::getValue($data, 'column12'),
            counterAccountName: self::getValue($data, 'column10'),
            ks: self::getValue($data, 'column4'),
            vs: self::getValue($data, 'column5'),
            ss: self::getValue($data, 'column6'),
            userIdentification: self::getValue($data, 'column7'),
            recipientMessage: self::getValue($data, 'column16'),
            transactionType: self::getValue($data, 'column8'),
            executor: self::getValue($data, 'column9'),
            comment: self::getValue($data, 'column25'),
            instructionId: self::getValue($data, 'column17') !== null
                ? (string) self::getValue($data, 'column17')
                : null,
            specification: self::getValue($data, 'column18'),
        );
    }

    /**
     * Витягує значення з колонки API.
     * 
     * Формат колонки: {"value": "...", "name": "...", "id": ...} або null
     */
    private static function getValue(array $data, string $column): mixed
    {
        if (!isset($data[$column]) || $data[$column] === null) {
            return null;
        }

        $value = $data[$column]['value'] ?? null;

        // Пустий рядок вважаємо за null
        if ($value === '') {
            return null;
        }

        return $value;
    }

    /**
     * Парсить дату з формату FioBanka (напр. "2025-11-19+0100").
     */
    private static function parseDate(string $dateString): Carbon
    {
        // Видаляємо timezone offset для простішого парсингу
        $cleanDate = preg_replace('/[+-]\d{4}$/', '', $dateString);

        return Carbon::parse($cleanDate);
    }
}
