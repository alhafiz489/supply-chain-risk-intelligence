<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">

    <title>
        {{ app()->getLocale() === 'id'
            ? 'Dashboard Admin - SupplyGuard'
            : 'Admin Dashboard - SupplyGuard'
        }}
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="{{ asset('css/supplyguard-professional.css') }}">
</head>

<body class="sg-admin-body">

@include('admin.partials.sidebar')
<div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar(false)"></div>

<div class="main-content">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button type="button"
                    class="btn btn-outline-secondary mobile-menu-button"
                    onclick="toggleSidebar(true)"
                    aria-label="Buka menu administrator">
                ☰
            </button>

            <div>
                <h2 class="topbar-title">
                    {{ app()->getLocale() === 'id'
                        ? 'Dashboard Administrator'
                        : 'Administrator Dashboard'
                    }}
                </h2>
                <div class="topbar-caption">
                    {{ app()->getLocale() === 'id'
                        ? 'Pusat kendali dan pemantauan SupplyGuard'
                        : 'SupplyGuard monitoring and control center'
                    }}
                </div>
            </div>
        </div>

        <div class="topbar-actions">
            <a href="{{ route('dashboard') }}" class="topbar-soft-button">
                <span>↗</span>
                <span class="button-label">
                    {{ app()->getLocale() === 'id' ? 'Lihat Web' : 'View Site' }}
                </span>
            </a>

            <div class="dropdown">
                <button class="topbar-soft-button dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    {{ strtoupper(app()->getLocale()) }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                    <li>
                        <a class="dropdown-item py-2"
                           href="{{ route('language.switch', ['locale' => 'id']) }}">
                            🇮🇩 Bahasa Indonesia
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2"
                           href="{{ route('language.switch', ['locale' => 'en']) }}">
                            🇬🇧 English
                        </a>
                    </li>
                </ul>
            </div>

            <div class="admin-profile">
                <span class="admin-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <div class="admin-profile-text">
                    <p class="admin-name">{{ auth()->user()->name }}</p>
                    <p class="admin-role">Administrator</p>
                </div>
            </div>
        </div>
    </header>

    <main class="page-wrapper">
        <section class="welcome-card mb-4">
            <div class="welcome-grid">
                <div>
                    <span class="welcome-kicker">
                        ● {{ app()->getLocale() === 'id' ? 'Sistem Aktif' : 'System Online' }}
                    </span>
                    <h1>
                        {{ app()->getLocale() === 'id'
                            ? 'Selamat Datang di Pusat Kendali'
                            : 'Welcome to the Control Center'
                        }}
                    </h1>
                    <p>
                        {{ app()->getLocale() === 'id'
                            ? 'Pantau kualitas data global, pengguna, pelabuhan, berita, dan perhitungan risiko dari satu dashboard yang terintegrasi.'
                            : 'Monitor global data quality, users, ports, news, and risk calculations from one integrated dashboard.'
                        }}
                    </p>
                </div>

                <div class="welcome-meta">
                    <div class="welcome-meta-item">
                        <span>◷</span>
                        <div>
                            <div class="small text-white-50">
                                {{ app()->getLocale() === 'id' ? 'Zona Waktu Sistem' : 'System Timezone' }}
                            </div>
                            <strong>Asia/Jakarta</strong>
                        </div>
                    </div>
                    <div class="welcome-meta-item">
                        <span>↻</span>
                        <div>
                            <div class="small text-white-50">
                                {{ app()->getLocale() === 'id' ? 'Pembaruan Data' : 'Data Updates' }}
                            </div>
                            <strong>
                                {{ app()->getLocale() === 'id' ? 'Scheduler Otomatis Aktif' : 'Automated Scheduler Active' }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="alert alert-success mt-4 mb-0">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mt-4 mb-0">
                {{ session('error') }}
            </div>
        @endif

        <section class="admin-operations" aria-label="Operasi dan sinkronisasi data">
            <div class="admin-operation-item">
                @include('admin.partials.risk-recalculate-button')
            </div>
            <div class="admin-operation-item">
                @include('admin.partials.weather-sync-card')
            </div>
            <div class="admin-operation-item">
                @include('admin.partials.economic-sync-card')
            </div>
            <div class="admin-operation-item">
                @include('admin.partials.currency-sync-card')
            </div>
            <div class="admin-operation-item">
                @include('admin.partials.global-news-sync-card')
            </div>
            <div class="admin-operation-item">
                @include('admin.partials.global-port-sync-card')
            </div>
        </section>

        <section class="data-quality-panel mt-4 mb-4">
            <div
                class="d-flex flex-column flex-xl-row align-items-xl-start justify-content-between gap-3 mb-4"
            >
                <div>
                    <h3 class="data-quality-title">
                        {{ app()->getLocale() === 'id'
                            ? 'Kelengkapan Data Risiko Global'
                            : 'Global Risk Data Completeness'
                        }}
                    </h3>

                    <p class="data-quality-description">
                        {{ app()->getLocale() === 'id'
                            ? 'Status ini menunjukkan berapa banyak komponen yang benar-benar tersedia untuk menghitung risiko setiap negara.'
                            : 'This status shows how many components are genuinely available for each country risk calculation.'
                        }}
                    </p>
                </div>

                <span class="data-status-badge data-status-partial">
                    {{ app()->getLocale() === 'id'
                        ? 'Skor parsial bukan kegagalan sistem'
                        : 'Partial scores are not system failures'
                    }}
                </span>
            </div>

            <div class="row g-3">
                <div class="col-sm-6 col-xl-3">
                    <div class="quality-card quality-ready">
                        <div class="quality-card-label">
                            {{ app()->getLocale() === 'id'
                                ? 'Data Lengkap'
                                : 'Complete Data'
                            }}
                        </div>

                        <p class="quality-card-value">
                            {{ number_format($statistics['ready_countries']) }}
                        </p>

                        <small class="text-muted">
                            {{ app()->getLocale() === 'id'
                                ? 'Semua komponen tersedia'
                                : 'All components available'
                            }}
                        </small>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="quality-card quality-partial">
                        <div class="quality-card-label">
                            {{ app()->getLocale() === 'id'
                                ? 'Data Parsial'
                                : 'Partial Data'
                            }}
                        </div>

                        <p class="quality-card-value">
                            {{ number_format($statistics['partial_countries']) }}
                        </p>

                        <small class="text-muted">
                            {{ app()->getLocale() === 'id'
                                ? 'Sebagian komponen tersedia'
                                : 'Some components available'
                            }}
                        </small>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="quality-card quality-unavailable">
                        <div class="quality-card-label">
                            {{ app()->getLocale() === 'id'
                                ? 'Belum Tersedia'
                                : 'Unavailable'
                            }}
                        </div>

                        <p class="quality-card-value">
                            {{ number_format($statistics['unavailable_countries']) }}
                        </p>

                        <small class="text-muted">
                            {{ app()->getLocale() === 'id'
                                ? 'Belum dapat dinilai'
                                : 'Not yet assessable'
                            }}
                        </small>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="quality-card quality-average">
                        <div class="quality-card-label">
                            {{ app()->getLocale() === 'id'
                                ? 'Rata-rata Kelengkapan'
                                : 'Average Completeness'
                            }}
                        </div>

                        <p class="quality-card-value">
                            {{ number_format(
                                $statistics['average_data_completeness'],
                                1
                            ) }}%
                        </p>

                        <div class="quality-progress mt-2">
                            <div
                                class="quality-progress-bar"
                                style="width: {{
                                    min(
                                        100,
                                        max(
                                            0,
                                            $statistics[
                                                'average_data_completeness'
                                            ]
                                        )
                                    )
                                }}%"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="quality-note mt-3">
                <strong>
                    {{ app()->getLocale() === 'id'
                        ? 'Catatan:'
                        : 'Note:'
                    }}
                </strong>

                {{ app()->getLocale() === 'id'
                    ? 'Data UN/LOCODE hanya menjadi referensi lokasi pelabuhan. Komponen pelabuhan baru dinilai ketika tersedia data operasional kemacetan atau keterlambatan.'
                    : 'UN/LOCODE data is used only as a port location reference. The port component is assessed only when congestion or delay operational data is available.'
                }}
            </div>
        </section>

        <section class="row g-3">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <span class="stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>

                    <div class="stat-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Total Pengguna'
                            : 'Total Users'
                        }}
                    </div>

                    <h3 class="stat-number">
                        {{ number_format($statistics['total_users']) }}
                    </h3>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <span class="stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>

                    <div class="stat-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Pengguna Aktif'
                            : 'Active Users'
                        }}
                    </div>

                    <h3 class="stat-number">
                        {{ number_format($statistics['active_users']) }}
                    </h3>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <span class="stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3Z" stroke="currentColor" stroke-width="1.8"/></svg></span>

                    <div class="stat-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Total Negara'
                            : 'Total Countries'
                        }}
                    </div>

                    <h3 class="stat-number">
                        {{ number_format($statistics['total_countries']) }}
                    </h3>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <span class="stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v17m-4-9h8M9 6h6M5 14a7 7 0 0 0 14 0M3 14h4m10 0h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>

                    <div class="stat-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Total Pelabuhan'
                            : 'Total Ports'
                        }}
                    </div>

                    <h3 class="stat-number">
                        {{ number_format($statistics['total_ports']) }}
                    </h3>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <span class="stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h14v16H5V4Zm3 4h8M8 12h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>

                    <div class="stat-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Total Berita'
                            : 'Total News'
                        }}
                    </div>

                    <h3 class="stat-number">
                        {{ number_format($statistics['total_news']) }}
                    </h3>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <span class="stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3 3.8 7v5c0 5 3.5 8 8.2 9 4.7-1 8.2-4 8.2-9V7L12 3Zm0 5v5m0 4h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>

                    <div class="stat-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Perhitungan Risiko'
                            : 'Risk Calculations'
                        }}
                    </div>

                    <h3 class="stat-number">
                        {{ number_format($statistics['total_risk_scores']) }}
                    </h3>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <span class="stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3l-5.6 2.9 1.1-6.2L3 9.6l6.2-.9L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span>

                    <div class="stat-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Total Favorit'
                            : 'Total Favorites'
                        }}
                    </div>

                    <h3 class="stat-number">
                        {{ number_format($statistics['total_watchlists']) }}
                    </h3>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card">
                    <span class="stat-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4 3 20h18L12 4Zm0 5v5m0 3h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>

                    <div class="stat-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Negara Risiko Tinggi'
                            : 'High-Risk Countries'
                        }}
                    </div>

                    <h3 class="stat-number">
                        {{ number_format(
                            $statistics['high_risk_countries']
                        ) }}
                    </h3>
                </div>
            </div>
        </section>

        <section class="row g-4 mt-1">
            <div class="col-xl-6">
                <div class="content-card">
                    <div class="content-card-header">
                        <div>
                            <h3 class="content-card-title">
                                {{ app()->getLocale() === 'id'
                                    ? 'Pengguna Terbaru'
                                    : 'Latest Users'
                                }}
                            </h3>

                            <small class="text-muted">
                                {{ app()->getLocale() === 'id'
                                    ? 'Lima akun terbaru'
                                    : 'Five latest accounts'
                                }}
                            </small>
                        </div>
                    </div>

                    <div class="content-card-body">
                        @if ($latestUsers->isEmpty())
                            <div class="empty-state">
                                {{ app()->getLocale() === 'id'
                                    ? 'Belum ada data pengguna.'
                                    : 'No user data yet.'
                                }}
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>
                                                {{ app()->getLocale() === 'id'
                                                    ? 'Nama'
                                                    : 'Name'
                                                }}
                                            </th>

                                            <th>Email</th>

                                            <th>
                                                {{ app()->getLocale() === 'id'
                                                    ? 'Peran'
                                                    : 'Role'
                                                }}
                                            </th>

                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($latestUsers as $user)
                                            <tr>
                                                <td class="fw-semibold">
                                                    {{ $user->name }}
                                                </td>

                                                <td>
                                                    {{ $user->email }}
                                                </td>

                                                <td>
                                                    {{ ucfirst($user->role) }}
                                                </td>

                                                <td>
                                                    <span
                                                        class="status-badge {{
                                                            $user->status === 'active'
                                                                ? 'status-active'
                                                                : 'status-inactive'
                                                        }}"
                                                    >
                                                        {{ ucfirst($user->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="content-card">
                    <div class="content-card-header">
                        <div>
                            <h3 class="content-card-title">
                                {{ app()->getLocale() === 'id'
                                    ? 'Perhitungan Risiko Terbaru'
                                    : 'Latest Risk Calculations'
                                }}
                            </h3>

                            <small class="text-muted">
                                {{ app()->getLocale() === 'id'
                                    ? 'Lima hasil perhitungan terbaru'
                                    : 'Five latest calculation results'
                                }}
                            </small>
                        </div>
                    </div>

                    <div class="content-card-body">
                        @if ($latestRisks->isEmpty())
                            <div class="empty-state">
                                {{ app()->getLocale() === 'id'
                                    ? 'Belum ada perhitungan risiko.'
                                    : 'No risk calculations yet.'
                                }}
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>
                                                {{ app()->getLocale() === 'id'
                                                    ? 'Negara'
                                                    : 'Country'
                                                }}
                                            </th>

                                            <th>
                                                {{ app()->getLocale() === 'id'
                                                    ? 'Skor'
                                                    : 'Score'
                                                }}
                                            </th>

                                            <th>
                                                {{ app()->getLocale() === 'id'
                                                    ? 'Kategori'
                                                    : 'Category'
                                                }}
                                            </th>

                                            <th>
                                                {{ app()->getLocale() === 'id'
                                                    ? 'Kelengkapan'
                                                    : 'Completeness'
                                                }}
                                            </th>

                                            <th>Status Data</th>

                                            <th>
                                                {{ app()->getLocale() === 'id'
                                                    ? 'Tanggal'
                                                    : 'Date'
                                                }}
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($latestRisks as $risk)
                                            @php
                                                $score = (int) $risk->total_score;

                                                $riskClass = match (true) {
                                                    $score <= 24 => 'risk-low',
                                                    $score <= 49 => 'risk-moderate',
                                                    $score <= 74 => 'risk-high',
                                                    default => 'risk-critical',
                                                };

                                                $riskLabel = app()->getLocale() === 'id'
                                                    ? match ($risk->risk_label) {
                                                        'Low Risk' => 'Risiko Rendah',
                                                        'Moderate Risk' => 'Risiko Sedang',
                                                        'High Risk' => 'Risiko Tinggi',
                                                        'Critical Risk' => 'Risiko Kritis',
                                                        default => $risk->risk_label,
                                                    }
                                                    : $risk->risk_label;

                                                $dataStatus =
                                                    $risk->country?->risk_data_status
                                                    ?? 'unavailable';

                                                $completeness = (int) (
                                                    $risk->country
                                                        ?->data_completeness_percent
                                                    ?? 0
                                                );

                                                $dataStatusClass = match (
                                                    $dataStatus
                                                ) {
                                                    'ready' =>
                                                        'data-status-ready',

                                                    'partial' =>
                                                        'data-status-partial',

                                                    default =>
                                                        'data-status-unavailable',
                                                };

                                                $dataStatusLabel =
                                                    app()->getLocale() === 'id'
                                                        ? match ($dataStatus) {
                                                            'ready' =>
                                                                'Lengkap',

                                                            'partial' =>
                                                                'Parsial',

                                                            default =>
                                                                'Belum Tersedia',
                                                        }
                                                        : match ($dataStatus) {
                                                            'ready' =>
                                                                'Complete',

                                                            'partial' =>
                                                                'Partial',

                                                            default =>
                                                                'Unavailable',
                                                        };
                                            @endphp

                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">
                                                        {{ $risk->country?->name ?? '-' }}
                                                    </div>

                                                    <small class="text-muted">
                                                        {{ $risk->country?->iso2 ?? '-' }}
                                                    </small>
                                                </td>

                                                <td class="fw-semibold">
                                                    {{ $risk->total_score }}/100
                                                </td>

                                                <td>
                                                    <span class="risk-badge {{ $riskClass }}">
                                                        {{ $riskLabel }}
                                                    </span>
                                                </td>

                                                <td class="completeness-cell">
                                                    <div
                                                        class="d-flex justify-content-between gap-2 mb-1"
                                                    >
                                                        <small class="text-muted">
                                                            {{ app()->getLocale() === 'id'
                                                                ? 'Data tersedia'
                                                                : 'Available data'
                                                            }}
                                                        </small>

                                                        <small class="fw-bold">
                                                            {{ $completeness }}%
                                                        </small>
                                                    </div>

                                                    <div class="completeness-mini-progress">
                                                        <div
                                                            class="completeness-mini-bar"
                                                            style="width: {{
                                                                min(
                                                                    100,
                                                                    max(
                                                                        0,
                                                                        $completeness
                                                                    )
                                                                )
                                                            }}%"
                                                        ></div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <span
                                                        class="data-status-badge {{ $dataStatusClass }}"
                                                    >
                                                        {{ $dataStatusLabel }}
                                                    </span>
                                                </td>

                                                <td class="text-nowrap">
                                                    {{ optional($risk->created_at)
                                                        ->format('d/m/Y H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    function toggleSidebar(forceOpen = null) {
        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        const shouldOpen = forceOpen === null
            ? !sidebar.classList.contains('show')
            : forceOpen;

        sidebar.classList.toggle('show', shouldOpen);
        backdrop.classList.toggle('show', shouldOpen);
        document.body.classList.toggle('sidebar-open', shouldOpen);
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {
            toggleSidebar(false);
        }
    });
</script>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>
</html>