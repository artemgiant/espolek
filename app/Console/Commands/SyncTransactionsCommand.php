<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\FioSync\SyncAllBankAccountsJob;
use Illuminate\Console\Command;

/**
 * Artisan команда для синхронізації банківських транзакцій.
 *
 * Використання:
 * - php artisan transactions:sync                    — всі акаунти
 * - php artisan transactions:sync --from=2025-01-01  — всі, від дати
 * - php artisan transactions:sync --account=5       — один акаунт
 * - php artisan transactions:sync --account=5 --from=2025-01-01
 */
class SyncTransactionsCommand extends Command
{
    /**
     * Назва та сигнатура команди.
     */
    protected $signature = 'transactions:sync 
                            {--from= : Початкова дата синхронізації (Y-m-d)}
                            {--account= : ID конкретного банківського акаунту}';

    /**
     * Опис команди.
     */
    protected $description = 'Синхронізувати банківські транзакції з FioBanka API';

    public function handle(): int
    {
        $dateFrom = $this->option('from');
        $accountId = $this->option('account');

        // Валідація дати
        if ($dateFrom !== null && !$this->isValidDate($dateFrom)) {
            $this->error("Невірний формат дати: {$dateFrom}. Використовуйте формат Y-m-d (наприклад, 2025-01-01)");
            return Command::FAILURE;
        }

        // Валідація account ID
        if ($accountId !== null && !is_numeric($accountId)) {
            $this->error("Account ID має бути числом: {$accountId}");
            return Command::FAILURE;
        }

        // Виводимо інформацію
        $this->info('Запуск синхронізації транзакцій...');

        if ($accountId) {
            $this->info("Акаунт: #{$accountId}");
        } else {
            $this->info('Акаунти: всі активні');
        }

        if ($dateFrom) {
            $this->info("Період: від {$dateFrom} до сьогодні");
        } else {
            $this->info('Період: від last_sync_at або 90 днів');
        }

        // Dispatch job
        SyncAllBankAccountsJob::dispatch(
            dateFrom: $dateFrom,
            bankAccountId: $accountId ? (int) $accountId : null,
        );

        $this->info('');
        $this->info('✓ Job відправлено в чергу!');
        $this->info('');
        $this->info('Для моніторингу використовуйте:');
        $this->info('  - Horizon: /horizon');
        $this->info('  - Логи: tail -f storage/logs/laravel.log');

        return Command::SUCCESS;
    }

    /**
     * Перевіряє чи дата має правильний формат.
     */
    private function isValidDate(string $date): bool
    {
        $parsed = \DateTime::createFromFormat('Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }
}
