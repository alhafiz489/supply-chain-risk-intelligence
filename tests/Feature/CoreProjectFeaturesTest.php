<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreProjectFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_risk_api_returns_components_and_delay_prediction(): void
    {
        $country = $this->country();

        Port::create([
            'country_id' => $country->id,
            'name' => 'Test International Port',
            'latitude' => -6.1,
            'longitude' => 106.8,
            'congestion_level' => 'High',
            'delay_days' => 5,
        ]);

        $response = $this->getJson('/api/risk?country_id='.$country->id);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'components' => [
                        'weather_risk',
                        'inflation_risk',
                        'currency_risk',
                        'news_risk',
                        'port_risk',
                    ],
                    'total_score',
                    'risk_label',
                    'recommendation',
                    'delay_prediction' => [
                        'expected_delay_days',
                        'minimum_delay_days',
                        'maximum_delay_days',
                        'confidence_percent',
                        'dominant_factors',
                        'method',
                    ],
                ],
            ]);
    }

    public function test_authenticated_user_can_manage_own_watchlist(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);
        $country = $this->country();

        RiskScore::create([
            'country_id' => $country->id,
            'weather_risk' => 20,
            'inflation_risk' => 30,
            'currency_risk' => 25,
            'news_risk' => 40,
            'port_risk' => 10,
            'total_score' => 27,
            'risk_label' => 'Low',
            'recommendation' => 'Monitor conditions.',
        ]);

        $this->actingAs($user)
            ->postJson('/watchlist', ['country_id' => $country->id])
            ->assertCreated()
            ->assertJsonPath('data.last_risk_score', 27);

        $this->actingAs($user)
            ->getJson('/watchlist/data')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->actingAs($user)
            ->deleteJson('/watchlist/'.$country->id)
            ->assertOk();

        $this->assertDatabaseMissing('watchlists', [
            'user_id' => $user->id,
            'country_id' => $country->id,
        ]);
    }

    public function test_admin_area_rejects_regular_user_and_accepts_admin(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertRedirect('/');

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk();
    }

    private function country(): Country
    {
        return Country::create([
            'name' => 'Indonesia',
            'iso2' => 'ID',
            'iso3' => 'IDN',
            'region' => 'Asia',
            'capital' => 'Jakarta',
            'currency_code' => 'IDR',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'gdp_usd_billion' => 1400,
            'inflation_rate' => 3.2,
            'population_million' => 280,
            'exchange_rate_to_idr' => 1,
            'currency_volatility_percent' => 1.5,
            'weather_condition' => 'Rain',
            'temperature' => 30,
            'rainfall_mm' => 22,
            'wind_speed_kmh' => 18,
            'master_synced_at' => now(),
            'weather_synced_at' => now(),
            'economic_synced_at' => now(),
            'currency_synced_at' => now(),
        ]);
    }
}
