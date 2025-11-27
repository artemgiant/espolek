<?php

declare(strict_types=1);

namespace App\Jobs\FioSync;

use App\Models\BankAccount;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

/**
 * Головний Job для синхронізації транзакцій всіх банківських акаунтів.
 *
 * Логіка роботи:
 * 1. Отримує всі активні банківські акаунти (або конкретний, якщо вказано)
 * 2. Для кожного акаунту створює окремий Batch
 * 3. Кожен Batch містить jobs розбиті по 5-денних періодах
 * 4. Jobs в batch виконуються послідовно з затримкою 35 секунд (chain)
 *
 * Приклад використання:
 * - SyncAllBankAccountsJob::dispatch() — всі акаунти, період від last_sync_at
 * - SyncAllBankAccountsJob::dispatch(dateFrom: '2025-01-01') — всі, від конкретної дати
 * - SyncAllBankAccountsJob::dispatch(bankAccountId: 5) — тільки один акаунт
 */
class SyncAllBankAccountsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Кількість днів в одному періоді для API запиту.
     */
    private const PERIOD_DAYS = 5;

    /**
     * Затримка між запитами в секундах (ліміт API = 30 сек).
     */
    private const DELAY_SECONDS = 35;

    /**
     * Кількість днів за замовчуванням якщо last_sync_at пустий.
     */
    private const DEFAULT_DAYS_BACK = 90;

    public function __construct(
        public  ?string $dateFrom = null,
        public  ?int $bankAccountId = null,
    ) {}

    public function handle(): void
    {
        Log::info('SyncAllBankAccountsJob: starting', [
            'date_from' => $this->dateFrom,
            'bank_account_id' => $this->bankAccountId,
        ]);

        // Отримуємо акаунти для синхронізації
        $accounts = $this->getBankAccounts();

        if ($accounts->isEmpty()) {
            Log::warning('SyncAllBankAccountsJob: no active bank accounts found');
            return;
        }

        Log::info('SyncAllBankAccountsJob: processing accounts', [
            'count' => $accounts->count(),
        ]);

        // Створюємо batch для кожного акаунту
        foreach ($accounts as $account) {
            $this->createBatchForAccount($account);
        }
    }

    /**
     * Отримує банківські акаунти для синхронізації.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, BankAccount>
     */
    private function getBankAccounts()
    {
        $query = BankAccount::active();

        if ($this->bankAccountId !== null) {
            $query->where('id', $this->bankAccountId);
        }

        return $query->get();
    }

    /**
     * Створює Batch з jobs для одного акаунту.
     */
    private function createBatchForAccount(BankAccount $account): void
    {
        $dateTo = now();
        $dateFrom = $this->getDateFrom($account);

        Log::info('SyncAllBankAccountsJob: creating batch for account', [
            'bank_account_id' => $account->id,
            'account_name' => $account->account_name,
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
        ]);

        // Розбиваємо період на 5-денні інтервали
        $jobs = $this->createJobsForPeriod($account, $dateFrom, $dateTo);

        if (empty($jobs)) {
            Log::info('SyncAllBankAccountsJob: no jobs to dispatch for account', [
                'bank_account_id' => $account->id,
            ]);
            return;
        }

        // Створюємо batch з chain (послідовне виконання з затримкою)
        Bus::batch($jobs)
            ->name("sync-bank-account-{$account->id}")
            ->allowFailures() // Дозволяємо продовжувати при помилці окремих jobs
            ->finally(function () use ($account) {
                // Оновлюємо last_sync_at після завершення всіх jobs
                $account->update(['last_sync_at' => now()]);

                Log::info('SyncAllBankAccountsJob: batch completed', [
                    'bank_account_id' => $account->id,
                ]);
            })
            ->dispatch();

        Log::info('SyncAllBankAccountsJob: batch dispatched', [
            'bank_account_id' => $account->id,
            'jobs_count' => count($jobs),
        ]);
    }

    /**
     * Визначає початкову дату для синхронізації.
     */
    private function getDateFrom(BankAccount $account): Carbon
    {
        // Якщо передано явно — використовуємо його
        if ($this->dateFrom !== null) {
            return Carbon::parse($this->dateFrom);
        }

        // Якщо є last_sync_at — беремо від нього
        if ($account->last_sync_at !== null) {
            return $account->last_sync_at->copy();
        }

        // Інакше — 90 днів назад
        return now()->subDays(self::DEFAULT_DAYS_BACK);
    }

    /**
     * Створює масив jobs для всього періоду, розбитого по 5 днів.
     *
     * @return array<int, SyncBankAccountJob>
     */
    private function createJobsForPeriod(BankAccount $account, Carbon $dateFrom, Carbon $dateTo): array
    {
        $jobs = [];
        $delayIndex = 0;

        // Розбиваємо період на інтервали по 5 днів
        $periodStart = $dateFrom->copy();

        while ($periodStart->lt($dateTo)) {
            $periodEnd = $periodStart->copy()->addDays(self::PERIOD_DAYS - 1);

            // Не виходимо за межі dateTo
            if ($periodEnd->gt($dateTo)) {
                $periodEnd = $dateTo->copy();
            }

            // Створюємо job з затримкою
            $delay = $delayIndex * self::DELAY_SECONDS;

            $jobs[] = (new SyncBankAccountJob(
                bankAccount: $account,
                dateFrom: $periodStart->copy(),
                dateTo: $periodEnd->copy(),
            ))->delay(now()->addSeconds($delay));

            $periodStart = $periodEnd->copy()->addDay();
            $delayIndex++;
        }

        return $jobs;
    }
}
