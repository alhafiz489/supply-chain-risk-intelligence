<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use Illuminate\Http\JsonResponse;

class GlobalMapController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $data = (function (): array {
            $latestRisks = RiskScore::query()
                ->latest('id')
                ->get()
                ->unique('country_id')
                ->keyBy('country_id');

            $countries = Country::query()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('name')
                ->get([
                    'id', 'name', 'iso2', 'capital', 'region',
                    'currency_code', 'latitude', 'longitude',
                    'gdp_usd_billion', 'inflation_rate', 'population_million',
                    'weather_condition', 'temperature', 'rainfall_mm', 'wind_speed_kmh',
                    'data_completeness_percent', 'risk_data_status',
                ])
                ->map(function (Country $country) use ($latestRisks): array {
                    $risk = $latestRisks->get($country->id);

                    return [
                    'id' => $country->id,
                    'name' => $country->name,
                    'iso2' => $country->iso2,
                    'capital' => $country->capital,
                    'region' => $country->region,
                    'currency_code' => $country->currency_code,
                    'latitude' => (float) $country->latitude,
                    'longitude' => (float) $country->longitude,
                    'gdp_usd_billion' => $country->gdp_usd_billion,
                    'inflation_rate' => $country->inflation_rate,
                    'population_million' => $country->population_million,
                    'weather_condition' => $country->weather_condition,
                    'temperature' => $country->temperature,
                    'rainfall_mm' => $country->rainfall_mm,
                    'wind_speed_kmh' => $country->wind_speed_kmh,
                    'data_completeness_percent' => $country->data_completeness_percent,
                    'risk_data_status' => $country->risk_data_status,
                    'total_risk_score' => $risk?->total_score,
                    'risk_label' => $risk?->risk_label,
                    'recommendation' => $risk?->recommendation,
                ];
                })->all();

            $countryNames = collect($countries)->pluck('name', 'id');

            $ports = Port::query()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('id')
                ->get([
                    'id', 'country_id', 'name', 'city', 'unlocode',
                    'latitude', 'longitude', 'congestion_level',
                    'delay_days', 'data_status', 'source', 'source_version',
                    'status_code', 'function_code', 'subdivision_code',
                    'iata_code', 'is_reference_active',
                ])
                ->map(fn (Port $port): array => [
                    'id' => $port->id,
                    'country_id' => $port->country_id,
                    'country_name' => $countryNames->get($port->country_id),
                    'name' => $port->name,
                    'city' => $port->city,
                    'unlocode' => $port->unlocode,
                    'latitude' => (float) $port->latitude,
                    'longitude' => (float) $port->longitude,
                    'congestion_level' => $port->congestion_level,
                    'delay_days' => $port->delay_days,
                    'data_status' => $port->data_status,
                    'source' => $port->source,
                    'source_version' => $port->source_version,
                    'status_code' => $port->status_code,
                    'function_code' => $port->function_code,
                    'subdivision_code' => $port->subdivision_code,
                    'iata_code' => $port->iata_code,
                    'is_reference_active' => $port->is_reference_active,
                ])->all();

            return compact('countries', 'ports');
        })();

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'country_count' => count($data['countries']),
                'port_count' => count($data['ports']),
                'clustered' => true,
            ],
        ]);
    }
}
