<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.app_title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin=""
    >
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="{{ asset('css/supplyguard-professional.css') }}">
</head>

<body class="sg-user-body sg-user-sidebar-layout">
@include('user.partials.sidebar')
<nav class="navbar navbar-expand-lg navbar-dark sg-user-navbar">
    <div class="container-xxl">
        <a class="sg-brand navbar-brand" href="{{ route('dashboard') }}">
            <span class="sg-brand-mark">S</span>
            <span class="sg-brand-copy">
                <span class="sg-brand-title">SupplyGuard</span>
                <span class="sg-brand-caption">Risk Intelligence Platform</span>
            </span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#userMainNavigation"
                aria-controls="userMainNavigation" aria-expanded="false"
                aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="userMainNavigation">
            <ul class="navbar-nav ms-lg-4 me-auto sg-user-nav">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        {{ app()->getLocale() === 'id' ? 'Dashboard' : 'Dashboard' }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('country.comparison') }}"
                       class="nav-link {{ request()->routeIs('country.comparison') ? 'active' : '' }}">
                        {{ app()->getLocale() === 'id' ? 'Perbandingan Negara' : 'Country Comparison' }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('watchlist.index') }}"
                       class="nav-link {{ request()->routeIs('watchlist.*') ? 'active' : '' }}">
                        {{ app()->getLocale() === 'id' ? 'Daftar Favorit' : 'Favorite List' }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('system.overview') }}"
                       class="nav-link {{ request()->routeIs('system.overview') ? 'active' : '' }}">
                        {{ app()->getLocale() === 'id' ? 'Cakupan Sistem' : 'System Overview' }}
                    </a>
                </li>
            </ul>

            <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2">
                @auth
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="sg-nav-action text-decoration-none">
                            {{ app()->getLocale() === 'id' ? 'Panel Admin' : 'Admin Panel' }}
                        </a>
                    @endif
                @endauth

                @include('user.partials.language-selector')

                @auth
                    <div class="sg-user-chip">
                        <span class="sg-user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="small fw-semibold">{{ auth()->user()->name }}</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="sg-nav-action">
                            {{ app()->getLocale() === 'id' ? 'Keluar' : 'Logout' }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary px-4 rounded-3 fw-semibold">
                        {{ __('messages.login') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@if (session('language_success'))
    <div class="container-xxl mt-3">
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            {{ session('language_success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Tutup"
            ></button>
        </div>
    </div>
@endif


<main class="sg-page">
    <div class="container-xxl">
        <section class="sg-hero">
            <div class="sg-hero-grid">
                <div>
                    <span class="sg-eyebrow">
                        <span class="sg-eyebrow-dot"></span>
                        Supply Chain Intelligence
                    </span>

                    <h1 class="page-title">
                        {{ __('messages.app_title') }}
                    </h1>

                    <p class="page-subtitle">
                        {{ __('messages.app_subtitle') }}
                    </p>
                </div>

                <div class="sg-hero-status">
                    <div class="sg-hero-status-item">
                        <span class="sg-hero-status-icon">◎</span>
                        <div>
                            <div class="sg-hero-status-label">
                                {{ app()->getLocale() === 'id' ? 'Cakupan' : 'Coverage' }}
                            </div>
                            <div class="sg-hero-status-value">
                                {{ app()->getLocale() === 'id' ? '250 Negara & Wilayah' : '250 Countries & Territories' }}
                            </div>
                        </div>
                    </div>

                    <div class="sg-hero-status-item">
                        <span class="sg-hero-status-icon">↻</span>
                        <div>
                            <div class="sg-hero-status-label">
                                {{ app()->getLocale() === 'id' ? 'Pembaruan' : 'Updates' }}
                            </div>
                            <div class="sg-hero-status-value">
                                {{ app()->getLocale() === 'id' ? 'Data Terjadwal Otomatis' : 'Automated Scheduled Data' }}
                            </div>
                        </div>
                    </div>

                    <div class="sg-hero-status-item">
                        <span class="sg-hero-status-icon">◇</span>
                        <div>
                            <div class="sg-hero-status-label">
                                {{ app()->getLocale() === 'id' ? 'Analisis' : 'Analysis' }}
                            </div>
                            <div class="sg-hero-status-value">
                                {{ app()->getLocale() === 'id' ? '5 Komponen Risiko' : '5 Risk Components' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="card main-card">
            <div class="card-body p-4 p-xl-5">
<!-- Ringkasan utama -->
            <div class="row g-3">
                <div class="col-sm-6 col-xl">
                    <div class="metric-card">
                        <div class="metric-label">
                            {{ __('messages.gdp') }}
                        </div>

                        <h3 id="gdpValue" class="metric-value">-</h3>

                        <small id="gdpUnit" class="text-muted">
                            USD Billion
                        </small>
                    </div>
                </div>

                <div class="col-sm-6 col-xl">
                    <div class="metric-card">
                        <div class="metric-label">
                            {{ __('messages.inflation') }}
                        </div>

                        <h3 id="inflationValue" class="metric-value">-</h3>

                        <small class="text-muted">
                            {{ app()->getLocale() === 'id' ? 'Persen' : 'Percent' }}
                        </small>
                    </div>
                </div>

                <div class="col-sm-6 col-xl">
                    <div class="metric-card">
                        <div class="metric-label">
                            {{ __('messages.currency') }}
                        </div>

                        <h3 id="currencyValue" class="metric-value">-</h3>

                        <small id="currencyDetail" class="text-muted">
                            {{ app()->getLocale() === 'id' ? 'Nilai tukar' : 'Exchange rate' }}
                        </small>
                    </div>
                </div>

                <div class="col-sm-6 col-xl">
                    <div class="metric-card">
                        <div class="metric-label">
                            {{ __('messages.risk_score') }}
                        </div>

                        <h3 id="riskScoreValue" class="metric-value mb-2">-</h3>

                        <span
                            id="riskLabelValue"
                            class="risk-badge bg-secondary text-white"
                        >
                            -
                        </span>
                    </div>
                </div>

                <div class="col-sm-6 col-xl">
                    <div class="metric-card">
                        <div class="metric-label">
                            {{ app()->getLocale() === 'id'
                                ? 'Kelengkapan Data'
                                : 'Data Completeness'
                            }}
                        </div>

                        <h3 id="dataCompletenessValue" class="metric-value mb-2">
                            -
                        </h3>

                        <span
                            id="dataCompletenessStatus"
                            class="quality-badge quality-unavailable"
                        >
                            -
                        </span>
                    </div>
                </div>
            </div>

            <!-- Form analisis -->
            <section id="riskAnalysisSection" class="analysis-panel mt-4">
                <label
                    for="countrySelect"
                    class="form-label fw-semibold"
                >
                    {{ __('messages.select_country') }}
                </label>

                <div class="row g-3 align-items-end">
                    <div class="col-md-9 col-lg-10">
                        <select id="countrySelect" class="form-select">
                            <option value="">
                                {{ __('messages.select_country') }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 col-lg-2">
                        <button
                            id="analyzeButton"
                            type="button"
                            onclick="analyzeCountry()"
                            class="btn btn-primary analyze-button w-100"
                        >
                            {{ __('messages.analyze') }}
                        </button>
                    </div>
                </div>

                <div
                    id="statusMessage"
                    class="alert status-message mt-3 mb-0 d-none"
                    role="alert"
                ></div>
            </section>

            <!-- Status kelengkapan data -->
            <section
                id="dataQualityPanel"
                class="data-quality-panel mt-4 d-none"
                aria-live="polite"
            >
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                            <h5 class="fw-bold mb-0">
                                {{ app()->getLocale() === 'id'
                                    ? 'Status Kualitas Data'
                                    : 'Data Quality Status'
                                }}
                            </h5>

                            <span
                                id="dataQualityBadge"
                                class="quality-badge quality-unavailable"
                            >
                                -
                            </span>
                        </div>

                        <p id="dataQualityMessage" class="text-secondary mb-0">
                            -
                        </p>
                    </div>

                    <div class="text-lg-end">
                        <div class="small text-muted">
                            {{ app()->getLocale() === 'id'
                                ? 'Kelengkapan komponen'
                                : 'Component completeness'
                            }}
                        </div>

                        <div id="dataQualityPercent" class="fs-3 fw-bold">
                            -
                        </div>
                    </div>
                </div>
            </section>

            <!-- Rekomendasi -->
            <section
                id="delayPredictionBox"
                class="recommendation-box mt-4 d-none"
                aria-live="polite"
            >
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold mb-2">
                            {{ app()->getLocale() === 'id'
                                ? 'Prediksi Keterlambatan Pengiriman'
                                : 'Shipment Delay Prediction' }}
                        </h5>
                        <p id="delayPredictionSummary" class="mb-1 text-secondary">-</p>
                        <small id="delayPredictionFactors" class="text-muted">-</small>
                    </div>
                    <div class="text-lg-end">
                        <div id="delayPredictionDays" class="fs-3 fw-bold">-</div>
                        <span id="delayPredictionConfidence" class="quality-badge quality-unavailable">-</span>
                    </div>
                </div>
            </section>

            <section
                id="riskRecommendationBox"
                class="recommendation-box mt-4 d-none"
                aria-live="polite"
            >
                <div class="d-flex align-items-start gap-3">
                    <div class="fs-3">💡</div>

                    <div>
                        <h5
                            id="riskRecommendationTitle"
                            class="fw-bold mb-2"
                        >
                            -
                        </h5>

                        <p
                            id="riskRecommendationValue"
                            class="mb-0 text-secondary"
                        >
                            -
                        </p>
                    </div>
                </div>
            </section>

            <!-- Rincian komponen risiko -->
            <section
                id="riskComponentSection"
                class="component-section mt-4 d-none"
            >
                <div class="mb-3">
                    <h4 class="fw-bold mb-1">
                        {{ app()->getLocale() === 'id'
                            ? 'Rincian Komponen Risiko'
                            : 'Risk Component Details'
                        }}
                    </h4>

                    <p class="text-muted mb-0">
                        {{ app()->getLocale() === 'id'
                            ? 'Setiap komponen memiliki nilai 0 sampai 100 dan bobot perhitungan yang berbeda.'
                            : 'Each component has a score from 0 to 100 and a different calculation weight.'
                        }}
                    </p>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6 col-lg">
                        <div class="component-item">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">
                                    {{ app()->getLocale() === 'id' ? 'Cuaca' : 'Weather' }}
                                </span>
                                <span class="component-weight">27%</span>
                            </div>

                            <div id="weatherRiskValue" class="component-score mt-2">
                                0
                            </div>

                            <div class="progress mt-2">
                                <div
                                    id="weatherRiskBar"
                                    class="progress-bar"
                                    style="width: 0%"
                                ></div>
                            </div>

                            <div class="mt-2">
                                <span
                                    id="weatherRiskStatus"
                                    class="component-availability-badge component-unavailable"
                                >
                                    -
                                </span>
                            </div>

                            <div
                                id="weatherRiskNote"
                                class="component-note"
                            >
                                -
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg">
                        <div class="component-item">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">
                                    {{ app()->getLocale() === 'id' ? 'Inflasi' : 'Inflation' }}
                                </span>
                                <span class="component-weight">21%</span>
                            </div>

                            <div id="inflationRiskValue" class="component-score mt-2">
                                0
                            </div>

                            <div class="progress mt-2">
                                <div
                                    id="inflationRiskBar"
                                    class="progress-bar"
                                    style="width: 0%"
                                ></div>
                            </div>

                            <div class="mt-2">
                                <span
                                    id="inflationRiskStatus"
                                    class="component-availability-badge component-unavailable"
                                >
                                    -
                                </span>
                            </div>

                            <div
                                id="inflationRiskNote"
                                class="component-note"
                            >
                                -
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg">
                        <div class="component-item">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">
                                    {{ app()->getLocale() === 'id' ? 'Mata Uang' : 'Currency' }}
                                </span>
                                <span class="component-weight">18%</span>
                            </div>

                            <div id="currencyRiskValue" class="component-score mt-2">
                                0
                            </div>

                            <div class="progress mt-2">
                                <div
                                    id="currencyRiskBar"
                                    class="progress-bar"
                                    style="width: 0%"
                                ></div>
                            </div>

                            <div class="mt-2">
                                <span
                                    id="currencyRiskStatus"
                                    class="component-availability-badge component-unavailable"
                                >
                                    -
                                </span>
                            </div>

                            <div
                                id="currencyRiskNote"
                                class="component-note"
                            >
                                -
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg">
                        <div class="component-item">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">
                                    {{ app()->getLocale() === 'id' ? 'Berita' : 'News' }}
                                </span>
                                <span class="component-weight">22%</span>
                            </div>

                            <div id="newsRiskValue" class="component-score mt-2">
                                0
                            </div>

                            <div class="progress mt-2">
                                <div
                                    id="newsRiskBar"
                                    class="progress-bar"
                                    style="width: 0%"
                                ></div>
                            </div>

                            <div class="mt-2">
                                <span
                                    id="newsRiskStatus"
                                    class="component-availability-badge component-unavailable"
                                >
                                    -
                                </span>
                            </div>

                            <div
                                id="newsRiskNote"
                                class="component-note"
                            >
                                -
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg">
                        <div class="component-item">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">
                                    {{ app()->getLocale() === 'id' ? 'Pelabuhan' : 'Port' }}
                                </span>
                                <span class="component-weight">12%</span>
                            </div>

                            <div id="portRiskValue" class="component-score mt-2">
                                0
                            </div>

                            <div class="progress mt-2">
                                <div
                                    id="portRiskBar"
                                    class="progress-bar"
                                    style="width: 0%"
                                ></div>
                            </div>

                            <div class="mt-2">
                                <span
                                    id="portRiskStatus"
                                    class="component-availability-badge component-unavailable"
                                >
                                    -
                                </span>
                            </div>

                            <div
                                id="portRiskNote"
                                class="component-note"
                            >
                                -
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Grafik -->
            <section
                id="visualizationSection"
                class="visualization-section mt-4"
            >
                <div class="mb-4">
                    <h4 class="fw-bold mb-1">
                        {{ app()->getLocale() === 'id'
                            ? 'Visualisasi Analisis Negara'
                            : 'Country Analysis Visualization'
                        }}
                    </h4>

                    <p class="text-muted mb-0">
                        {{ app()->getLocale() === 'id'
                            ? 'Grafik menampilkan komponen risiko dan perubahan nilai tukar mata uang.'
                            : 'The charts display risk components and currency exchange rate movements.'
                        }}
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="visualization-card">
                            <h5 class="fw-bold mb-1">
                                {{ app()->getLocale() === 'id'
                                    ? 'Grafik Komponen Risiko'
                                    : 'Risk Component Chart'
                                }}
                            </h5>

                            <p class="text-muted small">
                                {{ app()->getLocale() === 'id'
                                    ? 'Perbandingan nilai risiko dari setiap indikator.'
                                    : 'Comparison of risk values for each indicator.'
                                }}
                            </p>

                            <div class="chart-container">
                                <canvas id="riskComponentChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="visualization-card">
                            <h5 class="fw-bold mb-1">
                                {{ app()->getLocale() === 'id'
                                    ? 'Tren Nilai Tukar'
                                    : 'Exchange Rate Trend'
                                }}
                            </h5>

                            <p
                                id="currencyChartDescription"
                                class="text-muted small"
                            >
                                -
                            </p>

                            <div
                                id="currencyChartEmpty"
                                class="alert alert-light border text-muted d-none mb-0"
                            >
                                {{ app()->getLocale() === 'id'
                                    ? 'Riwayat nilai tukar belum tersedia.'
                                    : 'Exchange-rate history is not available yet.'
                                }}
                            </div>

                            <div
                                id="currencyChartContainer"
                                class="chart-container"
                            >
                                <canvas id="currencyTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Peta pelabuhan -->
            <section
                id="portSection"
                class="visualization-section mt-4"
            >
                <div class="row g-4">
                    <div class="col-lg-8">
                        <h4 class="fw-bold mb-1">
                            {{ app()->getLocale() === 'id'
                                ? 'Peta Global Negara dan Pelabuhan'
                                : 'Global Country and Port Map'
                            }}
                        </h4>

                        <p class="text-muted">
                            {{ app()->getLocale() === 'id'
                                ? 'Semua negara dan pelabuhan ditampilkan. Klik marker atau cluster untuk melihat detail.'
                                : 'All countries and ports are shown. Click a marker or cluster to view details.'
                            }}
                        </p>

                        <div class="map-legend" aria-label="Map marker legend">
                            <span><i class="map-legend-dot map-legend-country"></i>{{ app()->getLocale() === 'id' ? 'Negara' : 'Country' }}</span>
                            <span><i class="map-legend-dot map-legend-port"></i>{{ app()->getLocale() === 'id' ? 'Pelabuhan' : 'Port' }}</span>
                            <span><i class="map-legend-dot map-legend-selected-port"></i>{{ app()->getLocale() === 'id' ? 'Pelabuhan Hasil Analisis' : 'Analyzed-country Port' }}</span>
                        </div>
                        <form id="globalMapSearchForm" class="global-map-search" role="search">
                            <div class="global-map-search-field">
                                <span aria-hidden="true">⌕</span>
                                <input
                                    id="globalMapSearchInput"
                                    type="search"
                                    autocomplete="off"
                                    placeholder="{{ app()->getLocale() === 'id' ? 'Cari negara, ibu kota, pelabuhan, kota, atau UN/LOCODE...' : 'Search country, capital, port, city, or UN/LOCODE...' }}"
                                    aria-label="{{ app()->getLocale() === 'id' ? 'Cari pada peta global' : 'Search the global map' }}"
                                >
                            </div>
                            <select id="globalMapSearchType" aria-label="{{ app()->getLocale() === 'id' ? 'Jenis pencarian' : 'Search type' }}">
                                <option value="all">{{ app()->getLocale() === 'id' ? 'Semua' : 'All' }}</option>
                                <option value="country">{{ app()->getLocale() === 'id' ? 'Negara' : 'Country' }}</option>
                                <option value="port">{{ app()->getLocale() === 'id' ? 'Pelabuhan' : 'Port' }}</option>
                            </select>
                            <button type="submit">
                                <span aria-hidden="true">⌕</span>
                                {{ app()->getLocale() === 'id' ? 'Cari' : 'Search' }}
                            </button>
                        </form>
                        <div id="globalMapSearchResults" class="global-map-search-results d-none" aria-live="polite"></div>
                        <div id="globalMapStatus" class="alert alert-info py-2 small">
                            {{ app()->getLocale() === 'id' ? 'Memuat peta global...' : 'Loading global map...' }}
                        </div>
                        <div id="portMap"></div>
                    </div>

                    <div class="col-lg-4">
                        <h5 class="fw-bold mb-3">
                            {{ app()->getLocale() === 'id'
                                ? 'Ringkasan Peta Global'
                                : 'Global Map Summary'
                            }}
                        </h5>

                        <div id="portInformationList">
                            <p class="text-muted">
                                {{ app()->getLocale() === 'id'
                                    ? 'Data negara dan pelabuhan sedang dimuat.'
                                    : 'Country and port data are loading.'
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    </div>
</main>


<footer class="sg-footer">
    <div class="container-xxl">
        <div class="sg-footer-inner">
            <div>
                <strong class="text-dark">SupplyGuard</strong>
                <span class="ms-2">
                    {{ app()->getLocale() === 'id'
                        ? 'Platform intelijen risiko rantai pasok global.'
                        : 'Global supply-chain risk intelligence platform.'
                    }}
                </span>
            </div>
            <div>© {{ date('Y') }} SupplyGuard</div>
        </div>
    </div>
</footer>

<!-- Leaflet JavaScript -->
<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    crossorigin=""
></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
    const currentLocale = @json(app()->getLocale());

    let riskComponentChartInstance = null;
    let currencyTrendChartInstance = null;
    let portMapInstance = null;
    let portMarkerGroup = null;
    let globalCountryLayer = null;
    let globalPortCluster = null;
    let globalMapLoaded = false;
    let selectedPortLayer = null;
    let globalMapCountries = [];
    const globalPortSearchItems = [];
    const globalCountryMarkers = new Map();
    const professionalPopupOptions = {
        maxWidth: 390,
        minWidth: 340,
        maxHeight: 365,
        autoPan: true,
        keepInView: true,
        autoPanPaddingTopLeft: [24, 24],
        autoPanPaddingBottomRight: [24, 24]
    };

    const text = {
        selectCountry: @json(__('messages.select_country')),

        loadingCountries: currentLocale === 'id'
            ? 'Memuat data negara...'
            : 'Loading country data...',

        calculating: currentLocale === 'id'
            ? 'Menghitung...'
            : 'Calculating...',

        chooseCountry: currentLocale === 'id'
            ? 'Pilih negara terlebih dahulu.'
            : 'Please select a country first.',

        countriesFailed: currentLocale === 'id'
            ? 'Data negara gagal dimuat.'
            : 'Failed to load country data.',

        analysisFailed: currentLocale === 'id'
            ? 'Data negara gagal dianalisis.'
            : 'Failed to analyze country data.',

        recommendationFor: currentLocale === 'id'
            ? 'Rekomendasi untuk'
            : 'Recommendation for',

        exchangeRate: currentLocale === 'id'
            ? 'Kurs ke IDR'
            : 'Exchange to IDR',

        dataReady: currentLocale === 'id'
            ? 'Data Lengkap'
            : 'Complete Data',

        dataPartial: currentLocale === 'id'
            ? 'Data Parsial'
            : 'Partial Data',

        dataUnavailable: currentLocale === 'id'
            ? 'Data Belum Tersedia'
            : 'Data Unavailable',

        componentAvailable: currentLocale === 'id'
            ? 'Tersedia'
            : 'Available',

        componentUnavailable: currentLocale === 'id'
            ? 'Tidak tersedia'
            : 'Unavailable',

        partialWarning: currentLocale === 'id'
            ? 'Skor bersifat sementara karena belum semua komponen memiliki data.'
            : 'This score is provisional because not all components have data.',

        readyMessage: currentLocale === 'id'
            ? 'Seluruh komponen risiko memiliki data yang dapat dinilai.'
            : 'All risk components have assessable data.',

        unavailableMessage: currentLocale === 'id'
            ? 'Belum ada komponen yang cukup untuk menghasilkan skor risiko yang dapat digunakan.'
            : 'There are not enough components to produce a usable risk score.'
    };

    document.addEventListener('DOMContentLoaded', function () {
        const globalMapSection = document.getElementById('portSection');
        const heroSection = document.querySelector('.sg-hero');

        if (globalMapSection && heroSection) {
            heroSection.insertAdjacentElement('afterend', globalMapSection);
        }

        loadCountries();
        initializeGlobalMapSearch();
        loadGlobalMap();
    });

    async function loadCountries() {
        const select = document.getElementById('countrySelect');

        select.disabled = true;
        select.innerHTML = '';

        const loadingOption = document.createElement('option');
        loadingOption.value = '';
        loadingOption.textContent = text.loadingCountries;

        select.appendChild(loadingOption);

        try {
            const response = await fetch('/api/countries', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || text.countriesFailed);
            }

            select.innerHTML = '';

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = text.selectCountry;

            select.appendChild(defaultOption);

            result.data.forEach(function (country) {
                const option = document.createElement('option');
                option.value = country.id;
                option.textContent = country.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error(error);
            showStatus(text.countriesFailed, 'danger');
        } finally {
            select.disabled = false;
        }
    }

    async function analyzeCountry() {
        const countrySelect = document.getElementById('countrySelect');
        const countryId = countrySelect.value;

        if (!countryId) {
            showStatus(text.chooseCountry, 'warning');
            countrySelect.focus();
            return;
        }

        const analyzeButton = document.getElementById('analyzeButton');
        const originalButtonText = analyzeButton.textContent.trim();

        analyzeButton.disabled = true;
        analyzeButton.textContent = text.calculating;
        hideStatus();

        try {
            const responses = await Promise.all([
                fetch(`/api/countries/${countryId}`, {
                    headers: { 'Accept': 'application/json' }
                }),
                fetch(`/api/currency?country_id=${countryId}`, {
                    headers: { 'Accept': 'application/json' }
                }),
                fetch(`/api/risk?country_id=${countryId}`, {
                    headers: { 'Accept': 'application/json' }
                }),
                fetch(
                    `/api/ports?country_id=${countryId}&has_coordinates=1&per_page=100`,
                    {
                        headers: { 'Accept': 'application/json' }
                    }
                )
            ]);

            const [
                countryResult,
                currencyResult,
                riskResult,
                portResult
            ] = await Promise.all(
                responses.map(function (response) {
                    return response.json();
                })
            );

            if (
                !responses.every(function (response) {
                    return response.ok;
                }) ||
                !countryResult.success ||
                !currencyResult.success ||
                !riskResult.success ||
                !portResult.success
            ) {
                throw new Error(text.analysisFailed);
            }

            const country = countryResult.data;
            const currency = currencyResult.data;
            const risk = riskResult.data;
            const ports = portResult.data;

            renderCountrySummary(country, currency, risk);
            renderDataQuality(risk, country);
            renderRiskRecommendation(country, risk);
            renderDelayPrediction(risk);
            renderRiskComponents(risk);

            document
                .getElementById('visualizationSection')
                .classList.remove('d-none');

            document
                .getElementById('portSection')
                .classList.remove('d-none');

            renderRiskComponentChart(risk);

            renderCurrencyTrendChart(
                currency.trend,
                currency.currency_code,
                country.name
            );

            focusCountryOnGlobalMap(country, ports);
        } catch (error) {
            console.error(error);
            showStatus(error.message || text.analysisFailed, 'danger');
        } finally {
            analyzeButton.disabled = false;
            analyzeButton.textContent = originalButtonText;
        }
    }

    function renderCountrySummary(country, currency, risk) {
        document.getElementById('gdpValue').textContent =
            formatNumber(country.gdp_usd_billion);

        document.getElementById('inflationValue').textContent =
            country.inflation_rate === null ||
            country.inflation_rate === undefined
                ? '-'
                : formatNumber(country.inflation_rate) + '%';

        document.getElementById('currencyValue').textContent =
            currency.currency_code || '-';

        document.getElementById('currencyDetail').textContent =
            text.exchangeRate + ': ' +
            formatNumber(currency.exchange_rate_to_idr);

        document.getElementById('riskScoreValue').textContent =
            Number.isFinite(Number(risk.total_score))
                ? Number(risk.total_score) + '/100'
                : '-';

        setRiskLabel(
            risk.risk_label || '-',
            Number(risk.total_score) || 0
        );
    }

    function renderDataQuality(risk, country) {
        const completeness = Math.max(
            0,
            Math.min(
                100,
                Number(
                    risk.data_completeness_percent
                    ?? risk.data_completeness
                    ?? country.data_completeness_percent
                    ?? 0
                ) || 0
            )
        );

        const status = String(
            risk.risk_data_status
            ?? risk.data_status
            ?? country.risk_data_status
            ?? (
                completeness >= 100
                    ? 'ready'
                    : completeness > 0
                        ? 'partial'
                        : 'unavailable'
            )
        ).toLowerCase();

        const panel = document.getElementById('dataQualityPanel');
        const badge = document.getElementById('dataQualityBadge');
        const metricBadge =
            document.getElementById('dataCompletenessStatus');

        let label = text.dataUnavailable;
        let message = text.unavailableMessage;
        let badgeClass = 'quality-unavailable';
        let panelClass = 'data-quality-unavailable';

        if (status === 'ready') {
            label = text.dataReady;
            message = text.readyMessage;
            badgeClass = 'quality-ready';
            panelClass = 'data-quality-ready';
        } else if (status === 'partial') {
            label = text.dataPartial;
            message = text.partialWarning;
            badgeClass = 'quality-partial';
            panelClass = 'data-quality-partial';
        }

        document.getElementById('dataCompletenessValue').textContent =
            completeness + '%';

        document.getElementById('dataQualityPercent').textContent =
            completeness + '%';

        document.getElementById('dataQualityMessage').textContent =
            message;

        [badge, metricBadge].forEach(function (element) {
            element.textContent = label;
            element.className =
                'quality-badge ' + badgeClass;
        });

        panel.className =
            'data-quality-panel mt-4 ' + panelClass;

        panel.classList.remove('d-none');
    }

    function renderRiskRecommendation(country, risk) {
        const recommendationBox =
            document.getElementById('riskRecommendationBox');

        document.getElementById('riskRecommendationTitle').textContent =
            text.recommendationFor + ' ' + country.name;

        document.getElementById('riskRecommendationValue').textContent =
            translateRecommendation(risk.recommendation);

        recommendationBox.classList.remove('d-none');
    }

    function renderDelayPrediction(risk) {
        const prediction = risk.delay_prediction;
        const box = document.getElementById('delayPredictionBox');

        if (!prediction) {
            box.classList.add('d-none');
            return;
        }

        const range = `${prediction.minimum_delay_days}-${prediction.maximum_delay_days}`;
        document.getElementById('delayPredictionDays').textContent =
            `${range} ${currentLocale === 'id' ? 'hari' : 'days'}`;

        document.getElementById('delayPredictionSummary').textContent =
            currentLocale === 'id'
                ? `Estimasi utama ${prediction.expected_delay_days} hari, berdasarkan ${prediction.port_sample_size} data pelabuhan dan tekanan risiko saat ini.`
                : `Central estimate ${prediction.expected_delay_days} days, based on ${prediction.port_sample_size} port records and current risk pressure.`;

        const factors = (prediction.dominant_factors || [])
            .map(item => `${item.label} (${item.score})`)
            .join(', ');
        document.getElementById('delayPredictionFactors').textContent =
            `${currentLocale === 'id' ? 'Faktor dominan' : 'Dominant factors'}: ${factors || '-'}`;

        const confidence = document.getElementById('delayPredictionConfidence');
        confidence.textContent = `${currentLocale === 'id' ? 'Keyakinan' : 'Confidence'} ${prediction.confidence_percent}%`;
        confidence.className = 'quality-badge ' + (
            prediction.confidence_percent >= 75
                ? 'quality-ready'
                : prediction.confidence_percent >= 50
                    ? 'quality-partial'
                    : 'quality-unavailable'
        );

        box.classList.remove('d-none');
    }

    function renderRiskComponents(risk) {
        const components = risk.components || {};

        const definitions = [
            {
                key: 'weather_risk',
                valueId: 'weatherRiskValue',
                barId: 'weatherRiskBar',
                statusId: 'weatherRiskStatus',
                noteId: 'weatherRiskNote'
            },
            {
                key: 'inflation_risk',
                valueId: 'inflationRiskValue',
                barId: 'inflationRiskBar',
                statusId: 'inflationRiskStatus',
                noteId: 'inflationRiskNote'
            },
            {
                key: 'currency_risk',
                valueId: 'currencyRiskValue',
                barId: 'currencyRiskBar',
                statusId: 'currencyRiskStatus',
                noteId: 'currencyRiskNote'
            },
            {
                key: 'news_risk',
                valueId: 'newsRiskValue',
                barId: 'newsRiskBar',
                statusId: 'newsRiskStatus',
                noteId: 'newsRiskNote'
            },
            {
                key: 'port_risk',
                valueId: 'portRiskValue',
                barId: 'portRiskBar',
                statusId: 'portRiskStatus',
                noteId: 'portRiskNote'
            }
        ];

        definitions.forEach(function (definition) {
            updateRiskComponent(
                definition,
                getComponentScore(components, definition.key),
                getComponentAvailability(risk, definition.key),
                getComponentNote(risk, definition.key)
            );
        });

        document
            .getElementById('riskComponentSection')
            .classList.remove('d-none');
    }

    function updateRiskComponent(
        definition,
        value,
        available,
        note
    ) {
        const valueElement =
            document.getElementById(definition.valueId);

        const barElement =
            document.getElementById(definition.barId);

        const statusElement =
            document.getElementById(definition.statusId);

        const noteElement =
            document.getElementById(definition.noteId);

        if (!available) {
            valueElement.textContent = 'N/A';
            valueElement.classList.add('text-muted');

            barElement.style.width = '0%';
            barElement.className =
                'progress-bar bg-secondary';

            statusElement.textContent =
                text.componentUnavailable;

            statusElement.className =
                'component-availability-badge component-unavailable';

            noteElement.textContent =
                note || text.componentUnavailable;

            return;
        }

        const normalizedValue = Math.max(
            0,
            Math.min(100, Number(value) || 0)
        );

        valueElement.textContent =
            normalizedValue + '/100';

        valueElement.classList.remove('text-muted');

        barElement.style.width =
            normalizedValue + '%';

        barElement.className =
            'progress-bar ' +
            getProgressColor(normalizedValue);

        statusElement.textContent =
            text.componentAvailable;

        statusElement.className =
            'component-availability-badge component-available';

        noteElement.textContent =
            note || text.componentAvailable;
    }

    function getComponentScore(components, key) {
        const component = components?.[key];

        if (
            component !== null
            && typeof component === 'object'
        ) {
            return Number(component.score) || 0;
        }

        return Number(component) || 0;
    }

    function getComponentAvailability(risk, key) {
        if (
            risk.component_availability
            && Object.prototype.hasOwnProperty.call(
                risk.component_availability,
                key
            )
        ) {
            return Boolean(
                risk.component_availability[key]
            );
        }

        const component = risk.components?.[key];

        if (
            component !== null
            && typeof component === 'object'
            && Object.prototype.hasOwnProperty.call(
                component,
                'available'
            )
        ) {
            return Boolean(component.available);
        }

        return component !== null
            && component !== undefined;
    }

    function getComponentNote(risk, key) {
        if (
            risk.component_notes
            && risk.component_notes[key]
        ) {
            return risk.component_notes[key];
        }

        const component = risk.components?.[key];

        if (
            component !== null
            && typeof component === 'object'
            && component.note
        ) {
            return component.note;
        }

        return '';
    }

    function renderRiskComponentChart(risk) {
        const chartElement =
            document.getElementById('riskComponentChart');

        const components = risk.components || {};

        if (!chartElement) {
            return;
        }

        const keys = [
            'weather_risk',
            'inflation_risk',
            'currency_risk',
            'news_risk',
            'port_risk'
        ];

        const labels = currentLocale === 'id'
            ? ['Cuaca', 'Inflasi', 'Mata Uang', 'Berita', 'Pelabuhan']
            : ['Weather', 'Inflation', 'Currency', 'News', 'Port'];

        const values = keys.map(function (key) {
            return getComponentAvailability(risk, key)
                ? getComponentScore(components, key)
                : null;
        });

        if (riskComponentChartInstance) {
            riskComponentChartInstance.destroy();
        }

        riskComponentChartInstance = new Chart(chartElement, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: currentLocale === 'id'
                            ? 'Nilai Risiko'
                            : 'Risk Score',
                        data: values,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.75)',
                            'rgba(245, 158, 11, 0.75)',
                            'rgba(139, 92, 246, 0.75)',
                            'rgba(239, 68, 68, 0.75)',
                            'rgba(15, 118, 110, 0.75)'
                        ],
                        borderColor: [
                            'rgb(59, 130, 246)',
                            'rgb(245, 158, 11)',
                            'rgb(139, 92, 246)',
                            'rgb(239, 68, 68)',
                            'rgb(15, 118, 110)'
                        ],
                        borderWidth: 1,
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.raw === null
                                    ? text.componentUnavailable
                                    : context.raw + '/100';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function renderCurrencyTrendChart(trend, currencyCode, countryName) {
        const chartElement =
            document.getElementById('currencyTrendChart');

        const chartContainer =
            document.getElementById('currencyChartContainer');

        const emptyState =
            document.getElementById('currencyChartEmpty');

        const description =
            document.getElementById('currencyChartDescription');

        const safeTrend = Array.isArray(trend)
            ? trend.filter(function (item) {
                return item
                    && item.rate !== null
                    && item.rate !== undefined
                    && Number.isFinite(Number(item.rate));
            })
            : [];

        if (
            !chartElement
            || !chartContainer
            || !emptyState
            || !description
        ) {
            return;
        }

        if (currencyTrendChartInstance) {
            currencyTrendChartInstance.destroy();
            currencyTrendChartInstance = null;
        }

        if (safeTrend.length === 0) {
            chartContainer.classList.add('d-none');
            emptyState.classList.remove('d-none');

            if (String(currencyCode || '').toUpperCase() === 'IDR') {
                description.textContent =
                    currentLocale === 'id'
                        ? 'IDR merupakan mata uang acuan, sehingga tidak memiliki perubahan terhadap IDR.'
                        : 'IDR is the reference currency, so it has no movement against IDR.';

                emptyState.textContent =
                    currentLocale === 'id'
                        ? 'Tidak ada grafik perubahan karena pasangan IDR/IDR selalu bernilai 1.'
                        : 'No movement chart is shown because the IDR/IDR pair is always equal to 1.';
            } else {
                description.textContent =
                    currentLocale === 'id'
                        ? `Riwayat ${currencyCode || '-'} terhadap IDR untuk ${countryName} belum tersedia.`
                        : `${currencyCode || '-'} exchange-rate history against IDR for ${countryName} is not available yet.`;

                emptyState.textContent =
                    currentLocale === 'id'
                        ? 'Data tren nilai tukar belum tersedia.'
                        : 'Exchange-rate trend data is not available yet.';
            }

            return;
        }

        chartContainer.classList.remove('d-none');
        emptyState.classList.add('d-none');

        const labels = safeTrend.map(function (item) {
            return item.month
                ?? item.date
                ?? item.rate_date
                ?? '-';
        });

        const values = safeTrend.map(function (item) {
            return Number(item.rate);
        });

        description.textContent =
            currentLocale === 'id'
                ? `Perubahan ${currencyCode} terhadap IDR untuk ${countryName}.`
                : `${currencyCode} exchange movement against IDR for ${countryName}.`;

        currencyTrendChartInstance = new Chart(chartElement, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: `${currencyCode} / IDR`,
                        data: values,
                        borderColor: 'rgb(37, 99, 235)',
                        backgroundColor: 'rgba(37, 99, 235, 0.12)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function (value) {
                                return formatNumber(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function initializeGlobalMapSearch() {
        const form = document.getElementById('globalMapSearchForm');
        const input = document.getElementById('globalMapSearchInput');
        const results = document.getElementById('globalMapSearchResults');

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            searchGlobalMap();
        });

        input.addEventListener('input', function () {
            if (input.value.trim().length === 0) {
                results.classList.add('d-none');
                results.innerHTML = '';
            }
        });
    }

    function normalizeMapSearchValue(value) {
        return String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }

    function searchGlobalMap() {
        const input = document.getElementById('globalMapSearchInput');
        const type = document.getElementById('globalMapSearchType').value;
        const resultsElement = document.getElementById('globalMapSearchResults');
        const query = normalizeMapSearchValue(input.value);

        if (query.length < 2) {
            resultsElement.classList.remove('d-none');
            resultsElement.innerHTML = `<div class="global-map-search-empty">${currentLocale === 'id' ? 'Masukkan minimal 2 karakter.' : 'Enter at least 2 characters.'}</div>`;
            return;
        }

        const results = [];

        if (type === 'all' || type === 'country') {
            globalMapCountries.forEach(function (country) {
                const fields = [country.name, country.iso2, country.capital, country.region]
                    .map(normalizeMapSearchValue);
                if (fields.some(field => field.includes(query))) {
                    results.push({
                        kind: 'country',
                        primary: country.name,
                        secondary: `${country.iso2 || '-'} · ${country.capital || '-'} · ${country.region || '-'}`,
                        country
                    });
                }
            });
        }

        if (type === 'all' || type === 'port') {
            globalPortSearchItems.forEach(function (item) {
                const fields = [item.port.name, item.port.city, item.port.unlocode, item.port.country_name]
                    .map(normalizeMapSearchValue);
                if (fields.some(field => field.includes(query))) {
                    results.push({
                        kind: 'port',
                        primary: item.port.name,
                        secondary: `${item.port.unlocode || '-'} · ${item.port.city || '-'} · ${item.port.country_name || '-'}`,
                        portItem: item
                    });
                }
            });
        }

        results.sort(function (a, b) {
            const aStarts = normalizeMapSearchValue(a.primary).startsWith(query) ? 0 : 1;
            const bStarts = normalizeMapSearchValue(b.primary).startsWith(query) ? 0 : 1;
            return aStarts - bStarts || a.primary.localeCompare(b.primary);
        });

        renderGlobalMapSearchResults(results.slice(0, 12), results.length, query);
    }

    function renderGlobalMapSearchResults(results, total, query) {
        const resultsElement = document.getElementById('globalMapSearchResults');
        resultsElement.classList.remove('d-none');
        resultsElement.innerHTML = '';

        if (results.length === 0) {
            resultsElement.innerHTML = `<div class="global-map-search-empty">${currentLocale === 'id' ? 'Lokasi tidak ditemukan.' : 'No location found.'}</div>`;
            return;
        }

        const heading = document.createElement('div');
        heading.className = 'global-map-search-heading';
        heading.textContent = currentLocale === 'id'
            ? `${total} hasil ditemukan · menampilkan ${results.length}`
            : `${total} results found · showing ${results.length}`;
        resultsElement.appendChild(heading);

        results.forEach(function (result) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'global-map-search-result';
            button.innerHTML = `
                <span class="global-map-result-icon ${result.kind}">${result.kind === 'country' ? '◎' : '⚓'}</span>
                <span><strong>${escapeHtml(result.primary)}</strong><small>${escapeHtml(result.secondary)}</small></span>
                <em>${result.kind === 'country' ? (currentLocale === 'id' ? 'Negara' : 'Country') : (currentLocale === 'id' ? 'Pelabuhan' : 'Port')}</em>
            `;
            button.addEventListener('click', function () {
                focusGlobalMapSearchResult(result);
                resultsElement.classList.add('d-none');
            });
            resultsElement.appendChild(button);
        });
    }

    function focusGlobalMapSearchResult(result) {
        document.getElementById('portMap').scrollIntoView({ behavior: 'smooth', block: 'center' });

        if (result.kind === 'country') {
            const marker = globalCountryMarkers.get(Number(result.country.id));
            portMapInstance.setView([Number(result.country.latitude), Number(result.country.longitude)], 6);
            if (marker) {
                marker.openPopup();
            }
            return;
        }

        const marker = result.portItem.marker;
        const showMarker = function () {
            portMapInstance.setView(marker.getLatLng(), 10);
            marker.openPopup();
        };

        if (globalPortCluster && typeof globalPortCluster.zoomToShowLayer === 'function') {
            globalPortCluster.zoomToShowLayer(marker, showMarker);
        } else {
            showMarker();
        }
    }

    async function loadGlobalMap() {
        const status = document.getElementById('globalMapStatus');
        const informationList = document.getElementById('portInformationList');

        try {
            const response = await fetch('/api/global-map', {
                headers: { 'Accept': 'application/json' }
            });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error('Global map data is unavailable.');
            }

            await renderGlobalMap(payload.data.countries, payload.data.ports);
            globalMapLoaded = true;

            status.className = 'alert alert-success py-2 small';
            status.textContent = currentLocale === 'id'
                ? `${payload.meta.country_count} negara dan ${payload.meta.port_count.toLocaleString()} pelabuhan ditampilkan.`
                : `${payload.meta.country_count} countries and ${payload.meta.port_count.toLocaleString()} ports displayed.`;

            informationList.innerHTML = `
                <div class="global-map-summary-card">
                    <strong>${payload.meta.country_count.toLocaleString()}</strong>
                    <span>${currentLocale === 'id' ? 'Negara dengan koordinat' : 'Countries with coordinates'}</span>
                </div>
                <div class="global-map-summary-card">
                    <strong>${payload.meta.port_count.toLocaleString()}</strong>
                    <span>${currentLocale === 'id' ? 'Pelabuhan dengan koordinat' : 'Ports with coordinates'}</span>
                </div>
                <div class="alert alert-light border small mb-0">
                    ${currentLocale === 'id'
                        ? 'Klik angka cluster untuk memperbesar area. Klik marker biru untuk detail negara dan marker oranye untuk detail pelabuhan.'
                        : 'Click a cluster count to zoom in. Click a blue marker for country details or an orange marker for port details.'}
                </div>
            `;
        } catch (error) {
            console.error(error);
            status.className = 'alert alert-danger py-2 small';
            status.textContent = currentLocale === 'id'
                ? 'Peta global gagal dimuat. Muat ulang halaman untuk mencoba lagi.'
                : 'The global map failed to load. Reload the page to try again.';
        }
    }

    function renderCountryMapPopup(country) {
        const riskScore = country.total_risk_score ?? '-';
        const riskLabel = country.risk_label || text.componentUnavailable;
        const completeness = Number(country.data_completeness_percent ?? 0);

        return `
            <article class="professional-map-popup country-detail-popup">
                <header class="professional-popup-header">
                    <div class="professional-popup-icon">${escapeHtml(country.iso2 || '🌐')}</div>
                    <div><small>${currentLocale === 'id' ? 'PROFIL NEGARA' : 'COUNTRY PROFILE'}</small><h3>${escapeHtml(country.name)}</h3></div>
                    <span class="professional-popup-status">${escapeHtml(country.risk_data_status || '-')}</span>
                </header>
                <div class="professional-popup-body">
                    <div class="popup-identity-line"><span>📍 ${escapeHtml(country.capital || '-')}</span><span>${escapeHtml(country.region || '-')}</span></div>
                    <section class="popup-section">
                        <h4>${currentLocale === 'id' ? 'Ekonomi & Populasi' : 'Economy & Population'}</h4>
                        <div class="popup-metric-grid">
                            <div><small>GDP</small><strong>${escapeHtml(formatNumber(country.gdp_usd_billion))}</strong><span>USD Billion</span></div>
                            <div><small>${currentLocale === 'id' ? 'Inflasi' : 'Inflation'}</small><strong>${escapeHtml(formatNumber(country.inflation_rate))}%</strong><span>${currentLocale === 'id' ? 'Tahunan' : 'Annual'}</span></div>
                            <div><small>${currentLocale === 'id' ? 'Populasi' : 'Population'}</small><strong>${escapeHtml(formatNumber(country.population_million))}</strong><span>Million</span></div>
                            <div><small>${currentLocale === 'id' ? 'Mata uang' : 'Currency'}</small><strong>${escapeHtml(country.currency_code || '-')}</strong><span>${escapeHtml(country.iso2 || '-')}</span></div>
                        </div>
                    </section>
                    <section class="popup-section">
                        <h4>${currentLocale === 'id' ? 'Cuaca Terkini' : 'Current Weather'}</h4>
                        <div class="popup-inline-stats">
                            <span>🌤 ${escapeHtml(country.weather_condition || '-')}</span>
                            <span>🌡 ${escapeHtml(formatNumber(country.temperature))}°C</span>
                            <span>🌧 ${escapeHtml(formatNumber(country.rainfall_mm))} mm</span>
                            <span>💨 ${escapeHtml(formatNumber(country.wind_speed_kmh))} km/h</span>
                        </div>
                    </section>
                    <section class="popup-risk-panel">
                        <div><small>${currentLocale === 'id' ? 'Skor Risiko Terakhir' : 'Latest Risk Score'}</small><strong>${escapeHtml(String(riskScore))}/100</strong></div>
                        <div><span class="popup-risk-label">${escapeHtml(riskLabel)}</span><small>${currentLocale === 'id' ? 'Kelengkapan' : 'Completeness'} ${completeness}%</small></div>
                    </section>
                    ${country.recommendation ? `<p class="popup-recommendation">💡 ${escapeHtml(country.recommendation)}</p>` : ''}
                </div>
            </article>
        `;
    }

    function renderPortMapPopup(port, selected = false) {
        const delayText = port.delay_days === null || port.delay_days === undefined
            ? text.componentUnavailable
            : `${Number(port.delay_days)} ${currentLocale === 'id' ? 'hari' : 'days'}`;
        const operational = port.data_status !== 'reference_only';
        const identityItems = [
            ['UN/LOCODE', port.unlocode],
            ['IATA', port.iata_code],
            [currentLocale === 'id' ? 'Subdivisi' : 'Subdivision', port.subdivision_code],
            [currentLocale === 'id' ? 'Status lokasi' : 'Location status', decodePortStatus(port.status_code)]
        ].filter(item => item[1] !== null && item[1] !== undefined && String(item[1]).trim() !== '');
        const identityHtml = identityItems.map(item => `
            <div><span>${escapeHtml(item[0])}</span><strong>${escapeHtml(item[1])}</strong></div>
        `).join('');
        const functionLabels = decodePortFunctions(port.function_code);

        return `
            <article class="professional-map-popup port-detail-popup ${selected ? 'selected-detail-popup' : ''}">
                <header class="professional-popup-header">
                    <div class="professional-popup-icon">⚓</div>
                    <div><small>${selected ? (currentLocale === 'id' ? 'PELABUHAN HASIL ANALISIS' : 'ANALYZED-COUNTRY PORT') : (currentLocale === 'id' ? 'PROFIL PELABUHAN' : 'PORT PROFILE')}</small><h3>${escapeHtml(port.name)}</h3></div>
                    <span class="professional-popup-status ${operational ? 'status-operational' : ''}">${operational ? (currentLocale === 'id' ? 'Operasional' : 'Operational') : (currentLocale === 'id' ? 'Referensi' : 'Reference')}</span>
                </header>
                <div class="professional-popup-body">
                    <div class="popup-identity-line"><span>📍 ${escapeHtml(port.city || '-')}</span><span>${escapeHtml(port.country_name || '-')}</span></div>
                    <section class="popup-section">
                        <h4>${currentLocale === 'id' ? 'Identitas Pelabuhan' : 'Port Identity'}</h4>
                        <div class="popup-detail-list">
                            ${identityHtml}
                        </div>
                    </section>
                    ${functionLabels.length > 0 ? `
                        <section class="popup-section">
                            <h4>${currentLocale === 'id' ? 'Fungsi Lokasi' : 'Location Functions'}</h4>
                            <div class="popup-function-tags">${functionLabels.map(label => `<span>${escapeHtml(label)}</span>`).join('')}</div>
                        </section>
                    ` : ''}
                    <section class="popup-section">
                        <h4>${currentLocale === 'id' ? 'Kondisi Operasional' : 'Operational Condition'}</h4>
                        ${operational ? `
                            <div class="popup-metric-grid popup-port-metrics">
                                <div><small>${currentLocale === 'id' ? 'Kemacetan' : 'Congestion'}</small><strong>${escapeHtml(translateCongestionLevel(port.congestion_level))}</strong></div>
                                <div><small>${currentLocale === 'id' ? 'Keterlambatan' : 'Delay'}</small><strong>${escapeHtml(delayText)}</strong></div>
                            </div>
                        ` : `
                            <div class="reference-data-notice">
                                <strong>${currentLocale === 'id' ? 'Data referensi lokasi' : 'Location reference data'}</strong>
                                <span>${currentLocale === 'id'
                                    ? 'UN/LOCODE tidak menyediakan data kemacetan atau keterlambatan waktu nyata untuk lokasi ini.'
                                    : 'UN/LOCODE does not provide real-time congestion or delay data for this location.'}</span>
                            </div>
                        `}
                    </section>
                    <div class="popup-coordinate-box"><span>${Number(port.latitude).toFixed(5)}, ${Number(port.longitude).toFixed(5)}</span><small>${currentLocale === 'id' ? 'Sumber' : 'Source'}: ${escapeHtml(port.source || 'UN/LOCODE')} ${escapeHtml(port.source_version || '')}</small></div>
                </div>
            </article>
        `;
    }

    function decodePortFunctions(code) {
        const normalized = String(code || '').padEnd(8, '-');
        const labels = currentLocale === 'id'
            ? ['Pelabuhan laut', 'Terminal kereta', 'Terminal jalan', 'Bandara', 'Kantor pos', 'Terminal multimoda', 'Transportasi tetap', 'Perbatasan']
            : ['Seaport', 'Rail terminal', 'Road terminal', 'Airport', 'Postal exchange', 'Multimodal terminal', 'Fixed transport', 'Border crossing'];

        return labels.filter((label, index) => normalized[index] !== '-');
    }

    function decodePortStatus(code) {
        const statuses = {
            AA: currentLocale === 'id' ? 'Disetujui otoritas nasional' : 'Approved by national authority',
            AC: currentLocale === 'id' ? 'Disetujui bea cukai' : 'Approved by customs',
            AF: currentLocale === 'id' ? 'Disetujui badan fasilitasi' : 'Approved by facilitation body',
            AI: currentLocale === 'id' ? 'Diadopsi organisasi internasional' : 'Adopted by international organization',
            AM: currentLocale === 'id' ? 'Disetujui secara resmi' : 'Officially approved',
            AS: currentLocale === 'id' ? 'Disetujui badan standardisasi' : 'Approved by standards body',
            RL: currentLocale === 'id' ? 'Lokasi diakui' : 'Recognized location',
            QQ: currentLocale === 'id' ? 'Belum diverifikasi' : 'Not yet verified',
            RQ: currentLocale === 'id' ? 'Permintaan perubahan' : 'Change requested'
        };

        return statuses[String(code || '').toUpperCase()] || code || null;
    }

    async function renderGlobalMap(countries, ports) {
        globalMapCountries = countries;
        globalPortSearchItems.length = 0;
        if (!portMapInstance) {
            portMapInstance = L.map('portMap', {
                worldCopyJump: false,
                preferCanvas: true,
                minZoom: 1.5,
                zoomSnap: 0.25,
                zoomDelta: 0.25,
                maxBounds: [[-85, -180], [85, 180]],
                maxBoundsViscosity: 1
            }).setView([8, 0], 1.5);

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    maxZoom: 19,
                    minZoom: 1,
                    noWrap: true,
                    bounds: [[-85, -180], [85, 180]],
                    attribution: '&copy; OpenStreetMap contributors'
                }
            ).addTo(portMapInstance);
        }

        globalCountryLayer = L.layerGroup().addTo(portMapInstance);
        globalPortCluster = typeof L.markerClusterGroup === 'function'
            ? L.markerClusterGroup({
                chunkedLoading: true,
                chunkInterval: 100,
                chunkDelay: 25,
                maxClusterRadius: 55,
                showCoverageOnHover: false,
                spiderfyOnMaxZoom: true
            })
            : L.layerGroup();

        globalCountryMarkers.clear();

        countries.forEach(function (country) {
            const marker = L.marker(
                [Number(country.latitude), Number(country.longitude)],
                {
                    icon: L.divIcon({
                        className: 'global-country-marker-shell',
                        html: `<span class="global-country-marker">${escapeHtml(country.iso2 || '●')}</span>`,
                        iconSize: [34, 34],
                        iconAnchor: [17, 17],
                        popupAnchor: [0, -18]
                    }),
                    zIndexOffset: 1000,
                    title: country.name
                }
            );

            marker.bindPopup(() => renderCountryMapPopup(country), professionalPopupOptions);

            marker.addTo(globalCountryLayer);
            globalCountryMarkers.set(Number(country.id), marker);
        });

        globalPortCluster.addTo(portMapInstance);

        await addPortMarkersInBatches(ports);

        L.control.layers(
            {},
            {
                [currentLocale === 'id' ? 'Negara' : 'Countries']: globalCountryLayer,
                [currentLocale === 'id' ? 'Pelabuhan' : 'Ports']: globalPortCluster
            },
            { collapsed: true }
        ).addTo(portMapInstance);

        setTimeout(function () {
            portMapInstance.invalidateSize();
            portMapInstance.setView([8, 0], 1.5);
        }, 250);
    }

    function addPortMarkersInBatches(ports) {
        const status = document.getElementById('globalMapStatus');
        const batchSize = 250;
        let index = 0;

        return new Promise(function (resolve) {
            function processBatch() {
                const markers = [];
                const end = Math.min(index + batchSize, ports.length);

                for (; index < end; index += 1) {
                    const port = ports[index];
                    const marker = L.marker(
                        [Number(port.latitude), Number(port.longitude)],
                        {
                            icon: L.divIcon({
                                className: 'global-port-marker-shell',
                                html: '<span class="global-port-marker"></span>',
                                iconSize: [18, 18],
                                iconAnchor: [9, 9],
                                popupAnchor: [0, -10]
                            }),
                            title: port.name
                        }
                    );

                    marker.bindPopup(() => renderPortMapPopup(port), professionalPopupOptions);
                    globalPortSearchItems.push({ port, marker });

                    markers.push(marker);
                }

                if (typeof globalPortCluster.addLayers === 'function') {
                    globalPortCluster.addLayers(markers);
                } else {
                    markers.forEach(marker => marker.addTo(globalPortCluster));
                }

                const percent = ports.length > 0
                    ? Math.round((index / ports.length) * 100)
                    : 100;
                status.textContent = currentLocale === 'id'
                    ? `Menyiapkan marker pelabuhan... ${percent}%`
                    : `Preparing port markers... ${percent}%`;

                if (index < ports.length) {
                    window.setTimeout(processBatch, 0);
                    return;
                }

                resolve();
            }

            processBatch();
        });
    }

    function focusCountryOnGlobalMap(country, ports) {
        if (!portMapInstance || !globalMapLoaded) {
            return;
        }

        const marker = globalCountryMarkers.get(Number(country.id));
        const latitude = Number(country.latitude);
        const longitude = Number(country.longitude);
        const informationList = document.getElementById('portInformationList');

        if (!selectedPortLayer) {
            selectedPortLayer = L.layerGroup().addTo(portMapInstance);
        }
        selectedPortLayer.clearLayers();

        const selectedCoordinates = [];
        const selectedMarkers = [];

        (Array.isArray(ports) ? ports : []).forEach(function (port, index) {
            port.country_name = port.country_name || country.name;
            const portLatitude = Number(port.latitude);
            const portLongitude = Number(port.longitude);

            if (!Number.isFinite(portLatitude) || !Number.isFinite(portLongitude)) {
                return;
            }

            const delayText = port.delay_days === null || port.delay_days === undefined
                ? text.componentUnavailable
                : `${Number(port.delay_days)} ${currentLocale === 'id' ? 'hari' : 'days'}`;
            const selectedMarker = L.marker(
                [portLatitude, portLongitude],
                {
                    icon: L.divIcon({
                        className: 'selected-port-marker-shell',
                        html: '<span class="selected-port-marker" aria-hidden="true">⚓</span>',
                        iconSize: [38, 38],
                        iconAnchor: [19, 19],
                        popupAnchor: [0, -21]
                    }),
                    zIndexOffset: 2000,
                    title: port.name
                }
            );

            selectedMarker.bindPopup(() => renderPortMapPopup(port, true), professionalPopupOptions);

            selectedMarker.addTo(selectedPortLayer);
            selectedMarkers.push({ marker: selectedMarker, port, index });
            selectedCoordinates.push([portLatitude, portLongitude]);
        });

        if (selectedMarkers.length > 0) {
            informationList.innerHTML = `
                <div class="selected-country-port-header">
                    <span>${currentLocale === 'id' ? 'Pelabuhan di' : 'Ports in'} <strong>${escapeHtml(country.name)}</strong></span>
                    <strong>${selectedMarkers.length}</strong>
                </div>
                <p class="small text-muted mb-3">
                    ${currentLocale === 'id'
                        ? 'Marker jangkar hijau adalah pelabuhan negara yang sedang dianalisis. Klik daftar untuk menemukannya di peta.'
                        : 'Green anchor markers are ports in the analyzed country. Click the list to locate one on the map.'}
                </p>
                <div id="selectedPortList" class="selected-port-list"></div>
            `;

            const selectedPortList = document.getElementById('selectedPortList');
            selectedMarkers.forEach(function (item, listIndex) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'selected-port-item';
                button.innerHTML = `
                    <span class="selected-port-item-icon">⚓</span>
                    <span>
                        <strong>${escapeHtml(item.port.name)}</strong>
                        <small>${escapeHtml(item.port.city || '-')}
                            · ${escapeHtml(item.port.unlocode || '-')}</small>
                        <em class="selected-port-data-type ${item.port.data_status === 'reference_only' ? 'is-reference' : 'is-operational'}">
                            ${item.port.data_status === 'reference_only'
                                ? (currentLocale === 'id' ? 'Referensi lokasi' : 'Location reference')
                                : (currentLocale === 'id' ? 'Data operasional' : 'Operational data')}
                        </em>
                    </span>
                `;
                button.addEventListener('click', function () {
                    portMapInstance.setView(item.marker.getLatLng(), 9);
                    item.marker.openPopup();
                });
                selectedPortList.appendChild(button);
            });
        } else {
            informationList.innerHTML = `
                <div class="alert alert-warning mb-0">
                    ${currentLocale === 'id'
                        ? `Belum ada pelabuhan berkoordinat untuk ${escapeHtml(country.name)}.`
                        : `No geocoded ports are available for ${escapeHtml(country.name)}.`}
                </div>
            `;
        }

        if (Number.isFinite(latitude) && Number.isFinite(longitude)) {
            selectedCoordinates.push([latitude, longitude]);
            if (selectedCoordinates.length > 1) {
                portMapInstance.fitBounds(selectedCoordinates, { padding: [45, 45], maxZoom: 7 });
            } else {
                portMapInstance.setView([latitude, longitude], 5);
            }
        }
    }

    function renderPortMap(ports, country, risk) {
        const mapElement =
            document.getElementById('portMap');

        const informationList =
            document.getElementById('portInformationList');

        if (!mapElement) {
            return;
        }

        if (!portMapInstance) {
            portMapInstance = L.map('portMap').setView(
                [
                    Number(country.latitude) || 0,
                    Number(country.longitude) || 0
                ],
                4
            );

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    maxZoom: 19,
                    attribution:
                        '&copy; OpenStreetMap contributors'
                }
            ).addTo(portMapInstance);

            portMarkerGroup =
                L.layerGroup().addTo(portMapInstance);
        }

        portMarkerGroup.clearLayers();
        informationList.innerHTML = '';

        const countryLatitude = Number(country.latitude);
        const countryLongitude = Number(country.longitude);
        const hasCountryCoordinates =
            Number.isFinite(countryLatitude)
            && Number.isFinite(countryLongitude);

        if (hasCountryCoordinates) {
            const countryMarker = L.circleMarker(
                [countryLatitude, countryLongitude],
                {
                    radius: 12,
                    color: '#ffffff',
                    weight: 3,
                    fillColor: '#1667d9',
                    fillOpacity: 1,
                    className: 'country-map-marker'
                }
            );

            countryMarker.bindPopup(`
                <div class="map-popup map-popup-country">
                    <strong>${escapeHtml(country.name)}</strong>
                    <span class="map-popup-type">${currentLocale === 'id' ? 'NEGARA' : 'COUNTRY'}</span>
                    <hr>
                    ${currentLocale === 'id' ? 'Ibu kota' : 'Capital'}:
                    <strong>${escapeHtml(country.capital || '-')}</strong><br>
                    ${currentLocale === 'id' ? 'Wilayah' : 'Region'}:
                    ${escapeHtml(country.region || '-')}<br>
                    ${currentLocale === 'id' ? 'Mata uang' : 'Currency'}:
                    ${escapeHtml(country.currency_code || '-')}<br>
                    GDP: ${escapeHtml(formatNumber(country.gdp_usd_billion))} USD Billion<br>
                    ${currentLocale === 'id' ? 'Inflasi' : 'Inflation'}:
                    ${escapeHtml(formatNumber(country.inflation_rate))}%<br>
                    ${currentLocale === 'id' ? 'Skor risiko' : 'Risk score'}:
                    <strong>${escapeHtml(String(risk.total_score ?? '-'))}/100</strong>
                </div>
            `);

            countryMarker.addTo(portMarkerGroup);
        }

        if (!Array.isArray(ports) || ports.length === 0) {
            informationList.innerHTML = `
                <div class="alert alert-warning mb-0">
                    ${
                        currentLocale === 'id'
                            ? 'Belum ada pelabuhan dengan koordinat untuk negara ini.'
                            : 'No ports with coordinates are available for this country.'
                    }
                </div>
            `;

            portMapInstance.setView(
                [
                    Number(country.latitude) || 0,
                    Number(country.longitude) || 0
                ],
                4
            );

            setTimeout(function () {
                portMapInstance.invalidateSize();
            }, 250);

            return;
        }

        const coordinates = [];

        ports.forEach(function (port) {
            const latitude = Number(port.latitude);
            const longitude = Number(port.longitude);

            if (
                !Number.isFinite(latitude)
                || !Number.isFinite(longitude)
            ) {
                return;
            }

            coordinates.push([latitude, longitude]);

            const translatedStatus =
                translateCongestionLevel(
                    port.congestion_level
                );

            const delayText =
                port.delay_days === null
                || port.delay_days === undefined
                    ? text.componentUnavailable
                    : `${Number(port.delay_days)} ${
                        currentLocale === 'id'
                            ? 'hari'
                            : 'days'
                    }`;

            const marker = L.circleMarker(
                [latitude, longitude],
                {
                    radius: 7,
                    color: '#ffffff',
                    weight: 2,
                    fillColor: '#f97316',
                    fillOpacity: 1,
                    className: 'port-map-marker'
                }
            );

            marker.bindPopup(`
                <div class="map-popup map-popup-port">
                <strong>${escapeHtml(port.name)}</strong>
                <span class="map-popup-type">${currentLocale === 'id' ? 'PELABUHAN' : 'PORT'}</span>
                <br>
                UN/LOCODE:
                ${escapeHtml(port.unlocode || '-')}
                <br>
                ${currentLocale === 'id' ? 'Kota' : 'City'}:
                ${escapeHtml(port.city || '-')}
                <br>
                ${currentLocale === 'id' ? 'Kemacetan' : 'Congestion'}:
                ${escapeHtml(translatedStatus)}
                <br>
                ${currentLocale === 'id' ? 'Keterlambatan' : 'Delay'}:
                ${escapeHtml(delayText)}
                <br>
                ${currentLocale === 'id' ? 'Koordinat' : 'Coordinates'}:
                ${latitude.toFixed(4)}, ${longitude.toFixed(4)}
                </div>
            `);

            marker.addTo(portMarkerGroup);

            informationList.insertAdjacentHTML(
                'beforeend',
                `
                    <div class="port-information mb-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    ${escapeHtml(port.name)}
                                </h6>

                                <p class="text-muted small mb-1">
                                    ${escapeHtml(port.city || '-')}
                                </p>

                                <code class="small">
                                    ${escapeHtml(port.unlocode || '-')}
                                </code>
                            </div>

                            <span class="port-status-badge ${getPortStatusClass(port.congestion_level)}">
                                ${escapeHtml(translatedStatus)}
                            </span>
                        </div>

                        <small class="text-muted d-block mt-2">
                            ${
                                currentLocale === 'id'
                                    ? 'Perkiraan keterlambatan'
                                    : 'Estimated delay'
                            }:
                            ${escapeHtml(delayText)}
                        </small>

                        ${
                            port.data_status === 'reference_only'
                                ? `
                                    <small class="text-muted d-block mt-1">
                                        ${
                                            currentLocale === 'id'
                                                ? 'Data referensi lokasi; kondisi operasional belum tersedia.'
                                                : 'Location reference data; operational conditions are unavailable.'
                                        }
                                    </small>
                                `
                                : ''
                        }
                    </div>
                `
            );
        });

        if (coordinates.length === 1) {
            portMapInstance.setView(
                coordinates[0],
                7
            );
        } else if (coordinates.length > 1) {
            portMapInstance.fitBounds(
                coordinates,
                {
                    padding: [35, 35]
                }
            );
        }

        setTimeout(function () {
            portMapInstance.invalidateSize();
        }, 250);
    }

    function setRiskLabel(label, score) {
        const badge = document.getElementById('riskLabelValue');

        badge.textContent = translateRiskLabel(label);
        badge.className = 'risk-badge';

        if (score <= 24) {
            badge.classList.add('risk-low');
        } else if (score <= 49) {
            badge.classList.add('risk-moderate');
        } else if (score <= 74) {
            badge.classList.add('risk-high');
        } else {
            badge.classList.add('risk-critical');
        }
    }

    function getProgressColor(score) {
        if (score <= 24) {
            return 'bg-success';
        }

        if (score <= 49) {
            return 'bg-warning';
        }

        if (score <= 74) {
            return 'bg-danger';
        }

        return 'bg-dark';
    }

    function getPortStatusClass(level) {
        const normalizedLevel =
            String(level || '').toLowerCase();

        if (normalizedLevel === 'high') {
            return 'port-status-high';
        }

        if (normalizedLevel === 'medium') {
            return 'port-status-medium';
        }

        if (normalizedLevel === 'low') {
            return 'port-status-low';
        }

        return 'port-status-unavailable';
    }

    function translateCongestionLevel(level) {
        const normalizedLevel =
            String(level || '').toLowerCase();

        if (currentLocale !== 'id') {
            const translations = {
                low: 'Low',
                medium: 'Medium',
                high: 'High'
            };

            return translations[normalizedLevel]
                || text.componentUnavailable;
        }

        const translations = {
            low: 'Rendah',
            medium: 'Sedang',
            high: 'Tinggi'
        };

        return translations[normalizedLevel]
            || text.componentUnavailable;
    }

    function translateRiskLabel(label) {
        if (currentLocale !== 'id') {
            return label;
        }

        const translations = {
            'Low Risk': 'Risiko Rendah',
            'Moderate Risk': 'Risiko Sedang',
            'High Risk': 'Risiko Tinggi',
            'Critical Risk': 'Risiko Kritis'
        };

        return translations[label] || label;
    }

    function translateRecommendation(recommendation) {
        if (currentLocale !== 'id') {
            return recommendation;
        }

        const translations = {
            'Shipment can proceed with normal monitoring.':
                'Pengiriman dapat dilanjutkan dengan pemantauan normal.',

            'Continue shipment with additional monitoring.':
                'Pengiriman dapat dilanjutkan dengan pemantauan tambahan.',

            'Review shipment schedule and prepare a backup plan.':
                'Tinjau kembali jadwal pengiriman dan siapkan rencana cadangan.',

            'Consider delaying shipment or selecting an alternative supplier.':
                'Pertimbangkan untuk menunda pengiriman atau memilih pemasok alternatif.'
        };

        return translations[recommendation] || recommendation;
    }

    function formatNumber(value) {
        const number = Number(value);

        if (!Number.isFinite(number)) {
            return '-';
        }

        return new Intl.NumberFormat(
            currentLocale === 'id' ? 'id-ID' : 'en-US',
            {
                maximumFractionDigits: 2
            }
        ).format(number);
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function showStatus(message, type) {
        const statusMessage =
            document.getElementById('statusMessage');

        statusMessage.textContent = message;
        statusMessage.className =
            `alert alert-${type} status-message mt-3 mb-0`;

        statusMessage.classList.remove('d-none');
    }

    function hideStatus() {
        const statusMessage =
            document.getElementById('statusMessage');

        statusMessage.classList.add('d-none');
        statusMessage.textContent = '';
    }
</script>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>
@include('user.partials.automatic-translation')
</body>
</html>
