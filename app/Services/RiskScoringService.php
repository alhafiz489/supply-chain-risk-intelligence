<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\Watchlist;
use Illuminate\Support\Facades\DB;
use Throwable;

class RiskScoringService
{
    private const COMPONENT_WEIGHTS = [
        'weather_risk' => 27,
        'inflation_risk' => 21,
        'currency_risk' => 18,
        'news_risk' => 22,
        'port_risk' => 12,
    ];

    public function calculate(Country $country): array
    {
        $components = [
            'weather_risk' =>
                $this->calculateWeatherRisk($country),

            'inflation_risk' =>
                $this->calculateInflationRisk($country),

            'currency_risk' =>
                $this->calculateCurrencyRisk($country),

            'news_risk' =>
                $this->calculateNewsRisk($country),

            'port_risk' =>
                $this->calculatePortRisk($country),
        ];

        $availableWeight = 0;
        $weightedScore = 0;

        foreach ($components as $key => $component) {
            if (! $component['available']) {
                continue;
            }

            $weight = self::COMPONENT_WEIGHTS[$key];

            $availableWeight += $weight;
            $weightedScore += $component['score'] * $weight;
        }

        /*
        |--------------------------------------------------------------------------
        | Komponen yang tidak tersedia tidak dianggap aman
        |--------------------------------------------------------------------------
        |
        | Total hanya dinormalisasi dari komponen yang benar-benar tersedia.
        | Jika seluruh komponen kosong, skor netral 50 dikembalikan dan tidak
        | disimpan sebagai RiskScore.
        |
        */

        $totalScore = $availableWeight > 0
            ? (int) round($weightedScore / $availableWeight)
            : 50;

        $dataCompleteness = min(100, $availableWeight);

        $riskDataStatus = match (true) {
            $dataCompleteness === 100 => 'ready',
            $dataCompleteness > 0 => 'partial',
            default => 'unavailable',
        };

        $componentScores = [];
        $componentAvailability = [];
        $componentNotes = [];

        foreach ($components as $key => $component) {
            $componentScores[$key] = $component['score'];
            $componentAvailability[$key] = $component['available'];
            $componentNotes[$key] = $component['note'];
        }

        return [
            'country' => $country,
            'components' => $componentScores,
            'component_availability' => $componentAvailability,
            'component_notes' => $componentNotes,
            'total_score' => $totalScore,
            'risk_label' => $this->getRiskLabel($totalScore),
            'recommendation' => $this->getRecommendation(
                $totalScore,
                $riskDataStatus,
                $dataCompleteness
            ),
            'data_completeness_percent' => $dataCompleteness,
            'risk_data_status' => $riskDataStatus,
        ];
    }

    public function calculateAndSave(Country $country): array
    {
        $result = $this->calculate($country);

        $riskScore = DB::transaction(
            function () use ($country, $result) {
                $country->updateQuietly([
                    'data_completeness_percent' =>
                        $result['data_completeness_percent'],

                    'risk_data_status' =>
                        $result['risk_data_status'],
                ]);

                if (
                    $result['risk_data_status']
                    === 'unavailable'
                ) {
                    return null;
                }

                $riskScore = RiskScore::create([
                    'country_id' => $country->id,

                    'weather_risk' =>
                        $result['components']['weather_risk'],

                    'inflation_risk' =>
                        $result['components']['inflation_risk'],

                    'currency_risk' =>
                        $result['components']['currency_risk'],

                    'news_risk' =>
                        $result['components']['news_risk'],

                    'port_risk' =>
                        $result['components']['port_risk'],

                    'total_score' =>
                        $result['total_score'],

                    'risk_label' =>
                        $result['risk_label'],

                    'recommendation' =>
                        $result['recommendation'],
                ]);

                Watchlist::query()
                    ->where('country_id', $country->id)
                    ->update([
                        'last_risk_score' =>
                            $result['total_score'],

                        'last_risk_label' =>
                            $result['risk_label'],

                        'updated_at' => now(),
                    ]);

                return $riskScore;
            }
        );

        $result['risk_score'] = $riskScore;
        $result['saved'] = $riskScore !== null;

        return $result;
    }

