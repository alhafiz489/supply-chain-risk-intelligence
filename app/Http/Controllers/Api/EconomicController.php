<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EconomicController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_id' => [
                'nullable',
                'integer',
                'exists:countries,id',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $query = Country::query()
            ->select([
                'id',
                'name',
                'iso2',
                'iso3',
                'gdp_usd_billion',
                'gdp_data_year',
                'inflation_rate',
                'inflation_data_year',
                'population',
                'population_million',
                'population_data_year',
                'economic_synced_at',
                'data_completeness_percent',
                'risk_data_status',
            ])
            ->orderBy('name');

        if (isset($validated['country_id'])) {
            $country = $query->findOrFail(
                $validated['country_id']
            );

            return response()->json([
                'success' => true,
                'message' =>
                    'Data ekonomi negara berhasil diambil.',
                'data' => $country,
            ]);
        }

        $perPage = (int) (
            $validated['per_page'] ?? 50
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Data ekonomi global berhasil diambil.',
            'data' => $query->paginate($perPage),
        ]);
    }
}