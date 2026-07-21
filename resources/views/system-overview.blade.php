<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ app()->getLocale() === 'id' ? 'Cakupan Sistem' : 'System Overview' }} - SupplyGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/supplyguard-professional.css') }}">
    <style>
        body { background: #f3f7fc; color: #14233a; }
        .overview-hero { background: linear-gradient(135deg, #071a35, #155b83); border-radius: 28px; color: white; padding: 42px; }
        .stat-card, .content-panel { background: white; border: 1px solid #dfe8f3; border-radius: 20px; box-shadow: 0 12px 32px rgba(15, 35, 65, .07); }
        .stat-card { height: 100%; padding: 22px; }
        .stat-value { color: #0d5fd7; font-size: 2rem; font-weight: 800; }
        .content-panel { padding: 28px; }
        .status-ok { display: inline-flex; align-items: center; gap: 7px; color: #087a55; background: #e8faf2; border-radius: 999px; padding: 6px 11px; font-size: .78rem; font-weight: 800; }
        .weight-track { height: 9px; overflow: hidden; border-radius: 999px; background: #e7eef8; }
        .weight-fill { height: 100%; border-radius: inherit; background: linear-gradient(90deg, #1768df, #24b6cd); }
        .source-badge { border-radius: 999px; background: #eaf2ff; color: #1454aa; padding: 5px 9px; font-size: .74rem; font-weight: 800; }
        .feature-row { display: flex; justify-content: space-between; gap: 16px; padding: 13px 0; border-bottom: 1px solid #edf1f7; }
        .feature-row:last-child { border-bottom: 0; }
        @media (max-width: 767px) { .overview-hero { padding: 28px 22px; } .content-panel { padding: 20px; } }
    </style>
</head>
<body class="sg-user-sidebar-layout">
@include('user.partials.sidebar')
<nav class="navbar navbar-expand-lg navbar-dark sg-user-navbar">
    <div class="container py-2">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">SupplyGuard</a>
        <div class="d-flex gap-2 align-items-center">
            <a class="btn btn-outline-light btn-sm" href="{{ route('dashboard') }}">Dashboard</a>
            <a class="btn btn-outline-light btn-sm" href="{{ route('country.comparison') }}">
                {{ app()->getLocale() === 'id' ? 'Perbandingan' : 'Comparison' }}
            </a>
            @include('user.partials.language-selector')
        </div>
    </div>
</nav>

<main class="container py-4 py-lg-5">
    <section class="overview-hero mb-4">
        <span class="badge rounded-pill text-bg-info mb-3">PROJECT REQUIREMENT COVERAGE</span>
        <h1 class="display-6 fw-bold mb-3">
            {{ app()->getLocale() === 'id' ? 'Cakupan Sistem SupplyGuard' : 'SupplyGuard System Coverage' }}
        </h1>
        <p class="lead mb-0 opacity-75">
            {{ app()->getLocale() === 'id'
                ? 'Bukti visual fitur, data, sumber eksternal, dan metodologi analisis yang digunakan oleh aplikasi.'
                : 'Visual evidence of the features, data, external sources, and analysis methodology used by the application.' }}
        </p>
    </section>

    <section id="globalDataSummary" class="row g-3 mb-4" aria-label="Database statistics">
        @foreach ([
            ['countries', 'Countries & Territories'],
            ['ports', 'Port Records'],
            ['news', 'News Articles'],
            ['risk_scores', 'Stored Risk Scores'],
            ['sentiment_words', 'Active Sentiment Words'],
            ['users', 'Registered Users'],
        ] as [$key, $label])
            <div class="col-6 col-lg-4 col-xl-2">
                <article class="stat-card">
                    <div class="stat-value">{{ number_format($statistics[$key]) }}</div>
                    <div class="small text-secondary fw-semibold">{{ $label }}</div>
                </article>
            </div>
        @endforeach
    </section>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <section id="externalDataSources" class="content-panel h-100">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h4 fw-bold mb-1">External Data Sources</h2>
                        <p class="text-secondary mb-0">7 integrated sources supporting global analysis.</p>
                    </div>
                    <span class="status-ok">✓ ACTIVE</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Provider</th><th>Purpose</th><th>Type</th></tr></thead>
                        <tbody>
                        @foreach ($sources as $source)
                            <tr>
                                <td class="fw-bold">{{ $source['name'] }}</td>
                                <td class="text-secondary">{{ $source['purpose'] }}</td>
                                <td><span class="source-badge">{{ $source['type'] }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section id="weightedRiskModel" class="content-panel h-100">
                <h2 class="h4 fw-bold mb-1">Weighted Risk Model</h2>
                <p class="text-secondary">Five explainable components with a total weight of 100%.</p>
                @foreach ([
                    'Weather Risk' => 27,
                    'Inflation Risk' => 21,
                    'Currency Risk' => 18,
                    'News Sentiment Risk' => 22,
                    'Port Risk' => 12,
                ] as $component => $weight)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small fw-bold mb-1"><span>{{ $component }}</span><span>{{ $weight }}%</span></div>
                        <div class="weight-track"><div class="weight-fill" style="width: {{ $weight }}%"></div></div>
                    </div>
                @endforeach
                <div class="alert alert-primary mb-0">
                    Total score: <strong>0–100</strong> · Low · Moderate · High · Critical
                </div>
            </section>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <section class="content-panel h-100">
                <h2 class="h4 fw-bold mb-3">Feature Coverage</h2>
                @foreach ([
                    'Laravel full-stack application',
                    'Analytical user dashboard',
                    'Chart.js risk and currency charts',
                    'Leaflet global port map',
                    'Multi-country comparison',
                    'Authenticated personal watchlist',
                    'Admin dashboard and scheduled synchronization',
                    'Automatic multilingual user web',
                ] as $feature)
                    <div class="feature-row"><span>{{ $feature }}</span><span class="status-ok">✓ READY</span></div>
                @endforeach
            </section>
        </div>

        <div class="col-lg-6">
            <section id="intelligenceMethods" class="content-panel h-100">
                <h2 class="h4 fw-bold mb-3">Intelligence Methods</h2>
                <div class="feature-row">
                    <div><strong>Lexicon-based Sentiment</strong><div class="small text-secondary">Positive and negative weighted dictionary applied to global news.</div></div>
                    <span class="status-ok">✓ READY</span>
                </div>
                <div class="feature-row">
                    <div><strong>Delay Prediction</strong><div class="small text-secondary">Expected delay, min–max range, confidence, sample size, and dominant factors.</div></div>
                    <span class="status-ok">✓ READY</span>
                </div>
                <div class="feature-row">
                    <div><strong>Data Quality Control</strong><div class="small text-secondary">Unavailable components are excluded and the score is normalized by available weight.</div></div>
                    <span class="status-ok">✓ READY</span>
                </div>
                <div class="feature-row">
                    <div><strong>Automated Core Tests</strong><div class="small text-secondary">Risk API, prediction, authentication, watchlist, admin authorization, and translation.</div></div>
                    <span class="status-ok">✓ TESTED</span>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-primary mt-4">Run Country Analysis</a>
            </section>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
