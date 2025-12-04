<?php

declare(strict_types=1);

namespace App\Jobs\FioSync;

use App\Models\BankAccount;
use Carbon\Carbon;
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
 * Для валютних рахунків (EUR, USD):
 * - Спочатку FetchExchangeRatesJob (завантаження курсів)
 * - Потім SyncBankAccountJob (синхронізація транзакцій)
 *
 * Для CZK рахунків:
 * - Тільки SyncBankAccountJob
 */
class SyncAllBankAccountsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const PERIOD_DAYS = 5;
    private const DELAY_SECONDS = 35;
    private const DEFAULT_DAYS_BACK = 90;

    public function viaQueue(): string
    {
        return 'sync-transactions';
    }

    public function __construct(
        public ?string $dateFrom = null,
        public ?int $bankAccountId = null,
    ) {}

    public function handle(): void
    {
        Log::info('SyncAllBankAccountsJob: starting', [
            'date_from' => $this->dateFrom,
            'bank_account_id' => $this->bankAccountId,
        ]);

        $accounts = $this->getBankAccounts();

        if ($accounts->isEmpty()) {
            Log::warning('SyncAllBankAccountsJob: no active bank accounts found');
            return;
        }

        Log::info('SyncAllBankAccountsJob: processing accounts', [
            'count' => $accounts->count(),
        ]);

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
            'currency' => $account->currency,
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
        ]);

        // Створюємо jobs для синхронізації транзакцій
        $syncJobs = $this->createSyncJobs($account, $dateFrom, $dateTo);

        if (empty($syncJobs)) {
            Log::info('SyncAllBankAccountsJob: no jobs to dispatch for account', [
                'bank_account_id' => $account->id,
            ]);
            return;
        }

        // Для валютних рахунків додаємо FetchExchangeRatesJob на початок chain
        dump($account->currency);
        Log::info($account->currency);
        if ($account->currency !== 'CZK') {
            $fetchRatesJob = new FetchExchangeRatesJob(
                dateFrom: $dateFrom,
                dateTo: $dateTo,
                currency: $account->currency,
            );

            // Chain: спочатку курси, потім транзакції
            $jobs = array_merge([$fetchRatesJob], $syncJobs);
        } else {
            $jobs = $syncJobs;
        }

        // Створюємо batch
        Bus::batch($jobs)
            ->name("sync-bank-account-{$account->id}")
            ->allowFailures(false) // Якщо курси не завантажились — зупиняємо
            ->finally(function () use ($account) {
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
        if ($this->dateFrom !== null) {
            return Carbon::parse($this->dateFrom);
        }

        if ($account->last_sync_at !== null) {
            return $account->last_sync_at->copy();
        }

        return now()->subDays(self::DEFAULT_DAYS_BACK);
    }

    /**
     * Створює масив SyncBankAccountJob для періоду.
     *
     * @return array<int, SyncBankAccountJob>
     */
    private function createSyncJobs(BankAccount $account, Carbon $dateFrom, Carbon $dateTo): array
    {
        $jobs = [];
        $delayIndex = 0;

        $periodStart = $dateFrom->copy();

        while ($periodStart->lt($dateTo)) {
            $periodEnd = $periodStart->copy()->addDays(self::PERIOD_DAYS - 1);

            if ($periodEnd->gt($dateTo)) {
                $periodEnd = $dateTo->copy();
            }

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
