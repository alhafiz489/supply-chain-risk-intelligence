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

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --dark-color: #111827;
            --muted-color: #64748b;
            --page-background: #f4f7fb;
        }

        body {
            min-height: 100vh;
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
            border-radius: 12px;
            background: linear-gradient(
                135deg,
                #3b82f6,
                #1d4ed8
            );
            color: white;
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
            padding: 22px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: white;
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
        }

        .analysis-panel {
            padding: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
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
            background: linear-gradient(
                135deg,
                #1d4ed8,
                #1e40af
            );
        }

        .recommendation-box {
            padding: 22px;
            border: 1px solid #bfdbfe;
            border-radius: 18px;
            background: linear-gradient(
                135deg,
                #eff6ff,
                #f8fbff
            );
        }

        .component-section {
            padding: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: white;
        }

        .component-item {
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
            color: white;
        }

        .status-message {
            border-radius: 14px;
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

            <!-- Ringkasan data negara -->
            <div class="row g-3">
                <div class="col-sm-6 col-xl-3">
                    <div class="metric-card">
                        <div class="metric-label">
                            {{ __('messages.gdp') }}
                        </div>

                        <h3 id="gdpValue" class="metric-value">
                            -
                        </h3>

                        <small
                            id="gdpUnit"
                            class="text-muted"
                        >
                            USD Billion
                        </small>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="metric-card">
                        <div class="metric-label">
                            {{ __('messages.inflation') }}
                        </div>

                        <h3 id="inflationValue" class="metric-value">
                            -
                        </h3>

                        <small class="text-muted">
                            Percent
                        </small>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="metric-card">
                        <div class="metric-label">
                            {{ __('messages.currency') }}
                        </div>

                        <h3 id="currencyValue" class="metric-value">
                            -
                        </h3>

                        <small
                            id="currencyDetail"
                            class="text-muted"
                        >
                            Exchange rate
                        </small>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="metric-card">
                        <div class="metric-label">
                            {{ __('messages.risk_score') }}
                        </div>

                        <h3
                            id="riskScoreValue"
                            class="metric-value mb-2"
                        >
                            -
                        </h3>

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
                        <select
                            id="countrySelect"
                            class="form-select"
                        >
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
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
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
                </div>

                <div class="row g-3">
                    <div class="col-sm-6 col-lg">
                        <div class="component-item h-100">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Cuaca'
                                        : 'Weather'
                                    }}
                                </span>

                                <span class="component-weight">
                                    27%
                                </span>
                            </div>

                            <div
                                id="weatherRiskValue"
                                class="component-score mt-2"
                            >
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
                        <div class="component-item h-100">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Inflasi'
                                        : 'Inflation'
                                    }}
                                </span>

                                <span class="component-weight">
                                    21%
                                </span>
                            </div>

                            <div
                                id="inflationRiskValue"
                                class="component-score mt-2"
                            >
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
                        <div class="component-item h-100">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Mata Uang'
                                        : 'Currency'
                                    }}
                                </span>

                                <span class="component-weight">
                                    18%
                                </span>
                            </div>

                            <div
                                id="currencyRiskValue"
                                class="component-score mt-2"
                            >
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
                        <div class="component-item h-100">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Berita'
                                        : 'News'
                                    }}
                                </span>

                                <span class="component-weight">
                                    22%
                                </span>
                            </div>

                            <div
                                id="newsRiskValue"
                                class="component-score mt-2"
                            >
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
                        <div class="component-item h-100">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Pelabuhan'
                                        : 'Port'
                                    }}
                                </span>

                                <span class="component-weight">
                                    12%
                                </span>
                            </div>

                            <div
                                id="portRiskValue"
                                class="component-score mt-2"
                            >
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

        </div>
    </div>
</main>

<script>
    const currentLocale = @json(app()->getLocale());

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
                throw new Error(
                    result.message || text.countriesFailed
                );
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

            showStatus(
                text.countriesFailed,
                'danger'
            );
        } finally {
            select.disabled = false;
        }
    }

    async function analyzeCountry() {
        const countrySelect =
            document.getElementById('countrySelect');

        const countryId = countrySelect.value;

        if (!countryId) {
            showStatus(
                text.chooseCountry,
                'warning'
            );

            countrySelect.focus();

            return;
        }

        const analyzeButton =
            document.getElementById('analyzeButton');

        const originalButtonText =
            analyzeButton.textContent.trim();

        analyzeButton.disabled = true;
        analyzeButton.textContent = text.calculating;

        hideStatus();

        try {
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
                !responses[0].ok ||
                !responses[1].ok ||
                !responses[2].ok ||
                !countryResult.success ||
                !currencyResult.success ||
                !riskResult.success
            ) {
                throw new Error(text.analysisFailed);
            }

            const country = countryResult.data;
            const currency = currencyResult.data;
            const risk = riskResult.data;

            renderCountrySummary(
                country,
                currency,
                risk
            );

            renderRiskRecommendation(
                country,
                risk
            );

            renderRiskComponents(
                risk.components
            );

        } catch (error) {
            console.error(error);

            showStatus(
                error.message || text.analysisFailed,
                'danger'
            );
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
            text.exchangeRate +
            ': ' +
            formatNumber(currency.exchange_rate_to_idr);

        document.getElementById('riskScoreValue').textContent =
            risk.total_score + '/100';

        setRiskLabel(
            risk.risk_label,
            risk.total_score
        );
    }

    function renderRiskRecommendation(country, risk) {
        const recommendationBox =
            document.getElementById(
                'riskRecommendationBox'
            );

        document.getElementById(
            'riskRecommendationTitle'
        ).textContent =
            text.recommendationFor +
            ' ' +
            country.name;

        document.getElementById(
            'riskRecommendationValue'
        ).textContent =
            translateRecommendation(
                risk.recommendation
            );

        recommendationBox.classList.remove('d-none');
    }

    function renderRiskComponents(components) {
        if (!components) {
            return;
        }

        const componentData = {
            weather: components.weather_risk?.score ?? 0,
            inflation: components.inflation_risk?.score ?? 0,
            currency: components.currency_risk?.score ?? 0,
            news: components.news_risk?.score ?? 0,
            port: components.port_risk?.score ?? 0
        };

        updateRiskComponent(
            'weatherRiskValue',
            'weatherRiskBar',
            componentData.weather
        );

        updateRiskComponent(
            'inflationRiskValue',
            'inflationRiskBar',
            componentData.inflation
        );

        updateRiskComponent(
            'currencyRiskValue',
            'currencyRiskBar',
            componentData.currency
        );

        updateRiskComponent(
            'newsRiskValue',
            'newsRiskBar',
            componentData.news
        );

        updateRiskComponent(
            'portRiskValue',
            'portRiskBar',
            componentData.port
        );

        document
            .getElementById('riskComponentSection')
            .classList.remove('d-none');
    }

    function updateRiskComponent(
        valueElementId,
        barElementId,
        value
    ) {
        const normalizedValue = Math.max(
            0,
            Math.min(100, Number(value) || 0)
        );

        const valueElement =
            document.getElementById(valueElementId);

        const barElement =
            document.getElementById(barElementId);

        valueElement.textContent =
            normalizedValue + '/100';

        barElement.style.width =
            normalizedValue + '%';

        barElement.className =
            'progress-bar ' +
            getProgressColor(normalizedValue);
    }

    function setRiskLabel(label, score) {
        const badge =
            document.getElementById('riskLabelValue');

        badge.textContent =
            translateRiskLabel(label);

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