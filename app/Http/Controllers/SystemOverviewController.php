<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\NewsCache;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\SentimentWord;
use App\Models\User;
use Illuminate\View\View;

class SystemOverviewController extends Controller
{
    public function __invoke(): View
    {
        return view('system-overview', [
            'statistics' => [
                'countries' => Country::count(),
                'ports' => Port::count(),
                'news' => NewsCache::count(),
                'risk_scores' => RiskScore::count(),
                'sentiment_words' => SentimentWord::where('is_active', true)->count(),
                'users' => User::count(),
            ],
            'sources' => [
                ['name' => 'REST Countries', 'purpose' => 'Country master data, flags, languages, and currencies', 'type' => 'API'],
                ['name' => 'World Bank', 'purpose' => 'GDP, inflation, and population indicators', 'type' => 'API'],
                ['name' => 'Open-Meteo', 'purpose' => 'Weather and climate conditions', 'type' => 'API'],
                ['name' => 'Frankfurter', 'purpose' => 'Exchange rates and currency movement', 'type' => 'API'],
                ['name' => 'GNews', 'purpose' => 'Trade, economic, and logistics news', 'type' => 'API'],
                ['name' => 'UN/LOCODE', 'purpose' => 'Global port and location reference', 'type' => 'Dataset'],
                ['name' => 'OpenStreetMap', 'purpose' => 'Interactive global basemap', 'type' => 'Map'],
            ],
        ]);
    }
}
