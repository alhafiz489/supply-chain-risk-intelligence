<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class WorldBankEconomicService
{
    private const BASE_URL = 'https://api.worldbank.org/v2';

    private const INDICATORS = [
        'NY.GDP.MKTP.CD' => 'gdp',
        'FP.CPI.TOTL.ZG' => 'inflation',
        'SP.POP.TOTL' => 'population',
    ];

    public function syncAll(): array
    {
        $endYear = (int) now()->year;
        $startYear = $endYear - 8;

        $rows = $this->fetchIndicators(
            $startYear,
            $endYear
        );

        $latestByCountry = $this->latestValuesByCountry(
            $rows
        );

        $updated = 0;
        $withoutData = 0;
        $gdpUpdated = 0;
        $inflationUpdated = 0;
        $populationUpdated = 0;
        $failed = 0;
        $failedCountries = [];

        Country::query()
            ->whereNotNull('iso3')
            ->orderBy('id')
            ->chunkById(
                100,
                function ($countries) use (
                    $latestByCountry,
                    &$updated,
                    &$withoutData,
                    &$gdpUpdated,
                    &$inflationUpdated,
                    &$populationUpdated,
                    &$failed,
                    &$failedCountries
                ) {
                    foreach ($countries as $country) {
                        try {
                            $iso3 = strtoupper(
                                (string) $country->iso3
                            );

                            $countryData =
                                $latestByCountry[$iso3] ?? [];

                            if ($countryData === []) {
                                $withoutData++;
                                continue;
                            }

                            $updates = [];
                            $availableIndicators = 0;

                            if (
                                isset($countryData['gdp'])
                                && is_numeric(
                                    $countryData['gdp']['value']
                                )
                            ) {
                                $updates['gdp_usd_billion'] =
                                    round(
                                        (float) $countryData['gdp']['value']
                                        / 1_000_000_000,
                                        2
                                    );

                                $updates['gdp_data_year'] =
                                    (int) $countryData['gdp']['year'];

                                $availableIndicators++;
                                $gdpUpdated++;
                            }

                            if (
                                isset($countryData['inflation'])
                                && is_numeric(
                                    $countryData['inflation']['value']
                                )
                            ) {
                                $updates['inflation_rate'] =
                                    round(
                                        (float) $countryData['inflation']['value'],
                                        2
                                    );

                                $updates['inflation_data_year'] =
                                    (int) $countryData['inflation']['year'];

                                $availableIndicators++;
                                $inflationUpdated++;
                            }

                            if (
                                isset($countryData['population'])
                                && is_numeric(
                                    $countryData['population']['value']
                                )
                            ) {
                                $population = max(
                                    0,
                                    (int) round(
                                        (float) $countryData['population']['value']
                                    )
                                );

                                $updates['population'] =
                                    $population;

                                $updates['population_million'] =
                                    round(
                                        $population / 1_000_000,
                                        2
                                    );

                                $updates['population_data_year'] =
                                    (int) $countryData['population']['year'];

                                $availableIndicators++;
                                $populationUpdated++;
                            }

                            if ($updates === []) {
                                $withoutData++;
                                continue;
                            }

                            $baseCompleteness =
                                $country->weather_synced_at !== null
                                    ? 40
                                    : 20;

                            $economicCompleteness = (int) round(
                                ($availableIndicators / 3) * 20
                            );

                            $updates['data_completeness_percent'] =
                                max(
                                    (int) $country
                                        ->data_completeness_percent,
                                    min(
                                        60,
                                        $baseCompleteness
                                        + $economicCompleteness
                                    )
                                );

                            $updates['risk_data_status'] =
                                $country->risk_data_status === 'ready'
                                    ? 'ready'
                                    : 'partial';

                            $updates['economic_synced_at'] = now();

                            $country->update($updates);

                            $updated++;
                        } catch (Throwable $exception) {
                            report($exception);

                            $failed++;
                            $failedCountries[] = $country->name;
                        }
                    }
                }
            );

        return [
            'updated' => $updated,
            'without_data' => $withoutData,
            'failed' => $failed,
            'gdp_updated' => $gdpUpdated,
            'inflation_updated' => $inflationUpdated,
            'population_updated' => $populationUpdated,
            'failed_countries' => array_values(
                array_unique($failedCountries)
            ),
            'period' => "{$startYear}-{$endYear}",
        ];
    }

    private function fetchIndicators(
        int $startYear,
        int $endYear
    ): array {
        $indicatorCodes = implode(
            ';',
            array_keys(self::INDICATORS)
        );

        $url = self::BASE_URL
            .'/country/all/indicator/'
            .$indicatorCodes;

        $response = Http::acceptJson()
            ->connectTimeout(15)
            ->timeout(90)
            ->retry(3, 1000, null, false)
            ->get($url, [
                'source' => 2,
                'format' => 'json',
                'date' => "{$startYear}:{$endYear}",
                'per_page' => 20000,
            ]);

        $this->ensureSuccessful($response);

        $payload = $response->json();

        if (
            ! is_array($payload)
            || ! isset($payload[1])
            || ! is_array($payload[1])
        ) {
            throw new RuntimeException(
                'Format respons World Bank tidak valid.'
            );
        }

        return $payload[1];
    }

    private function latestValuesByCountry(
        array $rows
    ): array {
        $result = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $iso3 = strtoupper(
                trim(
                    (string) (
                        $row['countryiso3code'] ?? ''
                    )
                )
            );

            $indicatorCode = (string) data_get(
                $row,
                'indicator.id',
                ''
            );

            $year = (int) ($row['date'] ?? 0);
            $value = $row['value'] ?? null;

            if (
                $iso3 === ''
                || ! isset(self::INDICATORS[$indicatorCode])
                || $year <= 0
                || $value === null
                || ! is_numeric($value)
            ) {
                continue;
            }

            $key = self::INDICATORS[$indicatorCode];

            $existingYear = (int) (
                $result[$iso3][$key]['year'] ?? 0
            );

            if ($year <= $existingYear) {
                continue;
            }

            $result[$iso3][$key] = [
                'year' => $year,
                'value' => (float) $value,
            ];
        }

        return $result;
    }

    private function ensureSuccessful(
        Response $response
    ): void {
        if ($response->successful()) {
            return;
        }

        throw new RuntimeException(
            'World Bank API gagal merespons. HTTP status: '
            .$response->status()
        );
    }
}