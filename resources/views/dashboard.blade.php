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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --dark-color: #111827;
            --muted-color: #64748b;
            --page-background: #f4f7fb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, 0.08),
                    transparent 32%
                ),
                var(--page-background);
            color: #1f2937;
        }

        .navbar-custom {
            background: rgba(17, 24, 39, 0.97);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #ffffff;
            font-size: 18px;
            font-weight: 800;
        }

        .main-card {
            border: 0;
            border-radius: 24px;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 13px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 700;
        }

        .page-title {
            max-width: 960px;
            font-size: clamp(30px, 4vw, 48px);
            line-height: 1.15;
            letter-spacing: -1.5px;
        }

        .page-subtitle {
            max-width: 940px;
            color: var(--muted-color);
            line-height: 1.7;
        }

        .metric-card {
            height: 100%;
            min-height: 150px;
            padding: 22px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: #ffffff;
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                border-color 0.2s ease;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            border-color: #bfdbfe;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        .metric-label {
            margin-bottom: 12px;
            color: var(--muted-color);
            font-size: 14px;
            font-weight: 600;
        }

        .metric-value {
            margin-bottom: 0;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.7px;
            overflow-wrap: anywhere;
        }

        .analysis-panel,
        .component-section,
        .visualization-section {
            padding: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: #ffffff;
        }

        .analysis-panel {
            background: #f8fafc;
        }

        .form-select {
            min-height: 50px;
            border-radius: 12px;
        }

        .analyze-button {
            min-height: 50px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(
                135deg,
                var(--primary-color),
                var(--primary-dark)
            );
            font-weight: 700;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.22);
        }

        .analyze-button:hover,
        .analyze-button:focus {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
        }

        .recommendation-box {
            padding: 22px;
            border: 1px solid #bfdbfe;
            border-radius: 18px;
            background: linear-gradient(135deg, #eff6ff, #f8fbff);
        }

        .component-item {
            height: 100%;
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 15px;
            background: #f8fafc;
        }

        .component-score {
            font-size: 23px;
            font-weight: 800;
        }

        .component-weight {
            color: var(--muted-color);
            font-size: 12px;
        }

        .progress {
            height: 8px;
            border-radius: 999px;
            background: #e5e7eb;
        }

        .progress-bar {
            border-radius: 999px;
            transition: width 0.5s ease;
        }

        .risk-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .risk-low {
            background: #dcfce7;
            color: #166534;
        }

        .risk-moderate {
            background: #fef3c7;
            color: #92400e;
        }

        .risk-high {
            background: #fee2e2;
            color: #991b1b;
        }

        .risk-critical {
            background: #111827;
            color: #ffffff;
        }

        .status-message {
            border-radius: 14px;
        }

        .visualization-card {
            height: 100%;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #f8fafc;
        }

        .chart-container {
            position: relative;
            width: 100%;
            height: 320px;
        }

        #portMap {
            width: 100%;
            height: 420px;
            border: 1px solid #e5e7eb;
            border-radius: 15px;
            z-index: 1;
        }

        .port-information {
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #f8fafc;
        }

        .port-status-badge {
            display: inline-flex;
            flex-shrink: 0;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .port-status-low {
            color: #166534;
            background: #dcfce7;
        }

        .port-status-medium {
            color: #92400e;
            background: #fef3c7;
        }

        .port-status-high {
            color: #991b1b;
            background: #fee2e2;
        }

        @media (max-width: 767.98px) {
            .main-card .card-body {
                padding: 24px !important;
            }

            .page-title {
                letter-spacing: -0.8px;
            }

            .navbar .container {
                gap: 14px;
            }

            .navbar-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .chart-container {
                height: 280px;
            }

            #portMap {
                height: 340px;
            }
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container py-2">
        <a
            class="navbar-brand d-flex align-items-center gap-2 fw-bold"
            href="{{ url('/') }}"
        >
            <span class="brand-mark">S</span>
            <span>SupplyGuard</span>
        </a>

        <div class="navbar-actions d-flex gap-2 align-items-center">
            <div class="dropdown">
                <button
                    class="btn btn-outline-light dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    {{ __('messages.choose_language') }}
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li>
                        <a
                            class="dropdown-item py-2"
                            href="{{ route('language.switch', ['locale' => 'en']) }}"
                        >
                            🇬🇧 {{ __('messages.english') }}
                        </a>
                    </li>

                    <li>
                        <a
                            class="dropdown-item py-2"
                            href="{{ route('language.switch', ['locale' => 'id']) }}"
                        >
                            🇮🇩 {{ __('messages.indonesian') }}
                        </a>
                    </li>
                </ul>
            </div>

            <a href="#" class="btn btn-primary px-4">
                {{ __('messages.login') }}
            </a>
        </div>
    </div>
</nav>

<main class="container py-5">
    <div class="card main-card">
        <div class="card-body p-5">
            <span class="hero-badge mb-3">
                Supply Chain Intelligence
            </span>

            <h1 class="page-title fw-bold mb-3">
                {{ __('messages.app_title') }}
            </h1>

            <p class="page-subtitle fs-5 mb-4">
                {{ __('messages.app_subtitle') }}
            </p>

            <hr class="mb-4">

            <!-- Ringkasan utama -->
            <div class="row g-3">
                <div class="col-sm-6 col-xl-3">
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

                <div class="col-sm-6 col-xl-3">
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

                <div class="col-sm-6 col-xl-3">
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

                <div class="col-sm-6 col-xl-3">
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
            </div>

            <!-- Form analisis -->
            <section class="analysis-panel mt-4">
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

            <!-- Rekomendasi -->
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
                        </div>
                    </div>
                </div>
            </section>

            <!-- Grafik -->
            <section
                id="visualizationSection"
                class="visualization-section mt-4 d-none"
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

                            <div class="chart-container">
                                <canvas id="currencyTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Peta pelabuhan -->
            <section
                id="portSection"
                class="visualization-section mt-4 d-none"
            >
                <div class="row g-4">
                    <div class="col-lg-8">
                        <h4 class="fw-bold mb-1">
                            {{ app()->getLocale() === 'id'
                                ? 'Peta Lokasi Pelabuhan'
                                : 'Port Location Map'
                            }}
                        </h4>

                        <p class="text-muted">
                            {{ app()->getLocale() === 'id'
                                ? 'Lokasi pelabuhan utama pada negara yang dipilih.'
                                : 'Main port locations in the selected country.'
                            }}
                        </p>

                        <div id="portMap"></div>
                    </div>

                    <div class="col-lg-4">
                        <h5 class="fw-bold mb-3">
                            {{ app()->getLocale() === 'id'
                                ? 'Informasi Pelabuhan'
                                : 'Port Information'
                            }}
                        </h5>

                        <div id="portInformationList">
                            <p class="text-muted">
                                {{ app()->getLocale() === 'id'
                                    ? 'Pilih negara untuk melihat informasi pelabuhan.'
                                    : 'Select a country to view port information.'
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<!-- Leaflet JavaScript -->
<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    crossorigin=""
></script>

<script>
    const currentLocale = @json(app()->getLocale());

    let riskComponentChartInstance = null;
    let currencyTrendChartInstance = null;
    let portMapInstance = null;
    let portMarkerGroup = null;

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
            : 'Exchange to IDR'
    };

    document.addEventListener('DOMContentLoaded', function () {
        loadCountries();
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
                fetch(`/api/ports?country_id=${countryId}`, {
                    headers: { 'Accept': 'application/json' }
                })
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
            renderRiskRecommendation(country, risk);
            renderRiskComponents(risk.components);

            document
                .getElementById('visualizationSection')
                .classList.remove('d-none');

            document
                .getElementById('portSection')
                .classList.remove('d-none');

            renderRiskComponentChart(risk.components);

            renderCurrencyTrendChart(
                currency.trend,
                currency.currency_code,
                country.name
            );

            renderPortMap(ports, country);
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
            formatNumber(country.inflation_rate) + '%';

        document.getElementById('currencyValue').textContent =
            currency.currency_code || '-';

        document.getElementById('currencyDetail').textContent =
            text.exchangeRate + ': ' + formatNumber(currency.exchange_rate_to_idr);

        document.getElementById('riskScoreValue').textContent =
            risk.total_score + '/100';

        setRiskLabel(risk.risk_label, risk.total_score);
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

    function renderRiskComponents(components) {
        if (!components) {
            return;
        }

        updateRiskComponent(
            'weatherRiskValue',
            'weatherRiskBar',
            components.weather_risk?.score ?? 0
        );

        updateRiskComponent(
            'inflationRiskValue',
            'inflationRiskBar',
            components.inflation_risk?.score ?? 0
        );

        updateRiskComponent(
            'currencyRiskValue',
            'currencyRiskBar',
            components.currency_risk?.score ?? 0
        );

        updateRiskComponent(
            'newsRiskValue',
            'newsRiskBar',
            components.news_risk?.score ?? 0
        );

        updateRiskComponent(
            'portRiskValue',
            'portRiskBar',
            components.port_risk?.score ?? 0
        );

        document
            .getElementById('riskComponentSection')
            .classList.remove('d-none');
    }

    function updateRiskComponent(valueElementId, barElementId, value) {
        const normalizedValue = Math.max(
            0,
            Math.min(100, Number(value) || 0)
        );

        const valueElement = document.getElementById(valueElementId);
        const barElement = document.getElementById(barElementId);

        valueElement.textContent = normalizedValue + '/100';
        barElement.style.width = normalizedValue + '%';
        barElement.className =
            'progress-bar ' + getProgressColor(normalizedValue);
    }

    function renderRiskComponentChart(components) {
        const chartElement = document.getElementById('riskComponentChart');

        if (!chartElement || !components) {
            return;
        }

        const labels = currentLocale === 'id'
            ? ['Cuaca', 'Inflasi', 'Mata Uang', 'Berita', 'Pelabuhan']
            : ['Weather', 'Inflation', 'Currency', 'News', 'Port'];

        const values = [
            components.weather_risk?.score ?? 0,
            components.inflation_risk?.score ?? 0,
            components.currency_risk?.score ?? 0,
            components.news_risk?.score ?? 0,
            components.port_risk?.score ?? 0
        ];

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
                                return context.raw + '/100';
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
        const chartElement = document.getElementById('currencyTrendChart');

        if (!chartElement || !Array.isArray(trend)) {
            return;
        }

        const labels = trend.map(function (item) {
            return item.month;
        });

        const values = trend.map(function (item) {
            return Number(item.rate);
        });

        document.getElementById('currencyChartDescription').textContent =
            currentLocale === 'id'
                ? `Perubahan ${currencyCode} terhadap IDR untuk ${countryName}.`
                : `${currencyCode} exchange movement against IDR for ${countryName}.`;

        if (currencyTrendChartInstance) {
            currencyTrendChartInstance.destroy();
        }

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

    function renderPortMap(ports, country) {
        const mapElement = document.getElementById('portMap');
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
                    attribution: '&copy; OpenStreetMap contributors'
                }
            ).addTo(portMapInstance);

            portMarkerGroup =
                L.layerGroup().addTo(portMapInstance);
        }

        portMarkerGroup.clearLayers();
        informationList.innerHTML = '';

        if (!Array.isArray(ports) || ports.length === 0) {
            informationList.innerHTML = `
                <div class="alert alert-warning mb-0">
                    ${
                        currentLocale === 'id'
                            ? 'Belum ada data pelabuhan untuk negara ini.'
                            : 'No port data is available for this country.'
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
                !Number.isFinite(latitude) ||
                !Number.isFinite(longitude)
            ) {
                return;
            }

            coordinates.push([latitude, longitude]);

            const translatedStatus =
                translateCongestionLevel(port.congestion_level);

            const marker = L.marker([latitude, longitude]);

            marker.bindPopup(`
                <strong>${escapeHtml(port.name)}</strong>
                <br>
                ${currentLocale === 'id' ? 'Kota' : 'City'}:
                ${escapeHtml(port.city || '-')}
                <br>
                ${currentLocale === 'id' ? 'Kemacetan' : 'Congestion'}:
                ${escapeHtml(translatedStatus)}
                <br>
                ${currentLocale === 'id' ? 'Keterlambatan' : 'Delay'}:
                ${Number(port.delay_days) || 0}
                ${currentLocale === 'id' ? 'hari' : 'days'}
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

                                <p class="text-muted small mb-2">
                                    ${escapeHtml(port.city || '-')}
                                </p>
                            </div>

                            <span class="port-status-badge ${getPortStatusClass(port.congestion_level)}">
                                ${escapeHtml(translatedStatus)}
                            </span>
                        </div>

                        <small class="text-muted">
                            ${
                                currentLocale === 'id'
                                    ? 'Perkiraan keterlambatan'
                                    : 'Estimated delay'
                            }:
                            ${Number(port.delay_days) || 0}
                            ${currentLocale === 'id' ? 'hari' : 'days'}
                        </small>
                    </div>
                `
            );
        });

        if (coordinates.length === 1) {
            portMapInstance.setView(coordinates[0], 7);
        } else if (coordinates.length > 1) {
            portMapInstance.fitBounds(coordinates, {
                padding: [35, 35]
            });
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

        return 'port-status-low';
    }

    function translateCongestionLevel(level) {
        if (currentLocale !== 'id') {
            return level || '-';
        }

        const translations = {
            Low: 'Rendah',
            Medium: 'Sedang',
            High: 'Tinggi'
        };

        return translations[level] || level || '-';
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
</body>
</html>