<?php

namespace App\Services;

use App\Models\Country;

class DelayPredictionService
{
    /**
     * Menghasilkan estimasi operasional yang transparan, bukan klaim ML.
     * Nilai historis pelabuhan menjadi baseline dan skor risiko menambahkan
     * tekanan keterlambatan berdasarkan kondisi negara terbaru.
     */
    public function predict(Country $country, array $risk): array
    {
        $portQuery = $country->ports()
            ->whereNotNull('delay_days');

        $portSampleSize = (clone $portQuery)->count();
        $historicalAverage = $portSampleSize > 0
            ? (float) (clone $portQuery)->avg('delay_days')
            : null;

        $totalScore = (int) ($risk['total_score'] ?? 50);
        $riskPressureDays = match (true) {
            $totalScore >= 80 => 6,
            $totalScore >= 60 => 4,
            $totalScore >= 40 => 2,
            $totalScore >= 20 => 1,
            default => 0,
        };

        $baselineDays = $historicalAverage
            ?? match (true) {
                $totalScore >= 80 => 5,
                $totalScore >= 60 => 3,
                $totalScore >= 40 => 2,
                default => 1,
            };

        $expectedDays = (int) round(min(30, max(
            0,
            $baselineDays + $riskPressureDays
        )));

        $margin = match (true) {
            $portSampleSize >= 10 => 1,
            $portSampleSize >= 3 => 2,
            default => 3,
        };

        $minimumDays = max(0, $expectedDays - $margin);
        $maximumDays = min(30, $expectedDays + $margin);
        $completeness = (int) ($risk['data_completeness_percent'] ?? 0);

        $confidencePercent = min(95, max(25,
            (int) round(
                ($completeness * 0.65)
                + (min($portSampleSize, 10) * 3)
            )
        ));

        $level = match (true) {
            $expectedDays >= 8 => 'high',
            $expectedDays >= 4 => 'medium',
            default => 'low',
        };

        return [
            'expected_delay_days' => $expectedDays,
            'minimum_delay_days' => $minimumDays,
            'maximum_delay_days' => $maximumDays,
            'delay_range' => $minimumDays.'-'.$maximumDays.' days',
            'level' => $level,
            'confidence_percent' => $confidencePercent,
            'port_sample_size' => $portSampleSize,
            'historical_port_average_days' => $historicalAverage === null
                ? null
                : round($historicalAverage, 1),
            'dominant_factors' => $this->dominantFactors($risk),
            'method' => 'Explainable weighted delay estimation',
        ];
    }

    private function dominantFactors(array $risk): array
    {
        $labels = [
            'weather_risk' => 'Weather',
            'inflation_risk' => 'Inflation',
            'currency_risk' => 'Currency',
            'news_risk' => 'News sentiment',
            'port_risk' => 'Port conditions',
        ];

        $scores = array_filter(
            $risk['components'] ?? [],
            static fn ($score, $key): bool =>
                isset($labels[$key]) && is_numeric($score),
            ARRAY_FILTER_USE_BOTH
        );

        arsort($scores);

        return collect(array_slice($scores, 0, 3, true))
            ->map(fn ($score, $key) => [
                'component' => $key,
                'label' => $labels[$key],
                'score' => (int) $score,
            ])
            ->values()
            ->all();
    }
}
