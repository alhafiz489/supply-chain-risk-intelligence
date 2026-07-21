<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\SentimentWord;
use App\Models\NewsCache;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataCatalogController extends Controller
{
    public function country(Country $country): View
    {
        $country->loadCount(['ports', 'news', 'riskScores']);
        $country->load(['riskScores' => fn ($query) => $query->latest('id')->limit(1)]);
        return view('data.detail', ['type' => 'country', 'record' => $country]);
    }

    public function port(Port $port): View
    {
        $port->load('country');
        return view('data.detail', ['type' => 'port', 'record' => $port]);
    }

    public function sentiment(SentimentWord $sentimentWord): View
    {
        return view('data.detail', ['type' => 'sentiment', 'record' => $sentimentWord]);
    }

    public function news(NewsCache $newsCache): View
    {
        $newsCache->load('country');
        return view('data.detail', ['type' => 'news', 'record' => $newsCache]);
    }

    public function countries(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $region = trim((string) $request->query('region'));
        $countries = Country::query()->withCount('ports')
            ->when($search !== '', fn ($query) => $query->where(function ($nested) use ($search) {
                $nested->where('name', 'like', "%{$search}%")->orWhere('official_name', 'like', "%{$search}%")
                    ->orWhere('capital', 'like', "%{$search}%")->orWhere('iso2', 'like', "%{$search}%")->orWhere('iso3', 'like', "%{$search}%");
            }))->when($region !== '', fn ($query) => $query->where('region', $region))
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('data.countries', ['countries' => $countries, 'regions' => Country::whereNotNull('region')->distinct()->orderBy('region')->pluck('region'), 'total' => Country::count()]);
    }

    public function ports(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $countryId = $request->integer('country_id') ?: null;
        $ports = Port::query()->with('country:id,name,iso2')
            ->when($search !== '', fn ($query) => $query->where(function ($nested) use ($search) {
                $nested->where('name', 'like', "%{$search}%")->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('unlocode', 'like', "%{$search}%")->orWhere('iata_code', 'like', "%{$search}%");
            }))->when($countryId, fn ($query) => $query->where('country_id', $countryId))
            ->orderBy('name')->paginate(25)->withQueryString();

        return view('data.ports', ['ports' => $ports, 'countries' => Country::whereHas('ports')->orderBy('name')->get(['id', 'name']), 'total' => Port::count(), 'withCoordinates' => Port::whereNotNull('latitude')->whereNotNull('longitude')->count()]);
    }

    public function sentiments(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $type = trim((string) $request->query('type'));
        $words = SentimentWord::query()->when($search !== '', fn ($query) => $query->where('word', 'like', "%{$search}%"))
            ->when($type !== '', fn ($query) => $query->where('type', $type))->orderBy('type')->orderBy('word')->paginate(25)->withQueryString();

        return view('data.sentiments', ['words' => $words, 'total' => SentimentWord::count(), 'active' => SentimentWord::where('is_active', true)->count()]);
    }
}