    public function recalculateAll(): array
    {
        $processed = 0;
        $ready = 0;
        $partial = 0;
        $skipped = 0;
        $failed = 0;
        $failedCountries = [];

        Country::query()
            ->orderBy('id')
            ->chunkById(
                25,
                function ($countries) use (
                    &$processed,
                    &$ready,
                    &$partial,
                    &$skipped,
                    &$failed,
                    &$failedCountries
                ) {
                    foreach ($countries as $country) {
                        try {
                            $result =
                                $this->calculateAndSave($country);

                            if (! $result['saved']) {
                                $skipped++;
                                continue;
                            }

                            $processed++;

                            if (
                                $result['risk_data_status']
                                === 'ready'
                            ) {
                                $ready++;
                            } else {
                                $partial++;
                            }
                        } catch (Throwable $exception) {
                            report($exception);

                            $failed++;

                            $failedCountries[] = [
                                'id' => $country->id,
                                'name' => $country->name,
                            ];
                        }
                    }
                }
            );

        return [
            'processed' => $processed,
            'ready' => $ready,
            'partial' => $partial,
            'skipped' => $skipped,
            'failed' => $failed,
            'failed_countries' => $failedCountries,
        ];
    }

    private function calculateWeatherRisk(
        Country $country
    ): array {
        $available =
            $country->weather_synced_at !== null
            || $country->rainfall_mm !== null
            || $country->wind_speed_kmh !== null
            || filled($country->weather_condition);

        if (! $available) {
            return $this->unavailableComponent(
                'Data cuaca belum tersedia.'
            );
        }

        $rainfallRisk = min(
            40,
            (int) round(
                max(
                    0,
                    (float) ($country->rainfall_mm ?? 0)
                ) * 1.6
            )
        );

        $windRisk = min(
            35,
            (int) round(
                max(
                    0,
                    (float) ($country->wind_speed_kmh ?? 0)
                ) * 0.7
            )
        );

        $conditionRisk = match (
            strtolower(
                trim(
                    (string) $country->weather_condition
                )
            )
        ) {
            'storm',
            'thunderstorm' => 25,

            'heavy rain' => 20,
            'rain' => 14,
            'windy' => 12,
            'cloudy',
            'overcast' => 6,

            'clear',
            'mainly clear',
            'partly cloudy' => 2,

            default => 5,
        };

        return $this->availableComponent(
            min(
                100,
                max(
                    0,
                    $rainfallRisk
                    + $windRisk
                    + $conditionRisk
                )
            ),
            'Dihitung dari curah hujan, kecepatan angin, dan kondisi cuaca.'
        );
    }

    private function calculateInflationRisk(
        Country $country
    ): array {
        if ($country->inflation_rate === null) {
            return $this->unavailableComponent(
                'Data inflasi belum tersedia.'
            );
        }

        $inflation = (float) $country->inflation_rate;

        return $this->availableComponent(
            min(
                100,
                max(
                    0,
                    (int) round(
                        max(0, $inflation) * 8
                    )
                )
            ),
            'Dihitung dari tingkat inflasi terbaru.'
        );
    }

    private function calculateCurrencyRisk(
        Country $country
    ): array {
        if (
            $country->currency_volatility_percent
            === null
        ) {
            return $this->unavailableComponent(
                'Data volatilitas mata uang belum tersedia.'
            );
        }

        $volatility =
            (float) $country
                ->currency_volatility_percent;

        return $this->availableComponent(
            min(
                100,
                max(
                    0,
                    (int) round(
                        max(0, $volatility) * 14
                    )
                )
            ),
            'Dihitung dari volatilitas perubahan kurs harian.'
        );
    }

