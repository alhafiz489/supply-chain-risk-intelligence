<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\SentimentWord;
use App\Models\NewsCache;
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

        $this->actingAs($admin)
            ->get('/admin/ports')
            ->assertOk()
            ->assertSee('css/supplyguard-professional.css');
    }

    public function test_admin_login_only_accepts_active_administrator(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
            'role' => 'user',
            'status' => 'active',
        ]);
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Administrator Login');

        $this->post('/admin/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_system_overview_displays_live_project_evidence(): void
    {
        $country = $this->country();

        Port::create([
            'country_id' => $country->id,
            'name' => 'Evidence Port',
            'latitude' => -6.1,
            'longitude' => 106.8,
            'congestion_level' => 'Low',
            'delay_days' => 1,
        ]);

        SentimentWord::create([
            'word' => 'stable',
            'type' => 'positive',
            'weight' => 1,
            'is_active' => true,
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/system-overview')
            ->assertOk()
            ->assertSee('SupplyGuard System Coverage')
            ->assertSee('REST Countries')
            ->assertSee('Weighted Risk Model')
            ->assertSee('Delay Prediction')
            ->assertSee('Automated Core Tests')
            ->assertSee('Port Records');
    }

    public function test_dashboard_contains_distinct_interactive_country_and_port_markers(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertSee('map-legend-country')
            ->assertSee('map-legend-port')
            ->assertSee('map-legend-selected-port')
            ->assertSee('countryMarker.bindPopup', false)
            ->assertSee('marker.bindPopup', false)
            ->assertSee('loadGlobalMap()', false)
            ->assertSee('addPortMarkersInBatches(ports)', false)
            ->assertSee('focusCountryOnGlobalMap(country, ports)', false)
            ->assertSee('selected-port-marker', false)
            ->assertSee('selectedPortList', false)
            ->assertSee('decodePortFunctions', false)
            ->assertSee('reference-data-notice', false)
            ->assertSee('noWrap: true', false)
            ->assertSee('maxBoundsViscosity: 1', false)
            ->assertSee('zoomSnap: 0.25', false)
            ->assertSee('maxHeight: 365', false)
            ->assertSee('keepInView: true', false)
            ->assertSee('globalMapSearchForm', false)
            ->assertSee('searchGlobalMap()', false)
            ->assertSee('zoomToShowLayer', false)
            ->assertSee('id="userSidebar"', false)
            ->assertSee('sg-user-utility-topbar', false)
            ->assertSee('Global Map')
            ->assertSee('#riskAnalysisSection', false)
            ->assertDontSee('#externalDataSources', false)
            ->assertSee("heroSection.insertAdjacentElement('afterend', globalMapSection)", false)
            ->assertSee('leaflet.markercluster', false)
            ->assertSee('Global Country and Port Map');
    }

    public function test_global_map_api_returns_all_country_and_port_points(): void
    {
        $country = $this->country();
        Port::create([
            'country_id' => $country->id,
            'name' => 'Global Test Port',
            'latitude' => -6.1,
            'longitude' => 106.8,
            'congestion_level' => 'Medium',
            'delay_days' => 2,
        ]);

        $this->getJson('/api/global-map')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.country_count', 1)
            ->assertJsonPath('meta.port_count', 1)
            ->assertJsonPath('meta.clustered', true)
            ->assertJsonPath('data.countries.0.name', 'Indonesia')
            ->assertJsonPath('data.countries.0.weather_condition', 'Rain')
            ->assertJsonPath('data.ports.0.name', 'Global Test Port')
            ->assertJsonPath('data.ports.0.country_name', 'Indonesia');
    }

    public function test_user_news_page_displays_searchable_sentiment_feed(): void
    {
        $country = $this->country();
        NewsCache::create([
            'country_id' => $country->id,
            'title' => 'Global shipping activity improves',
            'category' => 'Logistics',
            'sentiment' => 'Positive',
            'summary' => 'Port activity and trade conditions improved.',
            'source_name' => 'Test News',
            'published_at' => now(),
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/news?search=shipping&sentiment=Positive')
            ->assertOk()
            ->assertSee('Global Supply Chain News')
            ->assertSee('Global shipping activity improves')
            ->assertSee('Test News')
            ->assertSee('News');
    }

    public function test_user_data_catalogs_read_database_records_and_open_details(): void
    {
        $country = $this->country();
        $country->update(['region' => 'Asia', 'capital' => 'Jakarta']);
        $port = Port::create([
            'country_id' => $country->id,
            'name' => 'Tanjung Priok Test Port',
            'unlocode' => 'IDTPP',
            'city' => 'Jakarta',
            'latitude' => -6.10,
            'longitude' => 106.88,
            'congestion_level' => 'Medium',
            'delay_days' => 2,
            'source' => 'UN/LOCODE',
        ]);
        $word = SentimentWord::create([
            'word' => 'disruption',
            'type' => 'negative',
            'weight' => -3,
            'is_active' => true,
        ]);
        $news = NewsCache::create([
            'country_id' => $country->id,
            'title' => 'Port disruption test article',
            'sentiment' => 'Negative',
            'summary' => 'A detailed logistics summary.',
            'matched_words' => [['word' => 'disruption', 'weight' => -3]],
            'source_name' => 'Test Source',
            'url' => 'https://example.com/article',
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/data/countries?search=Indonesia&region=Asia')
            ->assertOk()->assertSee('Indonesia')->assertSee('Detail');
        $this->get(route('data.countries.show', $country))
            ->assertOk()->assertSee('Identity & Geography', false)->assertSee('Jakarta');

        $this->get('/data/ports?search=Priok&country_id='.$country->id)
            ->assertOk()->assertSee('Tanjung Priok Test Port')->assertSee('IDTPP');
        $this->get(route('data.ports.show', $port))
            ->assertOk()->assertSee('Operational Condition')->assertSee('2 days');

        $this->get('/data/sentiments?search=disruption&type=negative')
            ->assertOk()->assertSee('disruption')->assertSee('-3');
        $this->get(route('data.sentiments.show', $word))
            ->assertOk()->assertSee('Lexicon Information')->assertSee('Negative');

        $this->get(route('news.show', $news))
            ->assertOk()->assertSee('Port disruption test article')
            ->assertSee('A detailed logistics summary.')->assertSee('disruption');
    }

    public function test_risk_api_validates_country_and_does_not_create_history_on_get(): void
    {
        $this->getJson('/api/risk')->assertUnprocessable()
            ->assertJsonValidationErrors('country_id');
        $this->getJson('/api/risk?country_id=999999')->assertUnprocessable()
            ->assertJsonValidationErrors('country_id');

        $country = $this->country();
        $before = RiskScore::count();
        $this->getJson('/api/risk?country_id='.$country->id)->assertOk();
        $this->assertSame($before, RiskScore::count());
    }

    public function test_guests_cannot_mutate_watchlist_or_open_admin_area(): void
    {
        $country = $this->country();
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->postJson('/watchlist', ['country_id' => $country->id])
            ->assertUnauthorized();
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
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
