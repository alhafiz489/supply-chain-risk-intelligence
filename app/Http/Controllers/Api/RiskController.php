<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\RiskScore;
use App\Services\RiskScoringService;
use App\Services\DelayPredictionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    private const COMPONENT_WEIGHTS = [
        'weather_risk' => 27,
        'inflation_risk' => 21,
        'currency_risk' => 18,
        'news_risk' => 22,
        'port_risk' => 12,
    ];

    /**
     * Endpoint utama GET /api/risk.
     */
    public function index(
        Request $request,
        RiskScoringService $riskScoring,
        DelayPredictionService $delayPrediction
    ): JsonResponse {
        return $this->buildResponse(
            $request,
            $riskScoring,
            $delayPrediction
        );
    }

    /**
     * Dipertahankan untuk kompatibilitas dengan route lama
     * yang masih memanggil RiskController@show.
     */
    public function show(
        Request $request,
        RiskScoringService $riskScoring,
        DelayPredictionService $delayPrediction
    ): JsonResponse {
        return $this->buildResponse(
            $request,
            $riskScoring,
            $delayPrediction
        );
    }

    private function buildResponse(
        Request $request,
        RiskScoringService $riskScoring,
        DelayPredictionService $delayPrediction
    ): JsonResponse {
        $validated = $request->validate([
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],
        ]);

        $country = Country::query()
            ->findOrFail($validated['country_id']);

        /*
        |--------------------------------------------------------------------------
        | GET API hanya menghitung, tidak menyimpan riwayat baru
        |--------------------------------------------------------------------------
        |
        | Penyimpanan massal tetap dilakukan melalui tombol/perintah hitung ulang
        | admin. Membuka dashboard tidak akan menambah record risk_scores.
        |
        */

        $result = $riskScoring->calculate($country);
        $prediction = $delayPrediction->predict($country, $result);

        $components = [];

        foreach (self::COMPONENT_WEIGHTS as $key => $weight) {
            $components[$key] = [
                'score' => (int) (
                    $result['components'][$key]
                    ?? 50
                ),
                'weight' => $weight,
                'available' => (bool) (
                    $result['component_availability'][$key]
                    ?? false
                ),
                'note' => (string) (
                    $result['component_notes'][$key]
                    ?? ''
                ),
            ];
        }

        $latestRiskScoreId = RiskScore::query()
            ->where('country_id', $country->id)
            ->latest('id')
            ->value('id');

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'id'
                ? 'Risk score berhasil dihitung.'
                : 'Risk score calculated successfully.',

            'data' => [
                /*
                 * ID ini adalah riwayat terakhir yang sudah tersimpan,
                 * bukan record baru dari request GET ini.
                 */
                'risk_score_id' => $latestRiskScoreId,

                'country' => [
                    'id' => $country->id,
                    'name' => $country->name,
                    'iso2' => $country->iso2,
                    'iso3' => $country->iso3,
                    'currency_code' => $country->currency_code,
                ],

                'components' => $components,

                'component_availability' =>
                    $result['component_availability'],

                'component_notes' =>
                    $result['component_notes'],

                'total_score' =>
                    $result['total_score'],

                'risk_label' =>
                    $result['risk_label'],

                'recommendation' =>
                    $result['recommendation'],

                'delay_prediction' => $prediction,

                'data_completeness_percent' =>
                    $result['data_completeness_percent'],

                'risk_data_status' =>
                    $result['risk_data_status'],

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
}
