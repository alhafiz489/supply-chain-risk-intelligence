<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">

    <title>
        {{ app()->getLocale() === 'id'
            ? 'Perbandingan Negara - SupplyGuard'
            : 'Country Comparison - SupplyGuard'
        }}
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="{{ asset('css/supplyguard-professional.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --dark: #111827;
            --muted: #64748b;
            --page-bg: #f4f7fb;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, 0.08),
                    transparent 32%
                ),
                var(--page-bg);
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
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            font-weight: 800;
        }

        .page-card {
            border: 0;
            border-radius: 24px;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
        }

        .selector-panel {
            padding: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: #f8fafc;
        }

        .form-select {
            min-height: 50px;
            border-radius: 12px;
        }

        .compare-button {
            min-height: 50px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(
                135deg,
                var(--primary),
                var(--primary-dark)
            );
            font-weight: 700;
        }

        .country-card {
            height: 100%;
            padding: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: white;
        }

        .country-name {
            font-size: 26px;
            font-weight: 800;
        }

        .risk-score {
            font-size: 42px;
            font-weight: 800;
            line-height: 1;
        }

        .risk-badge {
            display: inline-flex;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .risk-low {
            color: #166534;
            background: #dcfce7;
        }

        .risk-moderate {
            color: #92400e;
            background: #fef3c7;
        }

        .risk-high {
            color: #991b1b;
            background: #fee2e2;
        }

        .risk-critical {
            color: white;
            background: #111827;
        }

        .metric-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 13px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .metric-row:last-child {
            border-bottom: 0;
        }

        .metric-label {
            color: var(--muted);
        }

        .metric-value {
            text-align: right;
            font-weight: 700;
        }

        .recommendation-box {
            padding: 16px;
            border-radius: 14px;
            background: #eff6ff;
            color: #1e40af;
        }

        .chart-section {
            padding: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: white;
        }

        .chart-container {
            position: relative;
            height: 390px;
        }

        .winner-box {
            padding: 22px;
            border: 1px solid #bbf7d0;
            border-radius: 18px;
            background: #f0fdf4;
        }

        .status-message {
            border-radius: 14px;
        }

        @media (max-width: 767.98px) {
            .page-card .card-body {
                padding: 24px !important;
            }

            .chart-container {
                height: 320px;
            }
        }
    </style>
</head>

<body class="sg-user-sidebar-layout">
@include('user.partials.sidebar')

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container py-2">
        <a
            class="navbar-brand d-flex align-items-center gap-2 fw-bold"
            href="{{ route('dashboard') }}"
        >
            <span class="brand-mark">S</span>
            <span>SupplyGuard</span>
        </a>

        <a
    href="{{ route('watchlist.index') }}"
    class="btn btn-outline-light"
>
    {{ app()->getLocale() === 'id'
        ? 'Daftar Favorit'
        : 'Favorite List'
    }}
</a>

        <div class="d-flex align-items-center gap-2">
            <a
                href="{{ route('dashboard') }}"
                class="btn btn-outline-light"
            >
                {{ app()->getLocale() === 'id'
                    ? 'Dashboard'
                    : 'Dashboard'
                }}
            </a>

            <div class="dropdown">
                <button
                    class="btn btn-outline-light dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                >
                    {{ __('messages.choose_language') }}
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a
                            class="dropdown-item"
                            href="{{ route('language.switch', ['locale' => 'en']) }}"
                        >
                            🇬🇧 English
                        </a>
                    </li>

                    <li>
                        <a
                            class="dropdown-item"
                            href="{{ route('language.switch', ['locale' => 'id']) }}"
                        >
                            🇮🇩 Indonesia
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<main class="container py-5">
    <div class="card page-card">
        <div class="card-body p-5">

            <span class="badge text-bg-primary mb-3">
                Country Intelligence
            </span>

            <h1 class="fw-bold mb-2">
                {{ app()->getLocale() === 'id'
                    ? 'Mesin Perbandingan Negara'
                    : 'Country Comparison Engine'
                }}
            </h1>

            <p class="text-muted fs-5 mb-4">
                {{ app()->getLocale() === 'id'
                    ? 'Bandingkan dua negara untuk menentukan pilihan impor dengan tingkat risiko yang lebih rendah.'
                    : 'Compare two countries to determine an import option with a lower level of risk.'
                }}
            </p>

            <section class="selector-panel">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label
                            for="countryA"
                            class="form-label fw-semibold"
                        >
                            {{ app()->getLocale() === 'id'
                                ? 'Negara Pertama'
                                : 'First Country'
                            }}
                        </label>

                        <select id="countryA" class="form-select">
                            <option value="">
                                {{ app()->getLocale() === 'id'
                                    ? 'Pilih negara pertama'
                                    : 'Select the first country'
                                }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label
                            for="countryB"
                            class="form-label fw-semibold"
                        >
                            {{ app()->getLocale() === 'id'
                                ? 'Negara Kedua'
                                : 'Second Country'
                            }}
                        </label>

                        <select id="countryB" class="form-select">
                            <option value="">
                                {{ app()->getLocale() === 'id'
                                    ? 'Pilih negara kedua'
                                    : 'Select the second country'
                                }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button
                            id="compareButton"
                            type="button"
                            class="btn btn-primary compare-button w-100"
                            onclick="compareCountries()"
                        >
                            {{ app()->getLocale() === 'id'
                                ? 'Bandingkan'
                                : 'Compare'
                            }}
                        </button>
                    </div>
                </div>

                <div
                    id="statusMessage"
                    class="alert status-message mt-3 mb-0 d-none"
                ></div>
            </section>

            <section
                id="comparisonResult"
                class="mt-4 d-none"
            >
                <div class="row g-4">
                    <div class="col-lg-6">
                        <article class="country-card">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <small class="text-muted">
                                        {{ app()->getLocale() === 'id'
                                            ? 'Negara Pertama'
                                            : 'First Country'
                                        }}
                                    </small>

                                    <div
                                        id="countryAName"
                                        class="country-name"
                                    >
                                        -
                                    </div>
                                </div>

                                <span
                                    id="countryARiskBadge"
                                    class="risk-badge"
                                >
                                    -
                                </span>
                            </div>

                            <div class="mb-4">
                                <div
                                    id="countryAScore"
                                    class="risk-score"
                                >
                                    -
                                </div>

                                <small class="text-muted">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Skor risiko dari 100'
                                        : 'Risk score out of 100'
                                    }}
                                </small>
                            </div>

                            <div class="metric-row">
                                <span class="metric-label">GDP</span>
                                <span
                                    id="countryAGdp"
                                    class="metric-value"
                                >
                                    -
                                </span>
                            </div>

                            <div class="metric-row">
                                <span class="metric-label">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Inflasi'
                                        : 'Inflation'
                                    }}
                                </span>

                                <span
                                    id="countryAInflation"
                                    class="metric-value"
                                >
                                    -
                                </span>
                            </div>

                            <div class="metric-row">
                                <span class="metric-label">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Mata Uang'
                                        : 'Currency'
                                    }}
                                </span>

                                <span
                                    id="countryACurrency"
                                    class="metric-value"
                                >
                                    -
                                </span>
                            </div>

                            <div class="metric-row">
                                <span class="metric-label">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Risiko Cuaca'
                                        : 'Weather Risk'
                                    }}
                                </span>

                                <span
                                    id="countryAWeather"
                                    class="metric-value"
                                >
                                    -
                                </span>
                            </div>

                            <div class="metric-row">
                                <span class="metric-label">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Risiko Berita'
                                        : 'News Risk'
                                    }}
                                </span>

                                <span
                                    id="countryANews"
                                    class="metric-value"
                                >
                                    -
                                </span>
                            </div>

                            <div
                                id="countryARecommendation"
                                class="recommendation-box mt-4"
                            >
                                -
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-6">
                        <article class="country-card">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <small class="text-muted">
                                        {{ app()->getLocale() === 'id'
                                            ? 'Negara Kedua'
                                            : 'Second Country'
                                        }}
                                    </small>

                                    <div
                                        id="countryBName"
                                        class="country-name"
                                    >
                                        -
                                    </div>
                                </div>

                                <span
                                    id="countryBRiskBadge"
                                    class="risk-badge"
                                >
                                    -
                                </span>
                            </div>

                            <div class="mb-4">
                                <div
                                    id="countryBScore"
                                    class="risk-score"
                                >
                                    -
                                </div>

                                <small class="text-muted">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Skor risiko dari 100'
                                        : 'Risk score out of 100'
                                    }}
                                </small>
                            </div>

                            <div class="metric-row">
                                <span class="metric-label">GDP</span>
                                <span
                                    id="countryBGdp"
                                    class="metric-value"
                                >
                                    -
                                </span>
                            </div>

                            <div class="metric-row">
                                <span class="metric-label">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Inflasi'
                                        : 'Inflation'
                                    }}
                                </span>

                                <span
                                    id="countryBInflation"
                                    class="metric-value"
                                >
                                    -
                                </span>
                            </div>

                            <div class="metric-row">
                                <span class="metric-label">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Mata Uang'
                                        : 'Currency'
                                    }}
                                </span>

                                <span
                                    id="countryBCurrency"
                                    class="metric-value"
                                >
                                    -
                                </span>
                            </div>

                            <div class="metric-row">
                                <span class="metric-label">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Risiko Cuaca'
                                        : 'Weather Risk'
                                    }}
                                </span>

                                <span
                                    id="countryBWeather"
                                    class="metric-value"
                                >
                                    -
                                </span>
                            </div>

                            <div class="metric-row">
                                <span class="metric-label">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Risiko Berita'
                                        : 'News Risk'
                                    }}
                                </span>

                                <span
                                    id="countryBNews"
                                    class="metric-value"
                                >
                                    -
                                </span>
                            </div>

                            <div
                                id="countryBRecommendation"
                                class="recommendation-box mt-4"
                            >
                                -
                            </div>
                        </article>
                    </div>
                </div>

                <div
                    id="comparisonConclusion"
                    class="winner-box mt-4"
                >
                    -
                </div>

                <div class="chart-section mt-4">
                    <h4 class="fw-bold mb-1">
                        {{ app()->getLocale() === 'id'
                            ? 'Grafik Perbandingan Risiko'
                            : 'Risk Comparison Chart'
                        }}
                    </h4>

                    <p class="text-muted mb-4">
                        {{ app()->getLocale() === 'id'
                            ? 'Perbandingan lima komponen risiko antara kedua negara.'
                            : 'Comparison of five risk components between both countries.'
                        }}
                    </p>

                    <div class="chart-container">
                        <canvas id="comparisonChart"></canvas>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<script>
    const currentLocale = @json(app()->getLocale());

    let comparisonChartInstance = null;

    const text = {
        loading: currentLocale === 'id'
            ? 'Memuat negara...'
            : 'Loading countries...',

        selectFirst: currentLocale === 'id'
            ? 'Pilih negara pertama'
            : 'Select the first country',

        selectSecond: currentLocale === 'id'
            ? 'Pilih negara kedua'
            : 'Select the second country',

        chooseBoth: currentLocale === 'id'
            ? 'Pilih dua negara terlebih dahulu.'
            : 'Please select two countries first.',

        differentCountry: currentLocale === 'id'
            ? 'Pilih dua negara yang berbeda.'
            : 'Please select two different countries.',

        processing: currentLocale === 'id'
            ? 'Membandingkan...'
            : 'Comparing...',

        failed: currentLocale === 'id'
            ? 'Data negara gagal dibandingkan.'
            : 'Failed to compare country data.'
    };

    document.addEventListener('DOMContentLoaded', function () {
        loadCountries();
    });

    async function loadCountries() {
        const countryA = document.getElementById('countryA');
        const countryB = document.getElementById('countryB');

        countryA.disabled = true;
        countryB.disabled = true;

        countryA.innerHTML = `<option value="">${text.loading}</option>`;
        countryB.innerHTML = `<option value="">${text.loading}</option>`;

        try {
            const response = await fetch('/api/countries', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(text.failed);
            }

            countryA.innerHTML =
                `<option value="">${text.selectFirst}</option>`;

            countryB.innerHTML =
                `<option value="">${text.selectSecond}</option>`;

            result.data.forEach(function (country) {
                const optionA = document.createElement('option');
                optionA.value = country.id;
                optionA.textContent = country.name;

                const optionB = document.createElement('option');
                optionB.value = country.id;
                optionB.textContent = country.name;

                countryA.appendChild(optionA);
                countryB.appendChild(optionB);
            });

        } catch (error) {
            console.error(error);
            showStatus(text.failed, 'danger');
        } finally {
            countryA.disabled = false;
            countryB.disabled = false;
        }
    }

    async function compareCountries() {
        const countryAId =
            document.getElementById('countryA').value;

        const countryBId =
            document.getElementById('countryB').value;

        if (!countryAId || !countryBId) {
            showStatus(text.chooseBoth, 'warning');
            return;
        }

        if (countryAId === countryBId) {
            showStatus(text.differentCountry, 'warning');
            return;
        }

        const button =
            document.getElementById('compareButton');

        const originalText =
            button.textContent.trim();

        button.disabled = true;
        button.textContent = text.processing;

        hideStatus();

        try {
            const [
                countryA,
                countryB
            ] = await Promise.all([
                fetchCountryAnalysis(countryAId),
                fetchCountryAnalysis(countryBId)
            ]);

            renderCountry('A', countryA);
            renderCountry('B', countryB);

            renderConclusion(countryA, countryB);
            renderComparisonChart(countryA, countryB);

            document
                .getElementById('comparisonResult')
                .classList.remove('d-none');

        } catch (error) {
            console.error(error);
            showStatus(error.message || text.failed, 'danger');
        } finally {
            button.disabled = false;
            button.textContent = originalText;
        }
    }

    async function fetchCountryAnalysis(countryId) {
        const responses = await Promise.all([
            fetch(`/api/countries/${countryId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            }),

            fetch(`/api/currency?country_id=${countryId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            }),

            fetch(`/api/risk?country_id=${countryId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
        ]);

        const [
            countryResult,
            currencyResult,
            riskResult
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
            !riskResult.success
        ) {
            throw new Error(text.failed);
        }

        return {
            country: countryResult.data,
            currency: currencyResult.data,
            risk: riskResult.data
        };
    }

    function renderCountry(side, result) {
        const country = result.country;
        const currency = result.currency;
        const risk = result.risk;

        document.getElementById(`country${side}Name`).textContent =
            country.name;

        document.getElementById(`country${side}Score`).textContent =
            risk.total_score + '/100';

        document.getElementById(`country${side}Gdp`).textContent =
            formatNumber(country.gdp_usd_billion) + ' B USD';

        document.getElementById(`country${side}Inflation`).textContent =
            formatNumber(country.inflation_rate) + '%';

        document.getElementById(`country${side}Currency`).textContent =
            currency.currency_code +
            ' / ' +
            formatNumber(currency.exchange_rate_to_idr) +
            ' IDR';

        document.getElementById(`country${side}Weather`).textContent =
            (risk.components.weather_risk?.score ?? 0) + '/100';

        document.getElementById(`country${side}News`).textContent =
            (risk.components.news_risk?.score ?? 0) + '/100';

        document.getElementById(
            `country${side}Recommendation`
        ).textContent =
            translateRecommendation(risk.recommendation);

        setRiskBadge(
            `country${side}RiskBadge`,
            risk.risk_label,
            risk.total_score
        );
    }

    function renderConclusion(countryA, countryB) {
        const scoreA = Number(countryA.risk.total_score);
        const scoreB = Number(countryB.risk.total_score);

        const container =
            document.getElementById('comparisonConclusion');

        if (scoreA === scoreB) {
            container.innerHTML =
                currentLocale === 'id'
                    ? `<strong>Hasil seimbang.</strong> Kedua negara memiliki skor risiko yang sama, yaitu ${scoreA}/100.`
                    : `<strong>Balanced result.</strong> Both countries have the same risk score of ${scoreA}/100.`;

            return;
        }

        const safer =
            scoreA < scoreB ? countryA : countryB;

        const higher =
            scoreA > scoreB ? countryA : countryB;

        const difference = Math.abs(scoreA - scoreB);

        container.innerHTML =
            currentLocale === 'id'
                ? `
                    <strong>${escapeHtml(safer.country.name)} lebih direkomendasikan.</strong>
                    Negara ini memiliki skor risiko ${safer.risk.total_score}/100,
                    lebih rendah ${difference} poin dibandingkan
                    ${escapeHtml(higher.country.name)}.
                `
                : `
                    <strong>${escapeHtml(safer.country.name)} is more recommended.</strong>
                    This country has a risk score of ${safer.risk.total_score}/100,
                    which is ${difference} points lower than
                    ${escapeHtml(higher.country.name)}.
                `;
    }

    function renderComparisonChart(countryA, countryB) {
        const chartElement =
            document.getElementById('comparisonChart');

        const labels = currentLocale === 'id'
            ? [
                'Cuaca',
                'Inflasi',
                'Mata Uang',
                'Berita',
                'Pelabuhan'
            ]
            : [
                'Weather',
                'Inflation',
                'Currency',
                'News',
                'Port'
            ];

        const valuesA = extractRiskValues(countryA.risk.components);
        const valuesB = extractRiskValues(countryB.risk.components);

        if (comparisonChartInstance) {
            comparisonChartInstance.destroy();
        }

        comparisonChartInstance = new Chart(chartElement, {
            type: 'radar',

            data: {
                labels: labels,

                datasets: [
                    {
                        label: countryA.country.name,
                        data: valuesA,
                        borderWidth: 2,
                        pointRadius: 4,
                        backgroundColor: 'rgba(37, 99, 235, 0.16)',
                        borderColor: 'rgb(37, 99, 235)',
                        pointBackgroundColor: 'rgb(37, 99, 235)'
                    },
                    {
                        label: countryB.country.name,
                        data: valuesB,
                        borderWidth: 2,
                        pointRadius: 4,
                        backgroundColor: 'rgba(239, 68, 68, 0.12)',
                        borderColor: 'rgb(239, 68, 68)',
                        pointBackgroundColor: 'rgb(239, 68, 68)'
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                scales: {
                    r: {
                        beginAtZero: true,
                        min: 0,
                        max: 100,

                        ticks: {
                            stepSize: 20
                        }
                    }
                },

                plugins: {
                    legend: {
                        position: 'bottom'
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return (
                                    context.dataset.label +
                                    ': ' +
                                    context.raw +
                                    '/100'
                                );
                            }
                        }
                    }
                }
            }
        });
    }

    function extractRiskValues(components) {
        return [
            components.weather_risk?.score ?? 0,
            components.inflation_risk?.score ?? 0,
            components.currency_risk?.score ?? 0,
            components.news_risk?.score ?? 0,
            components.port_risk?.score ?? 0
        ];
    }

    function setRiskBadge(elementId, label, score) {
        const badge = document.getElementById(elementId);

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
            currentLocale === 'id'
                ? 'id-ID'
                : 'en-US',
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
