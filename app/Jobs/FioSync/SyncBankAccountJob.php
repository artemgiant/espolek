<?php

declare(strict_types=1);

namespace App\Jobs\FioSync ;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Services\FioBanka\DTO\TransactionDTO;
use App\Services\FioBanka\Exceptions\RateLimitException;
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
 * Job для синхронізації транзакцій одного банківського акаунту за конкретний період.
 *
 * Виконує один HTTP запит до FioBanka API та зберігає отримані транзакції.
 *
 * Важливо:
 * - Має затримку 35 секунд між запитами (ліміт API - 30 сек)
 * - При помилці 409 (rate limit) робить retry
 * - При інших помилках - зупиняє тільки цей job, інші в batch продовжують
 */
class SyncBankAccountJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Кількість спроб виконання job.
     */
    public int $tries = 3;

    /**
     * Затримка між спробами (секунди): 35, 60, 120.
     *
     * @var array<int>
     */
    public array $backoff = [35, 60, 120];

    /**
     * Таймаут виконання job (секунди).
     */
    public int $timeout = 120;

    public function __construct(
        public  BankAccount $bankAccount,
        public  Carbon $dateFrom,
        public  Carbon $dateTo,
    ) {}

    /**
     * Унікальний ідентифікатор job для запобігання дублювання.
     */
    public function uniqueId(): string
    {
        return sprintf(
            'sync-bank-%d-%s-%s',
            $this->bankAccount->id,
            $this->dateFrom->format('Y-m-d'),
            $this->dateTo->format('Y-m-d')
        );
    }

    public function handle(FioBankaClient $client): void
    {
        // Перевіряємо чи batch не був скасований
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

        // Отримуємо транзакції з API
        $statement = $client->getTransactions(
            token: $this->bankAccount->api_token,
            dateFrom: $this->dateFrom,
            dateTo: $this->dateTo,
        );

        Log::info('SyncBankAccountJob: received transactions', [
            'bank_account_id' => $this->bankAccount->id,
            'count' => $statement->transactionsCount(),
        ]);

        // Зберігаємо транзакції
        $created = 0;
        $updated = 0;

        foreach ($statement->transactions as $dto) {
            /** @var TransactionDTO $dto */
            $result = $this->saveTransaction($dto);

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
    private function saveTransaction(TransactionDTO $dto): string
    {
        $existing = Transaction::where('bank_account_id', $this->bankAccount->id)
            ->where('transaction_id', $dto->transactionId)
            ->first();

        $data = array_merge($dto->toArray(), [
            'bank_account_id' => $this->bankAccount->id,
            'operation_type' => $dto->getOperationType(),
        ]);

        if ($existing) {
            $existing->update($data);
            return 'updated';
        }

        Transaction::create($data);
        return 'created';
    }

    /**
     * Обробка невдалого виконання job.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SyncBankAccountJob: failed', [
            'bank_account_id' => $this->bankAccount->id,
            'date_from' => $this->dateFrom->format('Y-m-d'),
            'date_to' => $this->dateTo->format('Y-m-d'),
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Визначає чи потрібно робити retry при певних exceptions.
     */
    public function retryUntil(): \DateTime
    {
        // Максимум 10 хвилин на всі retry
        return now()->addMinutes(10);
    }
}
