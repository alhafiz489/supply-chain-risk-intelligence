<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\NewsCache;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\SentimentWord;
use App\Models\User;
use App\Models\Watchlist;
use App\Services\RiskScoringService;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $latestRiskIds = RiskScore::query()
            ->selectRaw('MAX(id)')
            ->groupBy('country_id');

        $dataQuality = Country::query()
            ->selectRaw(
                "
                COUNT(*) AS total_countries,
                SUM(
                    CASE
                        WHEN risk_data_status = 'ready'
                        THEN 1
                        ELSE 0
                    END
                ) AS ready_countries,
                SUM(
                    CASE
                        WHEN risk_data_status = 'partial'
                        THEN 1
                        ELSE 0
                    END
                ) AS partial_countries,
                SUM(
                    CASE
                        WHEN risk_data_status = 'unavailable'
                            OR risk_data_status IS NULL
                        THEN 1
                        ELSE 0
                    END
                ) AS unavailable_countries,
                AVG(
                    COALESCE(
                        data_completeness_percent,
                        0
                    )
                ) AS average_completeness
                "
            )
            ->first();

        $statistics = [
            'total_users' => User::query()
                ->where('role', 'user')
                ->count(),

            'active_users' => User::query()
                ->where('role', 'user')
                ->where('status', 'active')
                ->count(),

            'total_countries' => (int) (
                $dataQuality?->total_countries
                ?? Country::count()
            ),

            'total_ports' => Port::count(),
            'total_news' => NewsCache::count(),
            'total_risk_scores' => RiskScore::count(),
            'total_watchlists' => Watchlist::count(),

            'high_risk_countries' => RiskScore::query()
                ->whereIn('id', $latestRiskIds)
                ->where('total_score', '>=', 50)
                ->count(),

            'ready_countries' => (int) (
                $dataQuality?->ready_countries
                ?? 0
            ),

            'partial_countries' => (int) (
                $dataQuality?->partial_countries
                ?? 0
            ),

            'unavailable_countries' => (int) (
                $dataQuality?->unavailable_countries
                ?? 0
            ),

            'average_data_completeness' => round(
                (float) (
                    $dataQuality?->average_completeness
                    ?? 0
                ),
                1
            ),
        ];

        $latestUsers = User::query()
            ->latest()
            ->take(5)
            ->get();

        $latestRisks = RiskScore::query()
            ->with([
                'country:id,name,iso2,risk_data_status,data_completeness_percent',
            ])
            ->latest('id')
            ->take(5)
            ->get();

        return view(
            'admin.dashboard',
            compact(
                'statistics',
                'latestUsers',
                'latestRisks'
            )
        );
    }

    public function users(Request $request): View
{
    $search = trim((string) $request->query('search'));

    $users = User::query()
        ->when($search !== '', function ($query) use ($search) {
            $query->where(function ($userQuery) use ($search) {
                $userQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view(
        'admin.users.index',
        compact('users', 'search')
    );
}

public function updateUserStatus(
    Request $request,
    User $user
): RedirectResponse {
    $validated = $request->validate([
        'status' => [
            'required',
            'in:active,inactive',
        ],
    ]);

    if ($user->id === $request->user()->id) {
        return back()->with(
            'error',
            'Administrator tidak dapat menonaktifkan akun sendiri.'
        );
    }

    $user->update([
        'status' => $validated['status'],
    ]);

    return back()->with(
        'success',
        'Status pengguna berhasil diperbarui.'
    );
}

public function ports(Request $request): View
{
    $search = trim((string) $request->query('search'));
    $countryId = $request->integer('country_id') ?: null;
    $dataType = (string) $request->query('data_type');
    $coordinateStatus = (string) $request->query('coordinates');
    $perPage = (int) $request->query('per_page', 25);

    if (! in_array($dataType, ['manual', 'reference'], true)) {
        $dataType = '';
    }

    if (! in_array(
        $coordinateStatus,
        ['available', 'missing'],
        true
    )) {
        $coordinateStatus = '';
    }

    if (! in_array($perPage, [10, 25, 50, 100], true)) {
        $perPage = 25;
    }

    $ports = Port::query()
        ->select([
            'id',
            'country_id',
            'unlocode',
            'name',
            'city',
            'latitude',
            'longitude',
            'congestion_level',
            'delay_days',
            'source',
            'source_version',
            'data_status',
            'is_reference_active',
            'synced_at',
            'created_at',
        ])
        ->with([
            'country:id,name,iso2,iso3',
        ])
        ->when($search !== '', function ($query) use ($search) {
            $query->where(function ($portQuery) use ($search) {
                $portQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('unlocode', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%")
                    ->orWhereHas(
                        'country',
                        function ($countryQuery) use ($search) {
                            $countryQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('iso2', 'like', "%{$search}%")
                                ->orWhere('iso3', 'like', "%{$search}%");
                        }
                    );
            });
        })
        ->when(
            $countryId !== null,
            fn ($query) => $query->where('country_id', $countryId)
        )
        ->when(
            $dataType === 'manual',
            fn ($query) => $query->whereNull('unlocode')
        )
        ->when(
            $dataType === 'reference',
            fn ($query) => $query->whereNotNull('unlocode')
        )
        ->when(
            $coordinateStatus === 'available',
            fn ($query) => $query
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
        )
        ->when(
            $coordinateStatus === 'missing',
            fn ($query) => $query->where(function ($coordinateQuery) {
                $coordinateQuery
                    ->whereNull('latitude')
                    ->orWhereNull('longitude');
            })
        )
        ->orderBy('name')
        ->paginate($perPage)
        ->withQueryString();

    $countries = Country::query()
        ->select(['id', 'name', 'iso2'])
        ->orderBy('name')
        ->get();

    $portStatistics = [
        'total' => Port::count(),
        'manual' => Port::whereNull('unlocode')->count(),
        'reference' => Port::whereNotNull('unlocode')->count(),
        'with_coordinates' => Port::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count(),
    ];

    return view(
        'admin.ports.index',
        compact(
            'ports',
            'countries',
            'portStatistics',
            'search',
            'countryId',
            'dataType',
            'coordinateStatus',
            'perPage'
        )
    );
}

public function createPort(): View
{
    $countries = Country::query()
        ->select(['id', 'name', 'iso2'])
        ->orderBy('name')
        ->get();

    return view(
        'admin.ports.create',
        compact('countries')
    );
}

public function storePort(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'country_id' => [
            'required',
            'exists:countries,id',
        ],
        'name' => [
            'required',
            'string',
            'max:255',
        ],
        'city' => [
            'required',
            'string',
            'max:150',
        ],
        'latitude' => [
            'required',
            'numeric',
            'between:-90,90',
        ],
        'longitude' => [
            'required',
            'numeric',
            'between:-180,180',
        ],
        'congestion_level' => [
            'required',
            'in:Low,Medium,High',
        ],
        'delay_days' => [
            'required',
            'integer',
            'min:0',
            'max:365',
        ],
    ]);

    Port::create([
        ...$validated,
        'source' => 'Manual',
        'data_status' => 'operational',
        'is_reference_active' => true,
    ]);

    return redirect()
        ->route('admin.ports.index')
        ->with(
            'success',
            app()->getLocale() === 'id'
                ? 'Data operasional pelabuhan berhasil ditambahkan.'
                : 'Operational port data was added successfully.'
        );
}

public function editPort(Port $port): View|RedirectResponse
{
    if ($port->unlocode !== null) {
        return redirect()
            ->route('admin.ports.index')
            ->with(
                'error',
                app()->getLocale() === 'id'
                    ? 'Data referensi UN/LOCODE tidak dapat diedit secara manual.'
                    : 'UN/LOCODE reference data cannot be edited manually.'
            );
    }

    $countries = Country::query()
        ->select(['id', 'name', 'iso2'])
        ->orderBy('name')
        ->get();

    return view(
        'admin.ports.edit',
        compact('port', 'countries')
    );
}

public function updatePort(
    Request $request,
    Port $port
): RedirectResponse {
    if ($port->unlocode !== null) {
        return redirect()
            ->route('admin.ports.index')
            ->with(
                'error',
                app()->getLocale() === 'id'
                    ? 'Data referensi UN/LOCODE tidak dapat diperbarui secara manual.'
                    : 'UN/LOCODE reference data cannot be updated manually.'
            );
    }

    $validated = $request->validate([
        'country_id' => [
            'required',
            'exists:countries,id',
        ],
        'name' => [
            'required',
            'string',
            'max:255',
        ],
        'city' => [
            'required',
            'string',
            'max:150',
        ],
        'latitude' => [
            'required',
            'numeric',
            'between:-90,90',
        ],
        'longitude' => [
            'required',
            'numeric',
            'between:-180,180',
        ],
        'congestion_level' => [
            'required',
            'in:Low,Medium,High',
        ],
        'delay_days' => [
            'required',
            'integer',
            'min:0',
            'max:365',
        ],
    ]);

    $port->update([
        ...$validated,
        'source' => 'Manual',
        'data_status' => 'operational',
        'is_reference_active' => true,
    ]);

    return redirect()
        ->route('admin.ports.index')
        ->with(
            'success',
            app()->getLocale() === 'id'
                ? 'Data operasional pelabuhan berhasil diperbarui.'
                : 'Operational port data was updated successfully.'
        );
}

public function destroyPort(Port $port): RedirectResponse
{
    if ($port->unlocode !== null) {
        return redirect()
            ->route('admin.ports.index')
            ->with(
                'error',
                app()->getLocale() === 'id'
                    ? 'Data referensi UN/LOCODE tidak dapat dihapus secara manual.'
                    : 'UN/LOCODE reference data cannot be deleted manually.'
            );
    }

    $port->delete();

    return redirect()
        ->route('admin.ports.index')
        ->with(
            'success',
            app()->getLocale() === 'id'
                ? 'Data pelabuhan manual berhasil dihapus.'
                : 'Manual port data was deleted successfully.'
        );
}

public function news(Request $request): View
{
    $search = trim((string) $request->query('search'));

    $newsItems = NewsCache::with('country')
        ->when($search !== '', function ($query) use ($search) {
            $query->where(function ($newsQuery) use ($search) {
                $newsQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('sentiment', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhereHas(
                        'country',
                        function ($countryQuery) use ($search) {
                            $countryQuery->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
            });
        })
        ->orderByDesc('published_date')
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view(
        'admin.news.index',
        compact('newsItems', 'search')
    );
}

public function createNews(): View
{
    $countries = Country::query()
        ->orderBy('name')
        ->get();

    return view(
        'admin.news.create',
        compact('countries')
    );
}

public function storeNews(
    Request $request,
    SentimentAnalysisService $sentimentAnalysis
): RedirectResponse {
    $validated = $request->validate([
        'country_id' => [
            'required',
            'exists:countries,id',
        ],
        'title' => [
            'required',
            'string',
            'max:255',
        ],
        'category' => [
            'required',
            'string',
            'max:100',
        ],
        'sentiment' => [
            'nullable',
            'in:Auto,Positive,Neutral,Negative',
        ],
        'summary' => [
            'nullable',
            'string',
        ],
        'published_date' => [
            'nullable',
            'date',
        ],
    ]);

    $selectedSentiment = $validated['sentiment'] ?? 'Auto';

    if ($selectedSentiment === 'Auto') {
        $analysis = $sentimentAnalysis->analyze(
            $validated['title'].' '.($validated['summary'] ?? '')
        );

        $validated['sentiment'] = $analysis['sentiment'];
    }

    NewsCache::create($validated);

    return redirect()
        ->route('admin.news.index')
        ->with(
            'success',
            app()->getLocale() === 'id'
                ? 'Berita berhasil ditambahkan dan sentimen telah dianalisis.'
                : 'News added and sentiment analyzed successfully.'
        );
}

public function editNews(NewsCache $news): View
{
    $countries = Country::query()
        ->orderBy('name')
        ->get();

    return view(
        'admin.news.edit',
        compact('news', 'countries')
    );
}

public function updateNews(
    Request $request,
    NewsCache $news,
    SentimentAnalysisService $sentimentAnalysis
): RedirectResponse {
    $validated = $request->validate([
        'country_id' => [
            'required',
            'exists:countries,id',
        ],
        'title' => [
            'required',
            'string',
            'max:255',
        ],
        'category' => [
            'required',
            'string',
            'max:100',
        ],
        'sentiment' => [
            'nullable',
            'in:Auto,Positive,Neutral,Negative',
        ],
        'summary' => [
            'nullable',
            'string',
        ],
        'published_date' => [
            'nullable',
            'date',
        ],
    ]);

    $selectedSentiment = $validated['sentiment'] ?? 'Auto';

    if ($selectedSentiment === 'Auto') {
        $analysis = $sentimentAnalysis->analyze(
            $validated['title'].' '.($validated['summary'] ?? '')
        );

        $validated['sentiment'] = $analysis['sentiment'];
    }

    $news->update($validated);

    return redirect()
        ->route('admin.news.index')
        ->with(
            'success',
            app()->getLocale() === 'id'
                ? 'Berita berhasil diperbarui dan sentimen telah dianalisis.'
                : 'News updated and sentiment analyzed successfully.'
        );
}

public function destroyNews(NewsCache $news): RedirectResponse
{
    $news->delete();

    return redirect()
        ->route('admin.news.index')
        ->with(
            'success',
            app()->getLocale() === 'id'
                ? 'Berita berhasil dihapus.'
                : 'News deleted successfully.'
        );
}

public function sentimentWords(Request $request): View
{
    $search = trim((string) $request->query('search'));
    $type = (string) $request->query('type');
    $status = (string) $request->query('status');

    if (! in_array($type, ['positive', 'negative'], true)) {
        $type = '';
    }

    if (! in_array($status, ['active', 'inactive'], true)) {
        $status = '';
    }

    $words = SentimentWord::query()
        ->when($search !== '', function ($query) use ($search) {
            $query->where('word', 'like', "%{$search}%");
        })
        ->when($type !== '', function ($query) use ($type) {
            $query->where('type', $type);
        })
        ->when($status !== '', function ($query) use ($status) {
            $query->where(
                'is_active',
                $status === 'active'
            );
        })
        ->orderBy('type')
        ->orderBy('word')
        ->paginate(15)
        ->withQueryString();

    return view(
        'admin.sentiment-words.index',
        compact('words', 'search', 'type', 'status')
    );
}

public function createSentimentWord(): View
{
    return view('admin.sentiment-words.create');
}

public function storeSentimentWord(
    Request $request
): RedirectResponse {
    $validated = $request->validate([
        'word' => [
            'required',
            'string',
            'max:100',
            Rule::unique('sentiment_words')
                ->where(function ($query) use ($request) {
                    return $query->where(
                        'type',
                        $request->input('type')
                    );
                }),
        ],
        'type' => [
            'required',
            'in:positive,negative',
        ],
        'weight' => [
            'required',
            'integer',
            'min:1',
            'max:10',
        ],
        'is_active' => [
            'required',
            'boolean',
        ],
    ]);

    $validated['word'] = Str::lower(
        trim($validated['word'])
    );

    SentimentWord::create($validated);

    return redirect()
        ->route('admin.sentiment-words.index')
        ->with(
            'success',
            app()->getLocale() === 'id'
                ? 'Kata sentimen berhasil ditambahkan.'
                : 'Sentiment word added successfully.'
        );
}

public function editSentimentWord(
    SentimentWord $sentimentWord
): View {
    return view(
        'admin.sentiment-words.edit',
        compact('sentimentWord')
    );
}

public function updateSentimentWord(
    Request $request,
    SentimentWord $sentimentWord
): RedirectResponse {
    $validated = $request->validate([
        'word' => [
            'required',
            'string',
            'max:100',
            Rule::unique('sentiment_words')
                ->where(function ($query) use ($request) {
                    return $query->where(
                        'type',
                        $request->input('type')
                    );
                })
                ->ignore($sentimentWord->id),
        ],
        'type' => [
            'required',
            'in:positive,negative',
        ],
        'weight' => [
            'required',
            'integer',
            'min:1',
            'max:10',
        ],
        'is_active' => [
            'required',
            'boolean',
        ],
    ]);

    $validated['word'] = Str::lower(
        trim($validated['word'])
    );

    $sentimentWord->update($validated);

    return redirect()
        ->route('admin.sentiment-words.index')
        ->with(
            'success',
            app()->getLocale() === 'id'
                ? 'Kata sentimen berhasil diperbarui.'
                : 'Sentiment word updated successfully.'
        );
}

public function destroySentimentWord(
    SentimentWord $sentimentWord
): RedirectResponse {
    $sentimentWord->delete();

    return redirect()
        ->route('admin.sentiment-words.index')
        ->with(
            'success',
            app()->getLocale() === 'id'
                ? 'Kata sentimen berhasil dihapus.'
                : 'Sentiment word deleted successfully.'
        );
}

    public function recalculateRisks(
        RiskScoringService $riskScoring
    ): RedirectResponse {
        $result = $riskScoring->recalculateAll();

        if (
            $result['processed'] === 0
            && $result['failed'] > 0
        ) {
            return back()->with(
                'error',
                app()->getLocale() === 'id'
                    ? 'Perhitungan ulang risiko gagal dilakukan.'
                    : 'Risk recalculation failed.'
            );
        }

        $message = app()->getLocale() === 'id'
            ? "Risiko {$result['processed']} negara berhasil dihitung ulang."
            : "Risk scores for {$result['processed']} countries were recalculated.";

        if ($result['ready'] > 0) {
            $message .= app()->getLocale() === 'id'
                ? " {$result['ready']} negara memiliki data lengkap."
                : " {$result['ready']} countries have complete data.";
        }

        if ($result['partial'] > 0) {
            $message .= app()->getLocale() === 'id'
                ? " {$result['partial']} negara memakai data parsial dan ditandai sebagai skor sementara."
                : " {$result['partial']} countries use partial data and are marked as provisional.";
        }

        if ($result['skipped'] > 0) {
            $message .= app()->getLocale() === 'id'
                ? " {$result['skipped']} negara dilewati karena belum memiliki data yang dapat dinilai."
                : " {$result['skipped']} countries were skipped because no assessable data was available.";
        }

        if ($result['failed'] > 0) {
            $message .= app()->getLocale() === 'id'
                ? " {$result['failed']} negara gagal diproses."
                : " {$result['failed']} countries failed to process.";
        }

        return back()->with('success', $message);
    }

}