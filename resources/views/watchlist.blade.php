<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">

    <title>
        {{ app()->getLocale() === 'id'
            ? 'Daftar Pemantauan Favorit - SupplyGuard'
            : 'Favorite Monitoring List - SupplyGuard'
        }}
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="{{ asset('css/supplyguard-professional.css') }}">

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
            overflow: hidden;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 13px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 700;
        }

        .add-panel {
            padding: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: #f8fafc;
        }

        .form-select,
        .add-button {
            min-height: 50px;
            border-radius: 12px;
        }

        .add-button {
            border: 0;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            font-weight: 700;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.22);
        }

        .summary-card {
            height: 100%;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 17px;
            background: white;
        }

        .summary-number {
            margin: 0;
            font-size: 30px;
            font-weight: 800;
        }

        .watch-card {
            height: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 19px;
            background: white;
            overflow: hidden;
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                border-color 0.2s ease;
        }

        .watch-card:hover {
            transform: translateY(-3px);
            border-color: #bfdbfe;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.09);
        }

        .watch-card-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #f8fafc, #ffffff);
        }

        .watch-card-body {
            padding: 20px;
        }

        .country-code {
            display: inline-flex;
            padding: 5px 9px;
            border-radius: 999px;
            background: #e0e7ff;
            color: #3730a3;
            font-size: 12px;
            font-weight: 700;
        }

        .risk-score {
            font-size: 34px;
            font-weight: 800;
            line-height: 1;
        }

        .risk-badge {
            display: inline-flex;
            padding: 6px 10px;
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
            padding: 11px 0;
            border-bottom: 1px solid #edf0f4;
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

        .empty-state {
            padding: 54px 24px;
            border: 1px dashed #cbd5e1;
            border-radius: 18px;
            background: #f8fafc;
            text-align: center;
        }

        .loading-card {
            height: 280px;
            border-radius: 18px;
            background:
                linear-gradient(
                    90deg,
                    #f1f5f9 25%,
                    #e2e8f0 37%,
                    #f1f5f9 63%
                );
            background-size: 400% 100%;
            animation: loading 1.4s ease infinite;
        }

        @keyframes loading {
            0% {
                background-position: 100% 0;
            }

            100% {
                background-position: 0 0;
            }
        }

        .status-message {
            border-radius: 14px;
        }

        @media (max-width: 767.98px) {
            .page-card .card-body {
                padding: 24px !important;
            }

            .navbar .container {
                gap: 12px;
            }

            .navbar-actions {
                width: 100%;
                flex-wrap: wrap;
                justify-content: flex-end;
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

        <div class="navbar-actions d-flex align-items-center gap-2">
            <a
                href="{{ route('dashboard') }}"
                class="btn btn-outline-light"
            >
                {{ app()->getLocale() === 'id' ? 'Dashboard' : 'Dashboard' }}
            </a>

            <a
                href="{{ route('country.comparison') }}"
                class="btn btn-outline-light"
            >
                {{ app()->getLocale() === 'id'
                    ? 'Bandingkan Negara'
                    : 'Compare Countries'
                }}
            </a>

            <div class="dropdown">
                <button
                    class="btn btn-outline-light dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
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
            <span class="hero-badge mb-3">
                Monitoring Intelligence
            </span>

            <h1 class="fw-bold mb-2">
                {{ app()->getLocale() === 'id'
                    ? 'Daftar Pemantauan Favorit'
                    : 'Favorite Monitoring List'
                }}
            </h1>

            <p class="text-muted fs-5 mb-4">
                {{ app()->getLocale() === 'id'
                    ? 'Simpan negara penting agar kondisi ekonomi dan tingkat risikonya dapat dipantau dengan cepat.'
                    : 'Save important countries so their economic conditions and risk levels can be monitored quickly.'
                }}
            </p>

            <section class="add-panel">
                <div class="row g-3 align-items-end">
                    <div class="col-md-9 col-lg-10">
                        <label
                            for="favoriteCountrySelect"
                            class="form-label fw-semibold"
                        >
                            {{ app()->getLocale() === 'id'
                                ? 'Pilih Negara'
                                : 'Select Country'
                            }}
                        </label>

                        <select
                            id="favoriteCountrySelect"
                            class="form-select"
                        >
                            <option value="">
                                {{ app()->getLocale() === 'id'
                                    ? 'Memuat negara...'
                                    : 'Loading countries...'
                                }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 col-lg-2">
                        <button
                            id="addFavoriteButton"
                            type="button"
                            class="btn btn-primary add-button w-100"
                            onclick="addFavorite()"
                        >
                            {{ app()->getLocale() === 'id'
                                ? 'Tambah'
                                : 'Add'
                            }}
                        </button>
                    </div>
                </div>

                <div
                    id="statusMessage"
                    class="alert status-message mt-3 mb-0 d-none"
                    role="alert"
                ></div>
            </section>

            <section class="mt-4">
                <div class="row g-3">
                    <div class="col-sm-6 col-lg-4">
                        <div class="summary-card">
                            <div class="text-muted small mb-2">
                                {{ app()->getLocale() === 'id'
                                    ? 'Total Negara Dipantau'
                                    : 'Total Monitored Countries'
                                }}
                            </div>

                            <h3 id="totalFavoriteValue" class="summary-number">
                                0
                            </h3>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="summary-card">
                            <div class="text-muted small mb-2">
                                {{ app()->getLocale() === 'id'
                                    ? 'Risiko Tinggi atau Kritis'
                                    : 'High or Critical Risk'
                                }}
                            </div>

                            <h3 id="highRiskValue" class="summary-number">
                                0
                            </h3>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="summary-card">
                            <div class="text-muted small mb-2">
                                {{ app()->getLocale() === 'id'
                                    ? 'Terakhir Diperbarui'
                                    : 'Last Updated'
                                }}
                            </div>

                            <h6 id="lastUpdatedValue" class="fw-bold mb-0">
                                -
                            </h6>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">
                            {{ app()->getLocale() === 'id'
                                ? 'Negara Favorit'
                                : 'Favorite Countries'
                            }}
                        </h4>

                        <p class="text-muted mb-0">
                            {{ app()->getLocale() === 'id'
                                ? 'Data akan diperbarui setiap kali halaman dibuka atau tombol perbarui ditekan.'
                                : 'Data is updated whenever the page is opened or the refresh button is pressed.'
                            }}
                        </p>
                    </div>

                    <button
                        id="refreshAllButton"
                        type="button"
                        class="btn btn-outline-primary"
                        onclick="renderWatchlist()"
                    >
                        {{ app()->getLocale() === 'id'
                            ? 'Perbarui Semua'
                            : 'Refresh All'
                        }}
                    </button>
                </div>

                <div id="watchlistContent"></div>
            </section>
        </div>
    </div>
</main>

<script>
    const currentLocale = @json(app()->getLocale());

    const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute('content');

    let countries = [];

    const text = {
        selectCountry: currentLocale === 'id'
            ? 'Pilih negara'
            : 'Select a country',

        loadFailed: currentLocale === 'id'
            ? 'Data negara gagal dimuat.'
            : 'Failed to load country data.',

        chooseCountry: currentLocale === 'id'
            ? 'Pilih negara terlebih dahulu.'
            : 'Please select a country first.',

        alreadyAdded: currentLocale === 'id'
            ? 'Negara tersebut sudah ada di daftar favorit.'
            : 'That country is already in the favorite list.',

        added: currentLocale === 'id'
            ? 'Negara berhasil ditambahkan ke daftar favorit.'
            : 'Country added to the favorite list.',

        removed: currentLocale === 'id'
            ? 'Negara berhasil dihapus dari daftar favorit.'
            : 'Country removed from the favorite list.',

        noFavorite: currentLocale === 'id'
            ? 'Belum ada negara favorit'
            : 'No favorite countries yet',

        noFavoriteDescription: currentLocale === 'id'
            ? 'Pilih negara di atas lalu tekan tombol Tambah untuk mulai memantau.'
            : 'Select a country above and press Add to start monitoring.',

        loading: currentLocale === 'id'
            ? 'Memuat data pemantauan...'
            : 'Loading monitoring data...',

        refresh: currentLocale === 'id'
            ? 'Perbarui'
            : 'Refresh',

        remove: currentLocale === 'id'
            ? 'Hapus'
            : 'Remove',

        gdp: currentLocale === 'id'
            ? 'PDB'
            : 'GDP',

        inflation: currentLocale === 'id'
            ? 'Inflasi'
            : 'Inflation',

        currency: currentLocale === 'id'
            ? 'Mata Uang'
            : 'Currency',

        weatherRisk: currentLocale === 'id'
            ? 'Risiko Cuaca'
            : 'Weather Risk',

        portRisk: currentLocale === 'id'
            ? 'Risiko Pelabuhan'
            : 'Port Risk',

        lastCheck: currentLocale === 'id'
            ? 'Pemeriksaan terakhir'
            : 'Last checked',

        cardFailed: currentLocale === 'id'
            ? 'Data negara ini gagal dimuat.'
            : 'Failed to load this country data.'
    };

    document.addEventListener('DOMContentLoaded', async function () {
        await loadCountries();
        await renderWatchlist();
    });

    async function loadCountries() {
        const select =
            document.getElementById('favoriteCountrySelect');

        select.disabled = true;

        try {
            const response = await fetch('/api/countries', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(text.loadFailed);
            }

            countries = Array.isArray(result.data)
                ? result.data
                : [];

            select.innerHTML = '';

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = text.selectCountry;

            select.appendChild(defaultOption);

            countries.forEach(function (country) {
                const option = document.createElement('option');
                option.value = String(country.id);
                option.textContent = country.name;

                select.appendChild(option);
            });
        } catch (error) {
            console.error(error);
            showStatus(text.loadFailed, 'danger');
        } finally {
            select.disabled = false;
        }
    }

    async function getFavoriteIdsFromDatabase() {
    const response = await fetch('/watchlist/data', {
        headers: {
            'Accept': 'application/json'
        }
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(
            result.message || text.loadFailed
        );
    }

    return result.data.map(function (watchlist) {
        return String(watchlist.country_id);
    });
}

    async function addFavorite() {
    const select =
        document.getElementById('favoriteCountrySelect');

    const button =
        document.getElementById('addFavoriteButton');

    const countryId = select.value;

    if (!countryId) {
        showStatus(text.chooseCountry, 'warning');
        select.focus();
        return;
    }

    const originalButtonText =
        button.textContent.trim();

    button.disabled = true;

    button.textContent =
        currentLocale === 'id'
            ? 'Menyimpan...'
            : 'Saving...';

    try {
        const response = await fetch('/watchlist', {
            method: 'POST',

            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },

            body: JSON.stringify({
                country_id: Number(countryId)
            })
        });

        const result = await response.json();

        if (response.status === 409) {
            showStatus(
                text.alreadyAdded,
                'warning'
            );

            return;
        }

        if (!response.ok || !result.success) {
            throw new Error(
                result.message || text.loadFailed
            );
        }

        select.value = '';

        showStatus(
            text.added,
            'success'
        );

        await renderWatchlist();

    } catch (error) {
        console.error(error);

        showStatus(
            error.message || text.loadFailed,
            'danger'
        );
    } finally {
        button.disabled = false;
        button.textContent = originalButtonText;
    }
}

    async function removeFavorite(countryId) {
    try {
        const response = await fetch(
            `/watchlist/${countryId}`,
            {
                method: 'DELETE',

                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            }
        );

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(
                result.message || text.loadFailed
            );
        }

        showStatus(
            text.removed,
            'success'
        );

        await renderWatchlist();

    } catch (error) {
        console.error(error);

        showStatus(
            error.message || text.loadFailed,
            'danger'
        );
    }
}

    async function renderWatchlist() {
        const container =
            document.getElementById('watchlistContent');

        let favoriteIds = [];

try {
    favoriteIds = await getFavoriteIdsFromDatabase();
} catch (error) {
    console.error(error);

    showStatus(
        error.message || text.loadFailed,
        'danger'
    );
}

        document.getElementById('totalFavoriteValue').textContent =
            favoriteIds.length;

        if (favoriteIds.length === 0) {
            document.getElementById('highRiskValue').textContent = '0';
            document.getElementById('lastUpdatedValue').textContent = '-';

            container.innerHTML = `
                <div class="empty-state">
                    <div class="fs-1 mb-3">☆</div>

                    <h5 class="fw-bold">
                        ${escapeHtml(text.noFavorite)}
                    </h5>

                    <p class="text-muted mb-0">
                        ${escapeHtml(text.noFavoriteDescription)}
                    </p>
                </div>
            `;

            return;
        }

        container.innerHTML = `
            <div class="row g-4">
                ${favoriteIds.map(function () {
                    return `
                        <div class="col-md-6 col-xl-4">
                            <div class="loading-card"></div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;

        const results = await Promise.all(
            favoriteIds.map(function (countryId) {
                return fetchCountryMonitoring(countryId);
            })
        );

        let highRiskCount = 0;

        results.forEach(function (result) {
            if (
                result.success &&
                Number(result.risk.total_score) >= 50
            ) {
                highRiskCount++;
            }
        });

        document.getElementById('highRiskValue').textContent =
            highRiskCount;

        document.getElementById('lastUpdatedValue').textContent =
            formatDateTime(new Date());

        container.innerHTML = `
            <div class="row g-4">
                ${results.map(function (result, index) {
                    const countryId = favoriteIds[index];

                    if (!result.success) {
                        return renderFailedCard(countryId);
                    }

                    return renderCountryCard(result);
                }).join('')}
            </div>
        `;
    }

    async function fetchCountryMonitoring(countryId) {
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
                !responses.every(function (response) {
                    return response.ok;
                }) ||
                !countryResult.success ||
                !currencyResult.success ||
                !riskResult.success
            ) {
                throw new Error(text.cardFailed);
            }

            return {
                success: true,
                country: countryResult.data,
                currency: currencyResult.data,
                risk: riskResult.data
            };
        } catch (error) {
            console.error(error);

            return {
                success: false,
                countryId: countryId
            };
        }
    }

    function renderCountryCard(result) {
        const country = result.country;
        const currency = result.currency;
        const risk = result.risk;

        const weatherRisk =
            risk.components.weather_risk?.score ?? 0;

        const portRisk =
            risk.components.port_risk?.score ?? 0;

        return `
            <div class="col-md-6 col-xl-4">
                <article class="watch-card">
                    <header class="watch-card-header">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <span class="country-code">
                                    ${escapeHtml(country.iso2 || country.currency_code || '-')}
                                </span>

                                <h4 class="fw-bold mt-2 mb-1">
                                    ${escapeHtml(country.name)}
                                </h4>

                                <small class="text-muted">
                                    ${escapeHtml(country.currency_code || currency.currency_code || '-')}
                                </small>
                            </div>

                            <div class="text-end">
                                <div class="risk-score">
                                    ${Number(risk.total_score) || 0}
                                </div>

                                <small class="text-muted">
                                    /100
                                </small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <span class="risk-badge ${getRiskClass(risk.total_score)}">
                                ${escapeHtml(translateRiskLabel(risk.risk_label))}
                            </span>
                        </div>
                    </header>

                    <div class="watch-card-body">
                        <div class="metric-row">
                            <span class="metric-label">
                                ${escapeHtml(text.gdp)}
                            </span>

                            <span class="metric-value">
                                ${formatNumber(country.gdp_usd_billion)} B USD
                            </span>
                        </div>

                        <div class="metric-row">
                            <span class="metric-label">
                                ${escapeHtml(text.inflation)}
                            </span>

                            <span class="metric-value">
                                ${formatNumber(country.inflation_rate)}%
                            </span>
                        </div>

                        <div class="metric-row">
                            <span class="metric-label">
                                ${escapeHtml(text.currency)}
                            </span>

                            <span class="metric-value">
                                ${escapeHtml(currency.currency_code || '-')}
                            </span>
                        </div>

                        <div class="metric-row">
                            <span class="metric-label">
                                ${escapeHtml(text.weatherRisk)}
                            </span>

                            <span class="metric-value">
                                ${Number(weatherRisk) || 0}/100
                            </span>
                        </div>

                        <div class="metric-row">
                            <span class="metric-label">
                                ${escapeHtml(text.portRisk)}
                            </span>

                            <span class="metric-value">
                                ${Number(portRisk) || 0}/100
                            </span>
                        </div>

                        <p class="text-muted small mt-3 mb-3">
                            ${escapeHtml(text.lastCheck)}:
                            ${escapeHtml(formatDateTime(new Date()))}
                        </p>

                        <div class="d-flex gap-2">
                            <button
                                type="button"
                                class="btn btn-outline-primary flex-fill"
                                onclick="renderWatchlist()"
                            >
                                ${escapeHtml(text.refresh)}
                            </button>

                            <button
                                type="button"
                                class="btn btn-outline-danger flex-fill"
                                onclick="removeFavorite('${escapeJs(country.id)}')"
                            >
                                ${escapeHtml(text.remove)}
                            </button>
                        </div>
                    </div>
                </article>
            </div>
        `;
    }

    function renderFailedCard(countryId) {
        return `
            <div class="col-md-6 col-xl-4">
                <article class="watch-card">
                    <div class="watch-card-body">
                        <div class="alert alert-danger">
                            ${escapeHtml(text.cardFailed)}
                        </div>

                        <button
                            type="button"
                            class="btn btn-outline-danger w-100"
                            onclick="removeFavorite('${escapeJs(countryId)}')"
                        >
                            ${escapeHtml(text.remove)}
                        </button>
                    </div>
                </article>
            </div>
        `;
    }

    function getRiskClass(score) {
        const numericScore = Number(score) || 0;

        if (numericScore <= 24) {
            return 'risk-low';
        }

        if (numericScore <= 49) {
            return 'risk-moderate';
        }

        if (numericScore <= 74) {
            return 'risk-high';
        }

        return 'risk-critical';
    }

    function translateRiskLabel(label) {
        if (currentLocale !== 'id') {
            return label || '-';
        }

        const translations = {
            'Low Risk': 'Risiko Rendah',
            'Moderate Risk': 'Risiko Sedang',
            'High Risk': 'Risiko Tinggi',
            'Critical Risk': 'Risiko Kritis'
        };

        return translations[label] || label || '-';
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

    function formatDateTime(date) {
        return new Intl.DateTimeFormat(
            currentLocale === 'id'
                ? 'id-ID'
                : 'en-US',
            {
                dateStyle: 'medium',
                timeStyle: 'short'
            }
        ).format(date);
    }

    function showStatus(message, type) {
        const element =
            document.getElementById('statusMessage');

        element.textContent = message;
        element.className =
            `alert alert-${type} status-message mt-3 mb-0`;

        element.classList.remove('d-none');
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function escapeJs(value) {
        return String(value ?? '')
            .replaceAll('\\', '\\\\')
            .replaceAll("'", "\\'");
    }
</script>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>
@include('user.partials.automatic-translation')
</body>
</html>