    private function calculateNewsRisk(
        Country $country
    ): array {
        $sentimentCounts = $country->news()
            ->whereNotNull('external_id')
            ->whereNotNull('published_at')
            ->where(
                'published_at',
                '>=',
                now()->subDays(30)
            )
            ->selectRaw(
                'LOWER(sentiment) AS sentiment_key, COUNT(*) AS total'
            )
            ->groupBy('sentiment_key')
            ->pluck('total', 'sentiment_key');

        $articleCount = (int) $sentimentCounts->sum();

        if ($articleCount === 0) {
            return $this->unavailableComponent(
                'Belum ada berita eksternal dalam 30 hari terakhir.'
            );
        }

        $positive = (int) (
            $sentimentCounts['positive'] ?? 0
        );

        $neutral = (int) (
            $sentimentCounts['neutral'] ?? 0
        );

        $negative = (int) (
            $sentimentCounts['negative'] ?? 0
        );

        $totalScore =
            ($positive * 20)
            + ($neutral * 50)
            + ($negative * 85);

        return $this->availableComponent(
            min(
                100,
                max(
                    0,
                    (int) round(
                        $totalScore / $articleCount
                    )
                )
            ),
            "Dihitung dari {$articleCount} berita eksternal dalam 30 hari terakhir."
        );
    }

    private function calculatePortRisk(
        Country $country
    ): array {
        /*
        |--------------------------------------------------------------------------
        | Hanya data operasional yang dinilai
        |--------------------------------------------------------------------------
        |
        | Record UN/LOCODE dengan data_status=reference_only tidak memiliki data
        | kongesti atau delay dan tidak boleh dianggap berisiko rendah.
        |
        */

        $ports = $country->ports()
            ->where(function ($query) {
                $query
                    ->whereNotNull('congestion_level')
                    ->orWhereNotNull('delay_days');
            })
            ->get([
                'congestion_level',
                'delay_days',
            ]);

        if ($ports->isEmpty()) {
            return $this->unavailableComponent(
                'Pelabuhan hanya memiliki data referensi lokasi; data kongesti dan delay belum tersedia.'
            );
        }

        $totalScore = 0;
        $assessedPorts = 0;

        foreach ($ports as $port) {
            $congestion = strtolower(
                trim(
                    (string) $port->congestion_level
                )
            );

            $congestionScore = match ($congestion) {
                'high' => 70,
                'medium' => 42,
                'low' => 15,
                default => 50,
            };

            $delayScore = $port->delay_days !== null
                ? min(
                    30,
                    max(
                        0,
                        (int) $port->delay_days
                    ) * 7
                )
                : 0;

            $totalScore += min(
                100,
                $congestionScore + $delayScore
            );

            $assessedPorts++;
        }

        return $this->availableComponent(
            min(
                100,
                max(
                    0,
                    (int) round(
                        $totalScore / $assessedPorts
                    )
                )
            ),
            "Dihitung dari {$assessedPorts} pelabuhan yang memiliki data operasional."
        );
    }

    private function availableComponent(
        int $score,
        string $note
    ): array {
        return [
            'score' => min(100, max(0, $score)),
            'available' => true,
            'note' => $note,
        ];
    }

    private function unavailableComponent(
        string $note
    ): array {
        return [
            /*
             * Nilai 50 hanya disimpan sebagai representasi netral pada tabel
             * RiskScore. Nilai ini tidak masuk ke total karena available=false.
             */
            'score' => 50,
            'available' => false,
            'note' => $note,
        ];
    }

    private function getRiskLabel(int $score): string
    {
        return match (true) {
            $score <= 24 => 'Low Risk',
            $score <= 49 => 'Moderate Risk',
            $score <= 74 => 'High Risk',
            default => 'Critical Risk',
        };
    }

    private function getRecommendation(
        int $score,
        string $dataStatus,
        int $dataCompleteness
    ): string {
        $recommendation = match (true) {
            $score <= 24 =>
                'Pengiriman dapat dilanjutkan dengan pemantauan normal.',

            $score <= 49 =>
                'Pengiriman dapat dilanjutkan dengan pemantauan tambahan.',

            $score <= 74 =>
                'Tinjau kembali jadwal pengiriman dan siapkan rencana cadangan.',

            default =>
                'Pertimbangkan penundaan pengiriman atau pemasok alternatif.',
        };

        if ($dataStatus === 'partial') {
            $recommendation .=
                " Skor ini bersifat sementara karena kelengkapan data baru {$dataCompleteness}%.";
        }

        return $recommendation;
    }
}