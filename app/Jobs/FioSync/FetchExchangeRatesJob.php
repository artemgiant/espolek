<?php

declare(strict_types=1);

namespace App\Jobs\FioSync;

use App\Services\Cnb\ExchangeRateService;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job для завантаження курсів валют з CNB API.
 *
 * Логіка:
 * - Період > 7 днів → місячний метод
 * - Період ≤ 7 днів → денний метод
 */
class FetchExchangeRatesJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    /** @var array<int> */
    public array $backoff = [10, 30, 60];

    public function __construct(
        public Carbon $dateFrom,
        public Carbon $dateTo,
        public string $currency,
    ) {}

    public function handle(ExchangeRateService $service): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        Log::info('FetchExchangeRatesJob: starting', [
            'currency' => $this->currency,
            'date_from' => $this->dateFrom->format('Y-m-d'),
            'date_to' => $this->dateTo->format('Y-m-d'),
        ]);

        $periodDays = $this->dateFrom->diffInDays($this->dateTo);

        if ($periodDays > 7) {
            $this->fetchMonthly($service);
        } else {
            $this->fetchDaily($service);
        }

        Log::info('FetchExchangeRatesJob: completed', [
            'currency' => $this->currency,
        ]);
    }

    /**
     * Завантажує курси по місяцях.
     */
    private function fetchMonthly(ExchangeRateService $service): void
    {
        $current = $this->dateFrom->copy()->startOfMonth();
        $end = $this->dateTo->copy()->endOfMonth();

        while ($current->lte($end)) {
            $rates = $service->getMonthlyRates(
                $this->currency,
                $current->year,
                $current->month
            );

            if (empty($rates)) {
                Log::error('FetchExchangeRatesJob: failed to fetch monthly rates', [
                    'currency' => $this->currency,
                    'year' => $current->year,
                    'month' => $current->month,
                ]);
                throw new \RuntimeException("Failed to fetch rates for {$this->currency}");
            }

            $current->addMonth();
        }
    }

    /**
     * Завантажує курси по днях.
     */
    private function fetchDaily(ExchangeRateService $service): void
    {
        $current = $this->dateFrom->copy();

        while ($current->lte($this->dateTo)) {
            // Пропускаємо вихідні
            if (!$current->isWeekend()) {
                $rate = $service->getDailyRate($this->currency, $current);

                if ($rate === null) {
                    Log::warning('FetchExchangeRatesJob: no rate for date', [
                        'currency' => $this->currency,
                        'date' => $current->format('Y-m-d'),
                    ]);
                }
            }

            $current->addDay();
        }
    }


    public function failed(\Throwable $exception): void
    {
        Log::error('FetchExchangeRatesJob: failed', [
            'currency' => $this->currency,
            'error' => $exception->getMessage(),
        ]);

        // Скасувати весь batch — інші jobs не виконуватимуться
        $this->batch()?->cancel();
    }
}
