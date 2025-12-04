<?php

declare(strict_types=1);

namespace App\Jobs\FioSync;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Services\Cnb\ExchangeRateService;
use App\Services\FioBanka\DTO\TransactionDTO;
use App\Services\FioBanka\FioBankaClient;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job для синхронізації транзакцій одного банківського акаунту.
 *
 * Для кожної транзакції:
 * - CZK: exchange_rate = 1, amount_czk = amount
 * - EUR/USD: exchange_rate з Redis, amount_czk = amount * rate
 */
class SyncBankAccountJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    /** @var array<int> */
    public array $backoff = [35, 60, 120];

    public function __construct(
        public BankAccount $bankAccount,
        public Carbon $dateFrom,
        public Carbon $dateTo,
    ) {}

    public function uniqueId(): string
    {
        return sprintf(
            'sync-bank-%d-%s-%s',
            $this->bankAccount->id,
            $this->dateFrom->format('Y-m-d'),
            $this->dateTo->format('Y-m-d')
        );
    }

    public function handle(FioBankaClient $client, ExchangeRateService $exchangeService): void
    {
        if ($this->batch()?->cancelled()) {
            Log::info('SyncBankAccountJob: batch cancelled, skipping', [
                'bank_account_id' => $this->bankAccount->id,
            ]);
            return;
        }

        Log::info('SyncBankAccountJob: starting', [
            'bank_account_id' => $this->bankAccount->id,
            'account_name' => $this->bankAccount->account_name,
            'date_from' => $this->dateFrom->format('Y-m-d'),
            'date_to' => $this->dateTo->format('Y-m-d'),
        ]);

        $statement = $client->getTransactions(
            token: $this->bankAccount->api_token,
            dateFrom: $this->dateFrom,
            dateTo: $this->dateTo,
        );

        Log::info('SyncBankAccountJob: received transactions', [
            'bank_account_id' => $this->bankAccount->id,
            'count' => $statement->transactionsCount(),
        ]);

        $created = 0;
        $updated = 0;

        foreach ($statement->transactions as $dto) {
            /** @var TransactionDTO $dto */
            $result = $this->saveTransaction($dto, $exchangeService);

            if ($result === 'created') {
                $created++;
            } elseif ($result === 'updated') {
                $updated++;
            }
        }

        Log::info('SyncBankAccountJob: completed', [
            'bank_account_id' => $this->bankAccount->id,
            'created' => $created,
            'updated' => $updated,
        ]);
    }

    /**
     * Зберігає або оновлює транзакцію в БД.
     */
    private function saveTransaction(TransactionDTO $dto, ExchangeRateService $exchangeService): string
    {
        $existing = Transaction::where('bank_account_id', $this->bankAccount->id)
            ->where('transaction_id', $dto->transactionId)
            ->first();

        $data = array_merge($dto->toArray(), [
            'bank_account_id' => $this->bankAccount->id,
            'operation_type' => $dto->getOperationType(),
        ]);

        // Додаємо курс та еквівалент в CZK
        $exchangeData = $this->getExchangeData($dto, $exchangeService);
        $data = array_merge($data, $exchangeData);

        if ($existing) {
            $existing->update($data);
            return 'updated';
        }

        Transaction::create($data);
        return 'created';
    }

    /**
     * Отримує дані курсу валюти.
     *
     * @return array{exchange_rate: float, amount_czk: float}
     */
    private function getExchangeData(TransactionDTO $dto, ExchangeRateService $exchangeService): array
    {
        $currency = $dto->currency;

        // CZK — курс 1, еквівалент = сума
        if ($currency === 'CZK') {
            return [
                'exchange_rate' => 1.0,
                'amount_czk' => $dto->amount,
            ];
        }

        // Отримуємо курс з Redis
        $rate = $exchangeService->getFromRedis($currency, $dto->date);

        if ($rate === null) {
            Log::warning('SyncBankAccountJob: exchange rate not found in Redis', [
                'currency' => $currency,
                'date' => $dto->date->format('Y-m-d'),
                'transaction_id' => $dto->transactionId,
            ]);

            return [
                'exchange_rate' => null,
                'amount_czk' => null,
            ];
        }

        return [
            'exchange_rate' => $rate,
            'amount_czk' => round($dto->amount * $rate, 2),
        ];
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SyncBankAccountJob: failed', [
            'bank_account_id' => $this->bankAccount->id,
            'date_from' => $this->dateFrom->format('Y-m-d'),
            'date_to' => $this->dateTo->format('Y-m-d'),
            'error' => $exception->getMessage(),
        ]);
    }

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }
}
