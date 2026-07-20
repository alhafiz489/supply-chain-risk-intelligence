<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class OpenMeteoWeatherService
{
    private const ENDPOINT =
        'https://api.open-meteo.com/v1/forecast';

    private const BATCH_SIZE = 50;

    public function syncCountries(Collection $countries): array
    {
        $eligibleCountries = $countries
            ->filter(function (Country $country) {
                return $country->latitude !== null
                    && $country->longitude !== null;
            })
            ->values();

        $skipped = $countries->count()
            - $eligibleCountries->count();

        $processed = 0;
        $failed = 0;
        $updatedCountryIds = [];
        $failedCountries = [];

        foreach (
            $eligibleCountries->chunk(self::BATCH_SIZE)
            as $batch
        ) {
            try {
                $responses = $this->fetchBatch($batch);

                foreach (
                    $batch->values()
                    as $index => $country
                ) {
                    $weatherData = $responses[$index] ?? null;

                    if (! is_array($weatherData)) {
                        $failed++;
                        $failedCountries[] = $country->name;
                        continue;
                    }

                    $current = $weatherData['current'] ?? null;

                    if (! is_array($current)) {
                        $failed++;
                        $failedCountries[] = $country->name;
                        continue;
                    }

                    $weatherCode = (int) (
                        $current['weather_code'] ?? 0
                    );

                    $windSpeed = (float) (
                        $current['wind_speed_10m'] ?? 0
                    );

                    $country->fill([
                        'weather_condition' =>
                            $this->weatherCondition(
                                $weatherCode,
                                $windSpeed
                            ),
                        'temperature' => round(
                            (float) (
                                $current['temperature_2m'] ?? 0
                            ),
                            2
                        ),
                        'rainfall_mm' => round(
                            (float) (
                                $current['precipitation'] ?? 0
                            ),
                            2
                        ),
                        'wind_speed_kmh' => round(
                            $windSpeed,
                            2
                        ),
                        'weather_synced_at' => now(),
                        'data_completeness_percent' => max(
                            (int) $country
                                ->data_completeness_percent,
                            40
                        ),
                        'risk_data_status' =>
                            $country->risk_data_status === 'ready'
                                ? 'ready'
                                : 'partial',
                    ]);

                    $country->save();

                    $processed++;
                    $updatedCountryIds[] = $country->id;
                }
            } catch (Throwable $exception) {
                report($exception);

                $failed += $batch->count();

                foreach ($batch as $country) {
                    $failedCountries[] = $country->name;
                }
            }
        }

        return [
            'processed' => $processed,
            'failed' => $failed,
            'skipped' => $skipped,
            'updated_country_ids' => $updatedCountryIds,
            'failed_countries' => array_values(
                array_unique($failedCountries)
            ),
        ];
    }

    private function fetchBatch(Collection $countries): array
    {
        $latitudes = $countries
            ->pluck('latitude')
            ->map(fn ($value) => (string) (float) $value)
            ->implode(',');

        $longitudes = $countries
            ->pluck('longitude')
            ->map(fn ($value) => (string) (float) $value)
            ->implode(',');

        $response = Http::acceptJson()
            ->connectTimeout(10)
            ->timeout(30)
            ->retry(3, 500, null, false)
            ->get(self::ENDPOINT, [
                'latitude' => $latitudes,
                'longitude' => $longitudes,
                'current' => implode(',', [
                    'temperature_2m',
                    'precipitation',
                    'weather_code',
                    'wind_speed_10m',
                ]),
                'temperature_unit' => 'celsius',
                'wind_speed_unit' => 'kmh',
                'precipitation_unit' => 'mm',
                'timezone' => 'auto',
                'forecast_days' => 1,
            ]);

        $this->ensureSuccessful($response);

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RuntimeException(
                'Respons Open-Meteo tidak valid.'
            );
        }

        if (
            $countries->count() === 1
            && isset($payload['current'])
        ) {
            return [$payload];
        }

        return array_values($payload);
    }

    private function ensureSuccessful(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        throw new RuntimeException(
            'Open-Meteo gagal merespons. HTTP status: '
            .$response->status()
        );
    }

    private function weatherCondition(
        int $weatherCode,
        float $windSpeed
    ): string {
        $condition = match (true) {
            $weatherCode === 0 => 'Clear',
            in_array($weatherCode, [1, 2, 3], true) =>
                'Cloudy',
            in_array($weatherCode, [45, 48], true) =>
                'Fog',
            in_array(
                $weatherCode,
                [51, 53, 55, 56, 57],
                true
            ) => 'Drizzle',
            in_array(
                $weatherCode,
                [61, 63, 80, 81],
                true
            ) => 'Rain',
            in_array(
                $weatherCode,
                [65, 66, 67, 82],
                true
            ) => 'Heavy Rain',
            in_array(
                $weatherCode,
                [71, 73, 75, 77, 85, 86],
                true
            ) => 'Snow',
            in_array(
                $weatherCode,
                [95, 96, 99],
                true
            ) => 'Thunderstorm',
            default => 'Clear',
        };

        if (
            $windSpeed >= 50
            && ! in_array(
                $condition,
                ['Heavy Rain', 'Thunderstorm'],
                true
            )
        ) {
            return 'Windy';
        }

        return $condition;
    }
}