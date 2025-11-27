<?php

declare(strict_types=1);

namespace App\Services\FioBanka;

use App\Services\FioBanka\Adapters\TransactionAdapter;
use App\Services\FioBanka\DTO\AccountStatementDTO;
use App\Services\FioBanka\Exceptions\FioBankaException;
use App\Services\FioBanka\Exceptions\InvalidTokenException;
use App\Services\FioBanka\Exceptions\RateLimitException;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * HTTP клієнт для FioBanka API.
 *
 * Документація API: https://www.fio.cz/docs/cz/API_Bankovnictvi.pdf
 *
 * Важливі обмеження:
 * - 1 запит на 30 секунд для одного токену
 * - Токен дійсний 180 днів
 */
class FioBankaClient
{
    private const TIMEOUT = 30;

    private string $baseUrl;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl ?? config('services.fio_banka.base_url', 'https://fioapi.fio.cz/v1/rest');
    }

    /**
     * Отримує транзакції за період.
     *
     * @param string $token API токен банківського рахунку
     * @param Carbon $dateFrom Початкова дата періоду
     * @param Carbon $dateTo Кінцева дата періоду
     *
     * @throws RateLimitException Якщо перевищено ліміт запитів (409)
     * @throws InvalidTokenException Якщо токен невалідний (500)
     * @throws FioBankaException Інші помилки API
     */
    public function getTransactions(string $token, Carbon $dateFrom, Carbon $dateTo): AccountStatementDTO
    {
        $url = sprintf(
            '%s/periods/%s/%s/%s/transactions.json',
            $this->baseUrl,
            $token,
            $dateFrom->format('Y-m-d'),
            $dateTo->format('Y-m-d')
        );

        Log::info('FioBanka API request', [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
        ]);

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->accept('application/json')
                ->get($url);
        } catch (ConnectionException $e) {
            Log::error('FioBanka API connection error', [
                'message' => $e->getMessage(),
            ]);
            throw new FioBankaException('Connection to FioBanka API failed: ' . $e->getMessage(), 0, $e);
        }

        // Обробка помилок
        if ($response->status() === 409) {
            Log::warning('FioBanka API rate limit exceeded');
            throw new RateLimitException();
        }

        if ($response->status() === 500) {
            Log::error('FioBanka API invalid token or server error');
            throw new InvalidTokenException();
        }

        if ($response->failed()) {
            Log::error('FioBanka API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new FioBankaException(
                'FioBanka API error: ' . $response->status(),
                $response->status()
            );
        }

        $data = $response->json();

        if (!isset($data['accountStatement'])) {
            Log::error('FioBanka API invalid response format', [
                'response' => $data,
            ]);
            throw new FioBankaException('Invalid API response format');
        }

        Log::info('FioBanka API response received', [
            'transactions_count' => count($data['accountStatement']['transactionList']['transaction'] ?? []),
        ]);

        return TransactionAdapter::fromApiResponse($data);
    }

    /**
     * Отримує транзакції з останнього завантаження.
     *
     * Використовує endpoint /last/ який повертає тільки нові транзакції
     * з моменту останнього виклику цього методу.
     *
     * @throws RateLimitException
     * @throws InvalidTokenException
     * @throws FioBankaException
     */
    public function getLastTransactions(string $token): AccountStatementDTO
    {
        $url = sprintf('%s/last/%s/transactions.json', $this->baseUrl, $token);

        Log::info('FioBanka API request (last transactions)');

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->accept('application/json')
                ->get($url);
        } catch (ConnectionException $e) {
            throw new FioBankaException('Connection to FioBanka API failed: ' . $e->getMessage(), 0, $e);
        }

        if ($response->status() === 409) {
            throw new RateLimitException();
        }

        if ($response->status() === 500) {
            throw new InvalidTokenException();
        }

        if ($response->failed()) {
            throw new FioBankaException('FioBanka API error: ' . $response->status(), $response->status());
        }

        return TransactionAdapter::fromApiResponse($response->json());
    }

    /**
     * Встановлює маркер останнього завантаження.
     *
     * Це дозволяє "скинути" точку для /last/ endpoint.
     *
     * @param string $token API токен
     * @param Carbon $date Дата на яку встановити маркер
     *
     * @throws RateLimitException
     * @throws FioBankaException
     */
    public function setLastDownloadDate(string $token, Carbon $date): void
    {
        $url = sprintf(
            '%s/set-last-date/%s/%s/',
            $this->baseUrl,
            $token,
            $date->format('Y-m-d')
        );

        try {
            $response = Http::timeout(self::TIMEOUT)->get($url);
        } catch (ConnectionException $e) {
            throw new FioBankaException('Connection to FioBanka API failed: ' . $e->getMessage(), 0, $e);
        }

        if ($response->status() === 409) {
            throw new RateLimitException();
        }

        if ($response->failed()) {
            throw new FioBankaException('Failed to set last download date: ' . $response->status());
        }

        Log::info('FioBanka last download date set', [
            'date' => $date->format('Y-m-d'),
        ]);
    }
}
