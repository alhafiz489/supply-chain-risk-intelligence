<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">

    <title>
        {{ app()->getLocale() === 'id'
            ? 'Riwayat Risiko - SupplyGuard'
            : 'Risk History - SupplyGuard'
        }}
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        :root {
            --sidebar-width: 270px;
            --muted: #64748b;
            --page-background: #f4f7fb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--page-background);
            color: #1f2937;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1000;
            width: var(--sidebar-width);
            padding: 24px 18px;
            overflow-y: auto;
            background: linear-gradient(180deg, #111827, #172033);
            color: white;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
            padding: 0 8px;
            color: white;
            text-decoration: none;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: inline-grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 13px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            font-weight: 800;
        }

        .brand-title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
        }

        .brand-subtitle {
            margin: 0;
            color: #94a3b8;
            font-size: 12px;
        }

        .menu-label {
            margin: 20px 10px 8px;
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 6px;
            padding: 12px 14px;
            border-radius: 12px;
            color: #cbd5e1;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(59, 130, 246, 0.18);
            color: white;
        }

        .sidebar-icon {
            width: 28px;
            text-align: center;
            font-size: 17px;
        }

        .logout-form {
            margin-top: 24px;
        }

        .logout-button {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            background: transparent;
            color: white;
            font-weight: 700;
            text-align: left;
        }

        .logout-button:hover {
            background: rgba(239, 68, 68, 0.16);
        }

        .main-content {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 900;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            min-height: 78px;
            padding: 16px 32px;
            border-bottom: 1px solid #e5e7eb;
            background: rgba(255, 255, 255, 0.95);
        }

        .topbar-title {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-avatar {
            width: 42px;
            height: 42px;
            display: inline-grid;
            place-items: center;
            border-radius: 13px;
            background: #dbeafe;
            color: #1d4ed8;
            font-weight: 800;
        }

        .page-wrapper {
            padding: 32px;
        }

        .page-title {
            margin-bottom: 6px;
            font-size: 30px;
            font-weight: 800;
        }

        .stat-card {
            height: 100%;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: white;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }

        .stat-label {
            margin-bottom: 8px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .stat-number {
            margin: 0;
            font-size: 27px;
            font-weight: 800;
        }

        .filter-card,
        .content-card {
            margin-top: 22px;
            border: 1px solid #e5e7eb;
            border-radius: 19px;
            background: white;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .filter-card {
            padding: 22px;
        }

        .form-control,
        .form-select {
            min-height: 48px;
            border-radius: 12px;
        }

        .content-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 22px;
            border-bottom: 1px solid #e5e7eb;
        }

        .content-card-title {
            margin: 0;
            font-size: 17px;
            font-weight: 800;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            padding: 14px 16px;
            border-bottom-color: #e5e7eb;
            background: #f8fafc;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .table td {
            padding: 15px 16px;
            border-bottom-color: #edf0f4;
            vertical-align: middle;
            font-size: 14px;
        }

        .risk-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
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

        .component-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(65px, 1fr));
            gap: 7px;
            min-width: 390px;
        }

        .component-item {
            padding: 7px 8px;
            border-radius: 9px;
            background: #f8fafc;
            text-align: center;
        }

        .component-label {
            display: block;
            color: var(--muted);
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .component-score {
            display: block;
            margin-top: 2px;
            font-size: 13px;
            font-weight: 800;
        }

        .recommendation {
            max-width: 260px;
            color: #475569;
            line-height: 1.45;
        }

        .recalculate-button {
            min-width: 108px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 700;
        }

        .action-column {
            position: sticky;
            right: 0;
            z-index: 3;
            min-width: 145px;
            background: white;
            box-shadow: -10px 0 18px rgba(15, 23, 42, 0.06);
        }

        thead .action-column {
            z-index: 4;
            background: #f8fafc;
        }

        .table-scroll-hint {
            padding: 10px 16px;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
            color: var(--muted);
            font-size: 12px;
        }

        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: var(--muted);
        }

        .pagination-wrapper {
            padding: 18px 22px;
            border-top: 1px solid #e5e7eb;
        }

        .mobile-menu-button {
            display: none;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-button {
                display: inline-flex;
            }

            .topbar {
                padding: 14px 20px;
            }

            .page-wrapper {
                padding: 22px 18px;
            }
        }

        @media (max-width: 575.98px) {
            .admin-profile-text {
                display: none;
            }

            .content-card-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

@include('admin.partials.sidebar')

<div class="main-content">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button
                type="button"
                class="btn btn-outline-secondary mobile-menu-button"
                onclick="toggleSidebar()"
            >
                ☰
            </button>

            <div>
                <h2 class="topbar-title">
                    {{ app()->getLocale() === 'id' ? 'Riwayat Risiko Negara' : 'Country Risk History' }}
                </h2>

                <small class="text-muted">
                    {{ app()->getLocale() === 'id'
                        ? 'Pantau hasil dan komponen perhitungan risiko'
                        : 'Monitor risk calculation results and components'
                    }}
                </small>
            </div>
        </div>

        <div class="admin-profile">
            <span class="admin-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>

            <div class="admin-profile-text">
                <div class="fw-bold">{{ auth()->user()->name }}</div>
                <small class="text-muted">Administrator</small>
            </div>
        </div>
    </header>

    <main class="page-wrapper">
        <section>
            <h1 class="page-title">
                {{ app()->getLocale() === 'id' ? 'Riwayat Perhitungan Risiko' : 'Risk Calculation History' }}
            </h1>

            <p class="text-muted mb-0">
                {{ app()->getLocale() === 'id'
                    ? 'Setiap perhitungan disimpan sebagai riwayat dan tidak menimpa hasil sebelumnya.'
                    : 'Each calculation is stored as history and does not overwrite previous results.'
                }}
            </p>
        </section>

        @if (session('success'))
            <div class="alert alert-success mt-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mt-4">
                {{ session('error') }}
            </div>
        @endif

        <section class="row g-3 mt-2">
            <div class="col-6 col-md">
                <div class="stat-card">
                    <div class="stat-label">{{ app()->getLocale() === 'id' ? 'Total Riwayat' : 'Total History' }}</div>
                    <h3 class="stat-number">{{ number_format($statistics['total']) }}</h3>
                </div>
            </div>

            <div class="col-6 col-md">
                <div class="stat-card">
                    <div class="stat-label">{{ app()->getLocale() === 'id' ? 'Risiko Rendah' : 'Low Risk' }}</div>
                    <h3 class="stat-number">{{ number_format($statistics['low']) }}</h3>
                </div>
            </div>

            <div class="col-6 col-md">
                <div class="stat-card">
                    <div class="stat-label">{{ app()->getLocale() === 'id' ? 'Risiko Sedang' : 'Moderate Risk' }}</div>
                    <h3 class="stat-number">{{ number_format($statistics['moderate']) }}</h3>
                </div>
            </div>

            <div class="col-6 col-md">
                <div class="stat-card">
                    <div class="stat-label">{{ app()->getLocale() === 'id' ? 'Risiko Tinggi' : 'High Risk' }}</div>
                    <h3 class="stat-number">{{ number_format($statistics['high']) }}</h3>
                </div>
            </div>

            <div class="col-6 col-md">
                <div class="stat-card">
                    <div class="stat-label">{{ app()->getLocale() === 'id' ? 'Risiko Kritis' : 'Critical Risk' }}</div>
                    <h3 class="stat-number">{{ number_format($statistics['critical']) }}</h3>
                </div>
            </div>
        </section>

        <section class="filter-card">
            <form method="GET" action="{{ route('admin.risks.index') }}">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label for="search" class="form-label fw-semibold">
                            {{ app()->getLocale() === 'id' ? 'Cari Negara' : 'Search Country' }}
                        </label>

                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="{{ app()->getLocale() === 'id' ? 'Nama atau kode negara...' : 'Country name or code...' }}"
                        >
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label for="risk_label" class="form-label fw-semibold">
                            {{ app()->getLocale() === 'id' ? 'Kategori Risiko' : 'Risk Category' }}
                        </label>

                        <select id="risk_label" name="risk_label" class="form-select">
                            <option value="">{{ app()->getLocale() === 'id' ? 'Semua' : 'All' }}</option>
                            <option value="Low Risk" @selected($riskLabel === 'Low Risk')>Low Risk</option>
                            <option value="Moderate Risk" @selected($riskLabel === 'Moderate Risk')>Moderate Risk</option>
                            <option value="High Risk" @selected($riskLabel === 'High Risk')>High Risk</option>
                            <option value="Critical Risk" @selected($riskLabel === 'Critical Risk')>Critical Risk</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label for="date_from" class="form-label fw-semibold">
                            {{ app()->getLocale() === 'id' ? 'Dari Tanggal' : 'Date From' }}
                        </label>

                        <input
                            id="date_from"
                            type="date"
                            name="date_from"
                            value="{{ $dateFrom }}"
                            class="form-control"
                        >
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label for="date_to" class="form-label fw-semibold">
                            {{ app()->getLocale() === 'id' ? 'Sampai Tanggal' : 'Date To' }}
                        </label>

                        <input
                            id="date_to"
                            type="date"
                            name="date_to"
                            value="{{ $dateTo }}"
                            class="form-control"
                        >
                    </div>

                    <div class="col-lg-2 d-flex align-items-end">
                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                            style="min-height:48px;border-radius:12px;font-weight:700;"
                        >
                            {{ app()->getLocale() === 'id' ? 'Terapkan' : 'Apply' }}
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <section class="content-card">
            <div class="content-card-header">
                <div>
                    <h3 class="content-card-title">
                        {{ app()->getLocale() === 'id' ? 'Daftar Riwayat Risiko' : 'Risk History List' }}
                    </h3>

                    <small class="text-muted">
                        {{ app()->getLocale() === 'id' ? 'Data ditemukan:' : 'Records found:' }}
                        {{ $riskScores->total() }}
                    </small>
                </div>

                @if ($search !== '' || $riskLabel !== '' || $dateFrom !== '' || $dateTo !== '')
                    <a href="{{ route('admin.risks.index') }}" class="btn btn-outline-secondary btn-sm">
                        {{ app()->getLocale() === 'id' ? 'Reset Filter' : 'Reset Filters' }}
                    </a>
                @endif
            </div>

            @if ($riskScores->isEmpty())
                <div class="empty-state">
                    <div class="fs-1 mb-3">◉</div>
                    <h5 class="fw-bold">
                        {{ app()->getLocale() === 'id' ? 'Riwayat risiko tidak ditemukan' : 'No risk history found' }}
                    </h5>
                </div>
            @else
                <div class="table-scroll-hint d-lg-none">
                    {{ app()->getLocale() === 'id'
                        ? 'Geser tabel ke samping untuk melihat seluruh kolom.'
                        : 'Swipe the table sideways to view all columns.'
                    }}
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Negara' : 'Country' }}</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Komponen Skor' : 'Score Components' }}</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Total' : 'Total' }}</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Kategori' : 'Category' }}</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Rekomendasi' : 'Recommendation' }}</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Tanggal Perhitungan' : 'Calculation Date' }}</th>
                                <th class="action-column">{{ app()->getLocale() === 'id' ? 'Aksi' : 'Action' }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($riskScores as $risk)
                                @php
                                    $score = (int) $risk->total_score;

                                    $riskClass = match (true) {
                                        $score <= 24 => 'risk-low',
                                        $score <= 49 => 'risk-moderate',
                                        $score <= 74 => 'risk-high',
                                        default => 'risk-critical',
                                    };

                                    $translatedRiskLabel = app()->getLocale() === 'id'
                                        ? match ($risk->risk_label) {
                                            'Low Risk' => 'Risiko Rendah',
                                            'Moderate Risk' => 'Risiko Sedang',
                                            'High Risk' => 'Risiko Tinggi',
                                            'Critical Risk' => 'Risiko Kritis',
                                            default => $risk->risk_label,
                                        }
                                        : $risk->risk_label;
                                @endphp

                                <tr>
                                    <td>{{ $riskScores->firstItem() + $loop->index }}</td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $risk->country?->name ?? '-' }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $risk->country?->iso2 ?? '-' }}
                                        </small>
                                    </td>

                                    <td>
                                        <div class="component-grid">
                                            <div class="component-item">
                                                <span class="component-label">{{ app()->getLocale() === 'id' ? 'Cuaca' : 'Weather' }}</span>
                                                <span class="component-score">{{ $risk->weather_risk }}</span>
                                            </div>

                                            <div class="component-item">
                                                <span class="component-label">{{ app()->getLocale() === 'id' ? 'Inflasi' : 'Inflation' }}</span>
                                                <span class="component-score">{{ $risk->inflation_risk }}</span>
                                            </div>

                                            <div class="component-item">
                                                <span class="component-label">{{ app()->getLocale() === 'id' ? 'Mata Uang' : 'Currency' }}</span>
                                                <span class="component-score">{{ $risk->currency_risk }}</span>
                                            </div>

                                            <div class="component-item">
                                                <span class="component-label">{{ app()->getLocale() === 'id' ? 'Berita' : 'News' }}</span>
                                                <span class="component-score">{{ $risk->news_risk }}</span>
                                            </div>

                                            <div class="component-item">
                                                <span class="component-label">{{ app()->getLocale() === 'id' ? 'Pelabuhan' : 'Port' }}</span>
                                                <span class="component-score">{{ $risk->port_risk }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="fw-bold">
                                        {{ $risk->total_score }}/100
                                    </td>

                                    <td>
                                        <span class="risk-badge {{ $riskClass }}">
                                            {{ $translatedRiskLabel }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="recommendation">
                                            {{ $risk->recommendation ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="text-nowrap">
                                        {{ optional($risk->created_at)->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="action-column">
                                        @if ($risk->country)
                                            <form
                                                method="POST"
                                                action="{{ route('admin.risks.recalculate-country', $risk->country) }}"
                                                onsubmit="return confirm('{{ app()->getLocale() === 'id'
                                                    ? 'Hitung ulang risiko negara ini sekarang?'
                                                    : 'Recalculate this country risk now?'
                                                }}')"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="btn btn-primary btn-sm recalculate-button"
                                                >
                                                    {{ app()->getLocale() === 'id' ? 'Hitung Ulang' : 'Recalculate' }}
                                                </button>
                                            </form>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($riskScores->hasPages())
                    <div class="pagination-wrapper">
                        {{ $riskScores->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </section>
    </main>
</div>

<script>
    function toggleSidebar() {
        document
            .getElementById('adminSidebar')
            .classList.toggle('show');
    }
</script>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>
</html>