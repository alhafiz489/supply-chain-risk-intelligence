<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_id' => [
                'nullable',
                'integer',
                'exists:countries,id',
            ],
            'currency_code' => [
                'nullable',
                'string',
                'size:3',
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
                'currency_code',
                'exchange_rate_to_idr',
                'currency_volatility_percent',
                'currency_rate_date',
                'currency_source',
                'currency_data_status',
                'currency_synced_at',
                'data_completeness_percent',
                'risk_data_status',
            ])
            ->orderBy('name');

        if (isset($validated['currency_code'])) {
            $query->where(
                'currency_code',
                strtoupper($validated['currency_code'])
            );
        }

        if (isset($validated['country_id'])) {
            $country = $query->findOrFail(
                $validated['country_id']
            );

            return response()->json([
                'success' => true,
                'message' =>
                    'Data kurs negara berhasil diambil.',
                'data' => $country,
            ]);
        }

        $perPage = (int) (
            $validated['per_page'] ?? 50
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Data kurs global berhasil diambil.',
            'data' => $query->paginate($perPage),
        ]);
    }
}