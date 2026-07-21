<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\NewsCache;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsPageController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'sentiment' => ['nullable', 'in:Positive,Neutral,Negative'],
        ]);

        $query = NewsCache::query()
            ->with('country:id,name,iso2')
            ->latest('published_at')
            ->latest('id');

        $query->when($filters['search'] ?? null, function ($builder, string $search): void {
            $builder->where(function ($nested) use ($search): void {
                $nested->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('source_name', 'like', "%{$search}%");
            });
        });

        $query->when($filters['country_id'] ?? null, fn ($builder, $countryId) =>
            $builder->where('country_id', $countryId));
        $query->when($filters['sentiment'] ?? null, fn ($builder, $sentiment) =>
            $builder->where('sentiment', $sentiment));

        return view('news', [
            'newsItems' => $query->paginate(12)->withQueryString(),
            'countries' => Country::query()->whereHas('news')
                ->orderBy('name')->get(['id', 'name', 'iso2']),
            'statistics' => [
                'total' => NewsCache::count(),
                'positive' => NewsCache::where('sentiment', 'Positive')->count(),
                'neutral' => NewsCache::where('sentiment', 'Neutral')->count(),
                'negative' => NewsCache::where('sentiment', 'Negative')->count(),
            ],
        ]);
    }
}
