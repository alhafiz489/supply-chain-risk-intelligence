<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class FrankfurterCurrencyService
{
    private const ENDPOINT =
        'https://api.frankfurter.dev/v2/rates';

    private const BASE_CURRENCY = 'IDR';

    private const LOOKBACK_DAYS = 35;

    public function syncAll(): array
    {
        $from = now()
            ->subDays(self::LOOKBACK_DAYS)
            ->toDateString();

        $to = now()->toDateString();

        $rows = $this->fetchRates($from, $to);
        $seriesByCurrency = $this->buildSeries($rows);

        $updatedCountries = 0;
        $unsupportedCountries = 0;
        $failedCountries = 0;
        $updatedCurrencies = [];
        $unsupportedCurrencies = [];
        $failedNames = [];

        Country::query()
            ->whereNotNull('currency_code')
            ->orderBy('id')
            ->chunkById(
                100,
                function (Collection $countries) use (
                    $seriesByCurrency,
                    &$updatedCountries,
                    &$unsupportedCountries,
                    &$failedCountries,
                    &$updatedCurrencies,
                    &$unsupportedCurrencies,
                    &$failedNames
                ) {
                    foreach ($countries as $country) {
                        try {
                            $currencyCode = strtoupper(
                                trim((string) $country->currency_code)
                            );

                            if ($currencyCode === '') {
                                $unsupportedCountries++;
                                continue;
                            }

                            if (
                                $currencyCode === self::BASE_CURRENCY
                            ) {
                                $country->update([
                                    'exchange_rate_to_idr' => 1,
                                    'currency_volatility_percent' => 0,
                                    'currency_rate_date' =>
                                        now()->toDateString(),
                                    'currency_source' =>
                                        'Frankfurter',
                                    'currency_data_status' =>
                                        'available',
                                    'currency_synced_at' => now(),
                                    'data_completeness_percent' =>
                                        max(
                                            (int) $country
                                                ->data_completeness_percent,
                                            75
                                        ),
                                    'risk_data_status' =>
                                        $country->risk_data_status ===
                                        'ready'
                                            ? 'ready'
                                            : 'partial',
                                ]);

                                $updatedCountries++;
                                $updatedCurrencies[$currencyCode] = true;
                                continue;
                            }

                            $series =
                                $seriesByCurrency[$currencyCode]
                                ?? [];

                            if ($series === []) {
                                $country->update([
                                    'currency_source' =>
                                        'Frankfurter',
                                    'currency_data_status' =>
                                        'unavailable',
                                    'currency_synced_at' => now(),
                                ]);

                                $unsupportedCountries++;
                                $unsupportedCurrencies[$currencyCode] =
                                    true;
                                continue;
                            }

                            ksort($series);

                            $latestDate = array_key_last($series);
                            $latestRate = $series[$latestDate];

                            if (
                                ! is_numeric($latestRate)
                                || (float) $latestRate <= 0
                            ) {
                                $unsupportedCountries++;
                                $unsupportedCurrencies[$currencyCode] =
                                    true;
                                continue;
                            }

                            $volatility =
                                $this->dailyReturnVolatility(
                                    array_values($series)
                                );

                            $country->update([
                                'exchange_rate_to_idr' =>
                                    round(
                                        (float) $latestRate,
                                        2
                                    ),
                                'currency_volatility_percent' =>
                                    round($volatility, 2),
                                'currency_rate_date' => $latestDate,
                                'currency_source' => 'Frankfurter',
                                'currency_data_status' =>
                                    'available',
                                'currency_synced_at' => now(),
                                'data_completeness_percent' =>
                                    max(
                                        (int) $country
                                            ->data_completeness_percent,
                                        75
                                    ),
                                'risk_data_status' =>
                                    $country->risk_data_status ===
                                    'ready'
                                        ? 'ready'
                                        : 'partial',
                            ]);

                            $updatedCountries++;
                            $updatedCurrencies[$currencyCode] = true;
                        } catch (Throwable $exception) {
                            report($exception);

                            $failedCountries++;
                            $failedNames[] = $country->name;
                        }
                    }
                }
            );

        return [
            'updated_countries' => $updatedCountries,
            'updated_currencies' => count($updatedCurrencies),
            'unsupported_countries' => $unsupportedCountries,
            'unsupported_currencies' =>
                array_keys($unsupportedCurrencies),
            'failed_countries' => $failedCountries,
            'failed_names' => array_values(
                array_unique($failedNames)
            ),
            'from' => $from,
            'to' => $to,
        ];
    }

    private function fetchRates(
        string $from,
        string $to
    ): array {
        $response = Http::acceptJson()
            ->connectTimeout(15)
            ->timeout(90)
            ->retry(3, 1000, null, false)
            ->get(self::ENDPOINT, [
                'base' => self::BASE_CURRENCY,
                'from' => $from,
                'to' => $to,
            ]);

        $this->ensureSuccessful($response);

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RuntimeException(
                'Format respons Frankfurter tidak valid.'
            );
        }

        return $payload;
    }

    private function buildSeries(array $rows): array
    {
        $series = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $base = strtoupper(
                trim((string) ($row['base'] ?? ''))
            );

            $quote = strtoupper(
                trim((string) ($row['quote'] ?? ''))
            );

            $date = trim((string) ($row['date'] ?? ''));
            $rate = $row['rate'] ?? null;

            if (
                $base !== self::BASE_CURRENCY
                || $quote === ''
                || $date === ''
                || ! is_numeric($rate)
                || (float) $rate <= 0
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Konversi ke "1 unit mata uang asing = berapa IDR"
            |--------------------------------------------------------------------------
            |
            | API mengembalikan 1 IDR = X mata uang asing. Nilainya dibalik agar
            | sesuai dengan kolom exchange_rate_to_idr.
            |
            */

            $series[$quote][$date] =
                1 / (float) $rate;
        }

        return $series;
    }

    private function dailyReturnVolatility(
        array $values
    ): float {
        $returns = [];
        $previous = null;

        foreach ($values as $value) {
            $value = (float) $value;

            if ($value <= 0) {
                continue;
            }

            if ($previous !== null && $previous > 0) {
                $returns[] =
                    (($value - $previous) / $previous) * 100;
            }

            $previous = $value;
        }

        if (count($returns) < 2) {
            return 0;
        }

        $mean = array_sum($returns)
            / count($returns);

        $variance = array_sum(
            array_map(
                fn (float $return): float =>
                    ($return - $mean) ** 2,
                $returns
            )
        ) / count($returns);

        return sqrt($variance);
    }

    private function ensureSuccessful(
        Response $response
    ): void {
        if ($response->successful()) {
            return;
        }

        $message = data_get(
            $response->json(),
            'message',
            'Frankfurter API gagal merespons.'
        );

        throw new RuntimeException(
            "{$message} HTTP status: {$response->status()}."
        );
    }
}