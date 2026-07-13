<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\RiskScore;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    public function show(Request $request)
    {
        $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ]);

        $country = Country::with(['ports', 'news'])
            ->findOrFail($request->country_id);

        $weatherRisk = $this->calculateWeatherRisk($country);
        $inflationRisk = $this->calculateInflationRisk(
            (float) $country->inflation_rate
        );
        $currencyRisk = $this->calculateCurrencyRisk(
            (float) $country->currency_volatility_percent
        );
        $newsRisk = $this->calculateNewsRisk($country);
        $portRisk = $this->calculatePortRisk($country);

        /*
        |--------------------------------------------------------------------------
        | Algoritma SupplyGuard Adaptive Risk Index
        |--------------------------------------------------------------------------
        |
        | Weather  = 27%
        | Inflation = 21%
        | Currency  = 18%
        | News      = 22%
        | Port      = 12%
        |
        */

        $totalScore = round(
            ($weatherRisk * 0.27) +
            ($inflationRisk * 0.21) +
            ($currencyRisk * 0.18) +
            ($newsRisk * 0.22) +
            ($portRisk * 0.12)
        );

        $riskLabel = $this->getRiskLabel($totalScore);
        $recommendation = $this->getRecommendation($totalScore);

        $savedRisk = RiskScore::create([
            'country_id' => $country->id,
            'weather_risk' => $weatherRisk,
            'inflation_risk' => $inflationRisk,
            'currency_risk' => $currencyRisk,
            'news_risk' => $newsRisk,
            'port_risk' => $portRisk,
            'total_score' => $totalScore,
            'risk_label' => $riskLabel,
            'recommendation' => $recommendation,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Risk score berhasil dihitung.',
            'data' => [
                'risk_score_id' => $savedRisk->id,

                'country' => [
                    'id' => $country->id,
                    'name' => $country->name,
                    'iso2' => $country->iso2,
                    'currency_code' => $country->currency_code,
                ],

                'components' => [
                    'weather_risk' => [
                        'score' => $weatherRisk,
                        'weight' => 27,
                    ],
                    'inflation_risk' => [
                        'score' => $inflationRisk,
                        'weight' => 21,
                    ],
                    'currency_risk' => [
                        'score' => $currencyRisk,
                        'weight' => 18,
                    ],
                    'news_risk' => [
                        'score' => $newsRisk,
                        'weight' => 22,
                    ],
                    'port_risk' => [
                        'score' => $portRisk,
                        'weight' => 12,
                    ],
                ],

                'total_score' => $totalScore,
                'risk_label' => $riskLabel,
                'recommendation' => $recommendation,

                'formula' => [
                    'weather' => '27%',
                    'inflation' => '21%',
                    'currency' => '18%',
                    'news' => '22%',
                    'port' => '12%',
                ],
            ],
        ]);
    }

    private function calculateWeatherRisk(Country $country): int
    {
        $rainfallRisk = min(
            40,
            round((float) $country->rainfall_mm * 1.6)
        );

        $windRisk = min(
            35,
            round((float) $country->wind_speed_kmh * 0.7)
        );

        $conditionRisk = match (
            strtolower((string) $country->weather_condition)
        ) {
            'storm', 'thunderstorm' => 25,
            'heavy rain' => 20,
            'rain' => 14,
            'windy' => 12,
            'cloudy' => 6,
            default => 2,
        };

        return min(
            100,
            $rainfallRisk + $windRisk + $conditionRisk
        );
    }

    private function calculateInflationRisk(float $inflation): int
    {
        /*
         * Setiap kenaikan inflasi 1% memberikan sekitar 8 poin risiko.
         */

        return min(
            100,
            max(5, round($inflation * 8))
        );
    }

    private function calculateCurrencyRisk(float $volatility): int
    {
        /*
         * Volatilitas mata uang memiliki pengaruh langsung
         * terhadap ketidakpastian biaya impor.
         */

        return min(
            100,
            max(5, round($volatility * 14))
        );
    }

    private function calculateNewsRisk(Country $country): int
    {
        if ($country->news->isEmpty()) {
            return 50;
        }

        $totalScore = 0;

        foreach ($country->news as $news) {
            $totalScore += match (
                strtolower((string) $news->sentiment)
            ) {
                'positive' => 20,
                'negative' => 85,
                default => 50,
            };
        }

        return min(
            100,
            round($totalScore / $country->news->count())
        );
    }

    private function calculatePortRisk(Country $country): int
    {
        if ($country->ports->isEmpty()) {
            return 55;
        }

        $totalScore = 0;

        foreach ($country->ports as $port) {
            $congestionScore = match (
                strtolower((string) $port->congestion_level)
            ) {
                'high' => 70,
                'medium' => 42,
                default => 15,
            };

            $delayScore = min(
                30,
                (int) $port->delay_days * 7
            );

            $totalScore += min(
                100,
                $congestionScore + $delayScore
            );
        }

        return min(
            100,
            round($totalScore / $country->ports->count())
        );
    }

    private function getRiskLabel(int $score): string
    {
        if ($score <= 24) {
            return 'Low Risk';
        }

        if ($score <= 49) {
            return 'Moderate Risk';
        }

        if ($score <= 74) {
            return 'High Risk';
        }

        return 'Critical Risk';
    }

    private function getRecommendation(int $score): string
    {
        if ($score <= 24) {
            return 'Shipment can proceed with normal monitoring.';
        }

        if ($score <= 49) {
            return 'Continue shipment with additional monitoring.';
        }

        if ($score <= 74) {
            return 'Review shipment schedule and prepare a backup plan.';
        }

        return 'Consider delaying shipment or selecting an alternative supplier.';
    }
}