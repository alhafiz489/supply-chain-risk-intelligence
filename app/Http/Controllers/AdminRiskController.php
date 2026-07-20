<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Services\RiskScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRiskController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $riskLabel = trim((string) $request->query('risk_label'));
        $dateFrom = trim((string) $request->query('date_from'));
        $dateTo = trim((string) $request->query('date_to'));

        $allowedLabels = [
            'Low Risk',
            'Moderate Risk',
            'High Risk',
            'Critical Risk',
        ];

        if (! in_array($riskLabel, $allowedLabels, true)) {
            $riskLabel = '';
        }

        $riskScores = RiskScore::query()
            ->with('country')
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('country', function ($countryQuery) use ($search) {
                    $countryQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('iso2', 'like', "%{$search}%");
                });
            })
            ->when($riskLabel !== '', function ($query) use ($riskLabel) {
                $query->where('risk_label', $riskLabel);
            })
            ->when($dateFrom !== '', function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo !== '', function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $statistics = [
            'total' => RiskScore::count(),
            'low' => RiskScore::where('risk_label', 'Low Risk')->count(),
            'moderate' => RiskScore::where('risk_label', 'Moderate Risk')->count(),
            'high' => RiskScore::where('risk_label', 'High Risk')->count(),
            'critical' => RiskScore::where('risk_label', 'Critical Risk')->count(),
        ];

        return view(
            'admin.risks.index',
            compact(
                'riskScores',
                'statistics',
                'search',
                'riskLabel',
                'dateFrom',
                'dateTo'
            )
        );
    }

    public function recalculateCountry(
        Country $country,
        RiskScoringService $riskScoring
    ): RedirectResponse {
        $result = $riskScoring->calculateAndSave($country);

        return redirect()
            ->route('admin.risks.index')
            ->with(
                'success',
                app()->getLocale() === 'id'
                    ? "Risiko negara {$country->name} berhasil dihitung ulang dengan skor {$result['total_score']}."
                    : "The risk for {$country->name} was recalculated with a score of {$result['total_score']}."
            );
    }
}