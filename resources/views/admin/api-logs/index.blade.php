<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ app()->getLocale() === 'id' ? 'Log API - SupplyGuard' : 'API Logs - SupplyGuard' }}</title>
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

        * { box-sizing: border-box; }

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
            border-radius: 13px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            font-weight: 800;
        }

        .brand-title { margin: 0; font-size: 18px; font-weight: 800; }
        .brand-subtitle { margin: 0; color: #94a3b8; font-size: 12px; }

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

        .sidebar-icon { width: 28px; text-align: center; font-size: 17px; }
        .logout-form { margin-top: 24px; }

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

        .main-content {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 900;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 78px;
            padding: 16px 32px;
            border-bottom: 1px solid #e5e7eb;
            background: rgba(255, 255, 255, 0.95);
        }

        .topbar-title { margin: 0; font-size: 20px; font-weight: 800; }

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

        .page-wrapper { padding: 32px; }
        .page-title { margin-bottom: 6px; font-size: 30px; font-weight: 800; }

        .stat-card,
        .filter-card,
        .content-card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: white;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }

        .stat-card { height: 100%; padding: 20px; }
        .stat-label { color: var(--muted); font-size: 12px; font-weight: 700; }
        .stat-number { margin: 8px 0 0; font-size: 28px; font-weight: 800; }
        .filter-card { margin-top: 22px; padding: 22px; }
        .content-card { margin-top: 22px; overflow: hidden; }

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

        .table { margin-bottom: 0; }

        .table th {
            padding: 14px 16px;
            background: #f8fafc;
            color: var(--muted);
            font-size: 12px;
            white-space: nowrap;
        }

        .table td {
            padding: 15px 16px;
            vertical-align: middle;
            font-size: 14px;
        }

        .method-badge,
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
        }

        .method-get { background: #dbeafe; color: #1d4ed8; }
        .method-post { background: #dcfce7; color: #166534; }
        .method-put,
        .method-patch { background: #fef3c7; color: #92400e; }
        .method-delete { background: #fee2e2; color: #991b1b; }
        .status-success { background: #dcfce7; color: #166534; }
        .status-client { background: #fef3c7; color: #92400e; }
        .status-server { background: #fee2e2; color: #991b1b; }

        .endpoint {
            min-width: 240px;
            font-family: Consolas, monospace;
            font-size: 13px;
        }

        .action-column {
            position: sticky;
            right: 0;
            z-index: 2;
            background: white;
            box-shadow: -8px 0 16px rgba(15, 23, 42, 0.05);
        }

        thead .action-column {
            z-index: 3;
            background: #f8fafc;
        }

        .pagination-wrapper {
            padding: 18px 22px;
            border-top: 1px solid #e5e7eb;
        }

        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: var(--muted);
        }

        .mobile-menu-button { display: none; }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform .25s ease;
            }

            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-menu-button { display: inline-flex; }
            .topbar { padding: 14px 20px; }
            .page-wrapper { padding: 22px 18px; }
        }

        @media (max-width: 575.98px) {
            .admin-profile-text { display: none; }
            .content-card-header { align-items: flex-start; flex-direction: column; }
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
                <h2 class="topbar-title">{{ app()->getLocale() === 'id' ? 'Log Aktivitas API' : 'API Activity Logs' }}</h2>
                <small class="text-muted">{{ app()->getLocale() === 'id' ? 'Pantau permintaan, status, dan waktu respons API' : 'Monitor API requests, statuses, and response times' }}</small>
            </div>
        </div>

        <div class="admin-profile">
            <span class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <div class="admin-profile-text">
                <div class="fw-bold">{{ auth()->user()->name }}</div>
                <small class="text-muted">Administrator</small>
            </div>
        </div>
    </header>

    <main class="page-wrapper">
        <section>
            <h1 class="page-title">{{ app()->getLocale() === 'id' ? 'Riwayat Akses API' : 'API Access History' }}</h1>
            <p class="text-muted mb-0">{{ app()->getLocale() === 'id' ? 'Setiap permintaan API dicatat untuk membantu pemantauan dan pemeriksaan kesalahan.' : 'Every API request is recorded to support monitoring and error investigation.' }}</p>
        </section>

        <section class="row g-3 mt-2">
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-label">{{ app()->getLocale() === 'id' ? 'Total Permintaan' : 'Total Requests' }}</div>
                    <h3 class="stat-number">{{ number_format($statistics['total']) }}</h3>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-label">{{ app()->getLocale() === 'id' ? 'Berhasil' : 'Successful' }}</div>
                    <h3 class="stat-number">{{ number_format($statistics['success']) }}</h3>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-label">4xx Client Error</div>
                    <h3 class="stat-number">{{ number_format($statistics['client_error']) }}</h3>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-label">5xx Server Error</div>
                    <h3 class="stat-number">{{ number_format($statistics['server_error']) }}</h3>
                </div>
            </div>
        </section>

        <section class="filter-card">
            <form method="GET" action="{{ route('admin.api-logs.index') }}">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label for="search" class="form-label fw-semibold">{{ app()->getLocale() === 'id' ? 'Pencarian' : 'Search' }}</label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="{{ app()->getLocale() === 'id' ? 'Endpoint, route, atau alamat IP...' : 'Endpoint, route, or IP address...' }}"
                        >
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label for="method" class="form-label fw-semibold">Method</label>
                        <select id="method" name="method" class="form-select">
                            <option value="">All</option>
                            @foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $item)
                                <option value="{{ $item }}" @selected($method === $item)>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label for="status_group" class="form-label fw-semibold">Status</label>
                        <select id="status_group" name="status_group" class="form-select">
                            <option value="">All</option>
                            <option value="success" @selected($statusGroup === 'success')>2xx/3xx</option>
                            <option value="client_error" @selected($statusGroup === 'client_error')>4xx</option>
                            <option value="server_error" @selected($statusGroup === 'server_error')>5xx</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label for="date_from" class="form-label fw-semibold">{{ app()->getLocale() === 'id' ? 'Dari' : 'From' }}</label>
                        <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label for="date_to" class="form-label fw-semibold">{{ app()->getLocale() === 'id' ? 'Sampai' : 'To' }}</label>
                        <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        @if ($search !== '' || $method !== '' || $statusGroup !== '' || $dateFrom !== '' || $dateTo !== '')
                            <a href="{{ route('admin.api-logs.index') }}" class="btn btn-outline-secondary px-4">
                                {{ app()->getLocale() === 'id' ? 'Reset' : 'Reset' }}
                            </a>
                        @endif

                        <button type="submit" class="btn btn-primary px-4">
                            {{ app()->getLocale() === 'id' ? 'Terapkan Filter' : 'Apply Filters' }}
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <section class="content-card">
            <div class="content-card-header">
                <div>
                    <h3 class="h6 fw-bold mb-1">{{ app()->getLocale() === 'id' ? 'Daftar Log API' : 'API Log List' }}</h3>
                    <small class="text-muted">{{ app()->getLocale() === 'id' ? 'Data ditemukan:' : 'Records found:' }} {{ $logs->total() }}</small>
                </div>
            </div>

            @if ($logs->isEmpty())
                <div class="empty-state">
                    <div class="fs-1 mb-3">⌁</div>
                    <h5 class="fw-bold">{{ app()->getLocale() === 'id' ? 'Belum ada log API' : 'No API logs yet' }}</h5>
                    <p class="mb-0">{{ app()->getLocale() === 'id' ? 'Akses salah satu endpoint API untuk membuat log pertama.' : 'Access an API endpoint to create the first log.' }}</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Method</th>
                                <th>Endpoint</th>
                                <th>Status</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Waktu Respons' : 'Response Time' }}</th>
                                <th>IP</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Waktu' : 'Time' }}</th>
                                <th class="action-column">{{ app()->getLocale() === 'id' ? 'Aksi' : 'Action' }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($logs as $log)
                                @php
                                    $methodClass = 'method-'.strtolower($log->method);

                                    $statusClass = match (true) {
                                        $log->status_code >= 500 => 'status-server',
                                        $log->status_code >= 400 => 'status-client',
                                        default => 'status-success',
                                    };
                                @endphp

                                <tr>
                                    <td>{{ $logs->firstItem() + $loop->index }}</td>
                                    <td><span class="method-badge {{ $methodClass }}">{{ $log->method }}</span></td>
                                    <td class="endpoint">{{ $log->endpoint }}</td>
                                    <td><span class="status-badge {{ $statusClass }}">{{ $log->status_code }}</span></td>
                                    <td>{{ number_format($log->response_time_ms) }} ms</td>
                                    <td>{{ $log->ip_address ?? '-' }}</td>
                                    <td class="text-nowrap">{{ optional($log->created_at)->format('d/m/Y H:i:s') }}</td>
                                    <td class="action-column">
                                        <a
                                            href="{{ route('admin.api-logs.show', $log) }}"
                                            class="btn btn-outline-primary btn-sm fw-semibold"
                                        >
                                            {{ app()->getLocale() === 'id' ? 'Detail' : 'Details' }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($logs->hasPages())
                    <div class="pagination-wrapper">
                        {{ $logs->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </section>
    </main>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('adminSidebar').classList.toggle('show');
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>