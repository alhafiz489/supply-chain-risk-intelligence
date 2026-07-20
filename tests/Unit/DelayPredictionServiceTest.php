<?php

namespace Tests\Unit;

use App\Models\Country;
use App\Models\Port;
use App\Services\DelayPredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DelayPredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_prediction_uses_port_history_and_exposes_methodology(): void
    {
        $country = Country::create([
            'name' => 'Japan',
            'iso2' => 'JP',
            'currency_code' => 'JPY',
        ]);

        foreach ([2, 4, 6] as $delay) {
            Port::create([
                'country_id' => $country->id,
                'name' => 'Port '.$delay,
                'latitude' => 35,
                'longitude' => 139,
                'congestion_level' => 'Medium',
                'delay_days' => $delay,
            ]);
        }

        $prediction = app(DelayPredictionService::class)->predict(
            $country,
            [
                'total_score' => 65,
                'data_completeness_percent' => 100,
                'components' => [
                    'weather_risk' => 45,
                    'inflation_risk' => 30,
                    'currency_risk' => 40,
                    'news_risk' => 75,
                    'port_risk' => 65,
                ],
            ]
        );

        $this->assertSame(8, $prediction['expected_delay_days']);
        $this->assertSame(3, $prediction['port_sample_size']);
        $this->assertSame(4.0, $prediction['historical_port_average_days']);
        $this->assertSame('high', $prediction['level']);
        $this->assertSame('news_risk', $prediction['dominant_factors'][0]['component']);
        $this->assertSame('Explainable weighted delay estimation', $prediction['method']);
    }
}
