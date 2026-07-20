<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>
        {{ app()->getLocale() === 'id'
            ? 'Kelola Pelabuhan - SupplyGuard'
            : 'Manage Ports - SupplyGuard'
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

        .summary-card,
        .filter-card,
        .content-card {
            border: 1px solid #e5e7eb;
            background: white;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }

        .summary-card {
            height: 100%;
            padding: 18px;
            border-radius: 16px;
        }

        .summary-label {
            margin-bottom: 7px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .summary-value {
            margin: 0;
            font-size: 25px;
            font-weight: 800;
        }

        .filter-card {
            margin-top: 22px;
            padding: 20px;
            border-radius: 18px;
        }

        .form-control,
        .form-select {
            min-height: 46px;
            border-radius: 11px;
        }

        .filter-button {
            min-height: 46px;
            border-radius: 11px;
            font-weight: 700;
        }

        .content-card {
            margin-top: 22px;
            border-radius: 19px;
            overflow: hidden;
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
            padding: 14px 16px;
            border-bottom-color: #edf0f4;
            vertical-align: middle;
            font-size: 13px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
        }

        .badge-reference {
            background: #e0f2fe;
            color: #075985;
        }

        .badge-manual {
            background: #dcfce7;
            color: #166534;
        }

        .badge-unavailable {
            background: #f1f5f9;
            color: #64748b;
        }

        .badge-low {
            background: #dcfce7;
            color: #166534;
        }

        .badge-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-high {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-wrapper {
            display: flex;
            align-items: center;
            gap: 7px;
            min-width: 142px;
        }

        .action-wrapper form {
            margin: 0;
        }

        .action-button {
            min-width: 62px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
        }

        .action-column {
            position: sticky;
            right: 0;
            z-index: 2;
            background: white;
            box-shadow: -8px 0 14px rgba(15, 23, 42, 0.04);
        }

        thead .action-column {
            z-index: 3;
            background: #f8fafc;
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
                    {{ app()->getLocale() === 'id'
                        ? 'Kelola Pelabuhan'
                        : 'Manage Ports'
                    }}
                </h2>

                <small class="text-muted">
                    {{ app()->getLocale() === 'id'
                        ? 'Kelola data manual dan referensi UN/LOCODE'
                        : 'Manage manual and UN/LOCODE reference data'
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
                {{ app()->getLocale() === 'id'
                    ? 'Data Pelabuhan'
                    : 'Port Data'
                }}
            </h1>

            <p class="text-muted mb-0">
                {{ app()->getLocale() === 'id'
                    ? 'Data referensi menunjukkan lokasi pelabuhan, sedangkan data manual memuat kondisi operasional.'
                    : 'Reference records show port locations, while manual records contain operational conditions.'
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
            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Total Pelabuhan'
                            : 'Total Ports'
                        }}
                    </div>
                    <p class="summary-value">
                        {{ number_format($portStatistics['total']) }}
                    </p>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Referensi UN/LOCODE'
                            : 'UN/LOCODE References'
                        }}
                    </div>
                    <p class="summary-value">
                        {{ number_format($portStatistics['reference']) }}
                    </p>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Data Manual'
                            : 'Manual Data'
                        }}
                    </div>
                    <p class="summary-value">
                        {{ number_format($portStatistics['manual']) }}
                    </p>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-label">
                        {{ app()->getLocale() === 'id'
                            ? 'Memiliki Koordinat'
                            : 'With Coordinates'
                        }}
                    </div>
                    <p class="summary-value">
                        {{ number_format($portStatistics['with_coordinates']) }}
                    </p>
                </div>
            </div>
        </section>

        <section class="filter-card">
            <form method="GET" action="{{ route('admin.ports.index') }}">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label for="search" class="form-label fw-semibold">
                            {{ app()->getLocale() === 'id'
                                ? 'Pencarian'
                                : 'Search'
                            }}
                        </label>

                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="{{
                                app()->getLocale() === 'id'
                                    ? 'Nama, kota, negara, atau UN/LOCODE'
                                    : 'Name, city, country, or UN/LOCODE'
                            }}"
                        >
                    </div>

                    <div class="col-md-6 col-lg-2">
                        <label for="country_id" class="form-label fw-semibold">
                            {{ app()->getLocale() === 'id'
                                ? 'Negara'
                                : 'Country'
                            }}
                        </label>

                        <select
                            id="country_id"
                            name="country_id"
                            class="form-select"
                        >
                            <option value="">
                                {{ app()->getLocale() === 'id'
                                    ? 'Semua negara'
                                    : 'All countries'
                                }}
                            </option>

                            @foreach ($countries as $country)
                                <option
                                    value="{{ $country->id }}"
                                    @selected((int) $countryId === $country->id)
                                >
                                    {{ $country->name }}
                                    @if ($country->iso2)
                                        ({{ $country->iso2 }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 col-lg-2">
                        <label for="data_type" class="form-label fw-semibold">
                            {{ app()->getLocale() === 'id'
                                ? 'Jenis Data'
                                : 'Data Type'
                            }}
                        </label>

                        <select
                            id="data_type"
                            name="data_type"
                            class="form-select"
                        >
                            <option value="">
                                {{ app()->getLocale() === 'id'
                                    ? 'Semua jenis'
                                    : 'All types'
                                }}
                            </option>
                            <option
                                value="reference"
                                @selected($dataType === 'reference')
                            >
                                UN/LOCODE
                            </option>
                            <option
                                value="manual"
                                @selected($dataType === 'manual')
                            >
                                {{ app()->getLocale() === 'id'
                                    ? 'Manual/Operasional'
                                    : 'Manual/Operational'
                                }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6 col-lg-2">
                        <label for="coordinates" class="form-label fw-semibold">
                            {{ app()->getLocale() === 'id'
                                ? 'Koordinat'
                                : 'Coordinates'
                            }}
                        </label>

                        <select
                            id="coordinates"
                            name="coordinates"
                            class="form-select"
                        >
                            <option value="">
                                {{ app()->getLocale() === 'id'
                                    ? 'Semua'
                                    : 'All'
                                }}
                            </option>
                            <option
                                value="available"
                                @selected($coordinateStatus === 'available')
                            >
                                {{ app()->getLocale() === 'id'
                                    ? 'Tersedia'
                                    : 'Available'
                                }}
                            </option>
                            <option
                                value="missing"
                                @selected($coordinateStatus === 'missing')
                            >
                                {{ app()->getLocale() === 'id'
                                    ? 'Belum tersedia'
                                    : 'Missing'
                                }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 col-lg-1">
                        <label for="per_page" class="form-label fw-semibold">
                            {{ app()->getLocale() === 'id'
                                ? 'Baris'
                                : 'Rows'
                            }}
                        </label>

                        <select
                            id="per_page"
                            name="per_page"
                            class="form-select"
                        >
                            @foreach ([10, 25, 50, 100] as $size)
                                <option
                                    value="{{ $size }}"
                                    @selected($perPage === $size)
                                >
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-lg-1 d-flex align-items-end">
                        <button
                            type="submit"
                            class="btn btn-primary filter-button w-100"
                        >
                            {{ app()->getLocale() === 'id'
                                ? 'Terapkan'
                                : 'Apply'
                            }}
                        </button>
                    </div>
                </div>

                @if (
                    $search !== ''
                    || $countryId !== null
                    || $dataType !== ''
                    || $coordinateStatus !== ''
                    || $perPage !== 25
                )
                    <div class="mt-3">
                        <a
                            href="{{ route('admin.ports.index') }}"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            {{ app()->getLocale() === 'id'
                                ? 'Reset Filter'
                                : 'Reset Filters'
                            }}
                        </a>
                    </div>
                @endif
            </form>
        </section>

        <section class="content-card">
            <div class="content-card-header">
                <div>
                    <h3 class="content-card-title">
                        {{ app()->getLocale() === 'id'
                            ? 'Daftar Pelabuhan'
                            : 'Port List'
                        }}
                    </h3>

                    <small class="text-muted">
                        {{ app()->getLocale() === 'id'
                            ? 'Data ditemukan:'
                            : 'Records found:'
                        }}
                        {{ number_format($ports->total()) }}
                    </small>
                </div>

                <a
                    href="{{ route('admin.ports.create') }}"
                    class="btn btn-primary btn-sm"
                >
                    <span class="me-1">+</span>
                    {{ app()->getLocale() === 'id'
                        ? 'Tambah Data Operasional'
                        : 'Add Operational Data'
                    }}
                </a>
            </div>

            @if ($ports->isEmpty())
                <div class="empty-state">
                    <div class="fs-1 mb-3">⚓</div>
                    <h5 class="fw-bold">
                        {{ app()->getLocale() === 'id'
                            ? 'Data pelabuhan tidak ditemukan'
                            : 'No ports found'
                        }}
                    </h5>
                    <p class="mb-0">
                        {{ app()->getLocale() === 'id'
                            ? 'Ubah kata pencarian atau filter yang digunakan.'
                            : 'Change the search term or selected filters.'
                        }}
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>UN/LOCODE</th>
                                <th>
                                    {{ app()->getLocale() === 'id'
                                        ? 'Pelabuhan'
                                        : 'Port'
                                    }}
                                </th>
                                <th>
                                    {{ app()->getLocale() === 'id'
                                        ? 'Negara'
                                        : 'Country'
                                    }}
                                </th>
                                <th>
                                    {{ app()->getLocale() === 'id'
                                        ? 'Koordinat'
                                        : 'Coordinates'
                                    }}
                                </th>
                                <th>
                                    {{ app()->getLocale() === 'id'
                                        ? 'Sumber'
                                        : 'Source'
                                    }}
                                </th>
                                <th>
                                    {{ app()->getLocale() === 'id'
                                        ? 'Kondisi Operasional'
                                        : 'Operational Condition'
                                    }}
                                </th>
                                <th class="action-column">
                                    {{ app()->getLocale() === 'id'
                                        ? 'Aksi'
                                        : 'Actions'
                                    }}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($ports as $port)
                                @php
                                    $isReference = $port->unlocode !== null;
                                    $congestion = strtolower(
                                        trim((string) $port->congestion_level)
                                    );

                                    $congestionClass = match ($congestion) {
                                        'high' => 'badge-high',
                                        'medium' => 'badge-medium',
                                        'low' => 'badge-low',
                                        default => 'badge-unavailable',
                                    };

                                    $congestionLabel = match ($congestion) {
                                        'high' => app()->getLocale() === 'id'
                                            ? 'Tinggi'
                                            : 'High',

                                        'medium' => app()->getLocale() === 'id'
                                            ? 'Sedang'
                                            : 'Medium',

                                        'low' => app()->getLocale() === 'id'
                                            ? 'Rendah'
                                            : 'Low',

                                        default => app()->getLocale() === 'id'
                                            ? 'Tidak tersedia'
                                            : 'Unavailable',
                                    };
                                @endphp

                                <tr>
                                    <td>
                                        {{ $ports->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        @if ($port->unlocode)
                                            <code>{{ $port->unlocode }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $port->name }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $port->city ?? '-' }}
                                        </small>
                                    </td>

                                    <td>
                                        <div>{{ $port->country?->name ?? '-' }}</div>
                                        <small class="text-muted">
                                            {{ $port->country?->iso2 ?? '-' }}
                                        </small>
                                    </td>

                                    <td class="text-nowrap">
                                        @if (
                                            $port->latitude !== null
                                            && $port->longitude !== null
                                        )
                                            <small class="text-muted">
                                                {{ $port->latitude }},
                                                {{ $port->longitude }}
                                            </small>
                                        @else
                                            <span class="status-badge badge-unavailable">
                                                {{ app()->getLocale() === 'id'
                                                    ? 'Belum tersedia'
                                                    : 'Unavailable'
                                                }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <span
                                            class="status-badge {{
                                                $isReference
                                                    ? 'badge-reference'
                                                    : 'badge-manual'
                                            }}"
                                        >
                                            {{ $isReference
                                                ? 'UN/LOCODE'
                                                : (app()->getLocale() === 'id'
                                                    ? 'Manual'
                                                    : 'Manual')
                                            }}
                                        </span>

                                        @if ($port->source_version)
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    {{ $port->source_version }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="status-badge {{ $congestionClass }}">
                                            {{ $congestionLabel }}
                                        </span>

                                        <div class="mt-1">
                                            <small class="text-muted">
                                                {{ app()->getLocale() === 'id'
                                                    ? 'Delay:'
                                                    : 'Delay:'
                                                }}

                                                @if ($port->delay_days !== null)
                                                    {{ $port->delay_days }}
                                                    {{ app()->getLocale() === 'id'
                                                        ? 'hari'
                                                        : 'days'
                                                    }}
                                                @else
                                                    {{ app()->getLocale() === 'id'
                                                        ? 'tidak tersedia'
                                                        : 'unavailable'
                                                    }}
                                                @endif
                                            </small>
                                        </div>
                                    </td>

                                    <td class="action-column">
                                        @if (! $isReference)
                                            <div class="action-wrapper">
                                                <a
                                                    href="{{ route('admin.ports.edit', $port) }}"
                                                    class="btn btn-warning btn-sm action-button"
                                                >
                                                    Edit
                                                </a>

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.ports.destroy', $port) }}"
                                                    onsubmit="return confirm(
                                                        '{{ app()->getLocale() === 'id'
                                                            ? 'Hapus data pelabuhan manual ini?'
                                                            : 'Delete this manual port record?'
                                                        }}'
                                                    )"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-danger btn-sm action-button"
                                                    >
                                                        {{ app()->getLocale() === 'id'
                                                            ? 'Hapus'
                                                            : 'Delete'
                                                        }}
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="status-badge badge-reference">
                                                {{ app()->getLocale() === 'id'
                                                    ? 'Data Referensi'
                                                    : 'Reference Data'
                                                }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($ports->hasPages())
                    <div class="pagination-wrapper">
                        {{ $ports->links('pagination::bootstrap-5') }}
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