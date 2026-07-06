<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function show(Request $request)
    {
        $countryId = $request->query('country_id');

        if (!$countryId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter country_id wajib diisi.',
            ], 400);
        }

        $country = Country::find($countryId);

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data currency berhasil diambil.',
            'data' => [
                'country_name' => $country->name,
                'currency_code' => $country->currency_code,
                'exchange_rate_to_idr' => $country->exchange_rate_to_idr,
                'currency_volatility_percent' => $country->currency_volatility_percent,
                'trend' => [
                    [
                        'month' => 'Jan',
                        'rate' => round($country->exchange_rate_to_idr * 0.97, 2),
                    ],
                    [
                        'month' => 'Feb',
                        'rate' => round($country->exchange_rate_to_idr * 0.99, 2),
                    ],
                    [
                        'month' => 'Mar',
                        'rate' => round($country->exchange_rate_to_idr * 1.01, 2),
                    ],
                    [
                        'month' => 'Apr',
                        'rate' => round($country->exchange_rate_to_idr * 1.02, 2),
                    ],
                    [
                        'month' => 'May',
                        'rate' => round($country->exchange_rate_to_idr * 0.98, 2),
                    ],
                    [
                        'month' => 'Jun',
                        'rate' => round($country->exchange_rate_to_idr, 2),
                    ],
                ],
            ],
        ]);
    }
}