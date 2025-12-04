<?php

declare(strict_types=1);

namespace App\Services\Cnb;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Сервіс для роботи з курсами валют CNB.
 *
 * API: https://api.cnb.cz/cnbapi/swagger-ui.html
 */
class ExchangeRateService
{
    private const BASE_URL = 'https://api.cnb.cz/cnbapi';
    private const CACHE_PREFIX = 'cnb:rates';
    private const CACHE_TTL = 60 * 60 * 24 * 30; // 30 днів
    private const TIMEOUT = 30;

    /**
     * Отримує денний курс з API.
     */
    public function getDailyRate(string $currency, Carbon $date): ?float
    {
        $url = sprintf('%s/exrates/daily', self::BASE_URL);

        Log::info('CNB API: fetching daily rate', [
            'currency' => $currency,
            'date' => $date->format('Y-m-d'),
        ]);

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->accept('application/json')
                ->get($url, [
                    'date' => $date->format('Y-m-d'),
                    'lang' => 'EN',
                ]);

            if ($response->failed()) {
                Log::error('CNB API: daily rate failed', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();

            foreach ($data['rates'] ?? [] as $rate) {
                if ($rate['currencyCode'] === $currency) {
                    $rateValue = (float) $rate['rate'] / (int) $rate['amount'];
                    $this->saveToRedis($currency, $date, $rateValue);
                    return $rateValue;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('CNB API: exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Отримує курси за місяць з API.
     *
     * @return array<string, float> [date => rate]
     */
    public function getMonthlyRates(string $currency, int $year, int $month): array
    {
        $url = sprintf('%s/exrates/daily-currency-month', self::BASE_URL);
        $yearMonth = sprintf('%d-%02d', $year, $month);

        Log::info('CNB API: fetching monthly rates', [
            'currency' => $currency,
            'yearMonth' => $yearMonth,
        ]);

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->accept('application/json')
                ->get($url, [
                    'currency' => $currency,
                    'yearMonth' => $yearMonth,
                    'lang' => 'EN',
                ]);

            if ($response->failed()) {
                Log::error('CNB API: monthly rates failed', [
                    'status' => $response->status(),
                ]);
                return [];
            }

            $rates = [];
            foreach ($response->json()['rates'] ?? [] as $item) {
                $date = Carbon::parse($item['validFor']);
                $rateValue = (float) $item['rate'] / (int) $item['amount'];

                $rates[$date->format('Y-m-d')] = $rateValue;
                $this->saveToRedis($currency, $date, $rateValue);
            }

            Log::info('CNB API: monthly rates saved', [
                'currency' => $currency,
                'count' => count($rates),
            ]);

            return $rates;
        } catch (\Exception $e) {
            Log::error('CNB API: exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Зберігає курс в Redis.
     */
    public function saveToRedis(string $currency, Carbon $date, float $rate): void
    {
        $key = $this->getCacheKey($currency, $date);
        Cache::store('redis')->put($key, $rate, self::CACHE_TTL);
    }

    /**
     * Отримує курс з Redis.
     * Якщо не знайдено — шукає назад до 7 днів (вихідні).
     */
    public function getFromRedis(string $currency, Carbon $date): ?float
    {
        if ($currency === 'CZK') {
            return 1.0;
        }

        for ($i = 0; $i < 7; $i++) {
            $searchDate = $date->copy()->subDays($i);
            $key = $this->getCacheKey($currency, $searchDate);
            $rate = Cache::store('redis')->get($key);

            if ($rate !== null) {
                return (float) $rate;
            }
        }

        return null;
    }

    /**
     * Генерує ключ кешу.
     */
    private function getCacheKey(string $currency, Carbon $date): string
    {
        return sprintf('%s:%s:%s', self::CACHE_PREFIX, $currency, $date->format('Y-m-d'));
    }
}
