<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'name' => 'Indonesia',
                'iso2' => 'ID',
                'region' => 'Southeast Asia',
                'capital' => 'Jakarta',
                'currency_code' => 'IDR',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'gdp_usd_billion' => 1371.00,
                'inflation_rate' => 2.80,
                'population_million' => 278.70,
                'exchange_rate_to_idr' => 1,
                'currency_volatility_percent' => 1.5,
                'weather_condition' => 'Rain',
                'temperature' => 30,
                'rainfall_mm' => 12,
                'wind_speed_kmh' => 18,
            ],
            [
                'name' => 'China',
                'iso2' => 'CN',
                'region' => 'East Asia',
                'capital' => 'Beijing',
                'currency_code' => 'CNY',
                'latitude' => 39.904200,
                'longitude' => 116.407400,
                'gdp_usd_billion' => 17700.00,
                'inflation_rate' => 1.20,
                'population_million' => 1410.00,
                'exchange_rate_to_idr' => 2200,
                'currency_volatility_percent' => 4.8,
                'weather_condition' => 'Cloudy',
                'temperature' => 24,
                'rainfall_mm' => 5,
                'wind_speed_kmh' => 22,
            ],
            [
                'name' => 'Germany',
                'iso2' => 'DE',
                'region' => 'Europe',
                'capital' => 'Berlin',
                'currency_code' => 'EUR',
                'latitude' => 52.520000,
                'longitude' => 13.405000,
                'gdp_usd_billion' => 4450.00,
                'inflation_rate' => 3.10,
                'population_million' => 83.20,
                'exchange_rate_to_idr' => 17500,
                'currency_volatility_percent' => 2.2,
                'weather_condition' => 'Clear',
                'temperature' => 18,
                'rainfall_mm' => 1,
                'wind_speed_kmh' => 12,
            ],
            [
                'name' => 'Australia',
                'iso2' => 'AU',
                'region' => 'Oceania',
                'capital' => 'Canberra',
                'currency_code' => 'AUD',
                'latitude' => -35.280900,
                'longitude' => 149.130000,
                'gdp_usd_billion' => 1690.00,
                'inflation_rate' => 4.10,
                'population_million' => 26.60,
                'exchange_rate_to_idr' => 10800,
                'currency_volatility_percent' => 3.4,
                'weather_condition' => 'Windy',
                'temperature' => 21,
                'rainfall_mm' => 2,
                'wind_speed_kmh' => 35,
            ],
            [
                'name' => 'Japan',
                'iso2' => 'JP',
                'region' => 'East Asia',
                'capital' => 'Tokyo',
                'currency_code' => 'JPY',
                'latitude' => 35.676200,
                'longitude' => 139.650300,
                'gdp_usd_billion' => 4210.00,
                'inflation_rate' => 2.90,
                'population_million' => 124.50,
                'exchange_rate_to_idr' => 105,
                'currency_volatility_percent' => 5.1,
                'weather_condition' => 'Storm',
                'temperature' => 26,
                'rainfall_mm' => 25,
                'wind_speed_kmh' => 45,
            ],
            [
                'name' => 'Singapore',
                'iso2' => 'SG',
                'region' => 'Southeast Asia',
                'capital' => 'Singapore',
                'currency_code' => 'SGD',
                'latitude' => 1.352100,
                'longitude' => 103.819800,
                'gdp_usd_billion' => 501.00,
                'inflation_rate' => 3.80,
                'population_million' => 5.90,
                'exchange_rate_to_idr' => 12100,
                'currency_volatility_percent' => 1.8,
                'weather_condition' => 'Clear',
                'temperature' => 31,
                'rainfall_mm' => 3,
                'wind_speed_kmh' => 15,
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['iso2' => $country['iso2']],
                $country
            );
        }
    }
}