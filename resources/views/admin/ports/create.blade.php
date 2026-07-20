<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>
        {{ app()->getLocale() === 'id'
            ? 'Tambah Pelabuhan - SupplyGuard'
            : 'Add Port - SupplyGuard'
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
            flex-shrink: 0;
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

        .logout-button:hover { background: rgba(239, 68, 68, 0.16); }

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

        .form-card {
            max-width: 980px;
            margin-top: 24px;
            padding: 28px;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }

        .form-section-title {
            margin-bottom: 6px;
            font-size: 18px;
            font-weight: 800;
        }

        .form-section-description {
            margin-bottom: 24px;
            color: var(--muted);
            font-size: 14px;
        }

        .form-label {
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
        }

        .form-control,
        .form-select {
            min-height: 48px;
            border-radius: 12px;
        }

        .required-mark { color: #dc2626; }

        .button-wrapper {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid #e5e7eb;
        }

        .action-button {
            min-width: 140px;
            min-height: 46px;
            border-radius: 12px;
            font-weight: 700;
        }

        .mobile-menu-button { display: none; }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-menu-button { display: inline-flex; }
            .topbar { padding: 14px 20px; }
            .page-wrapper { padding: 22px 18px; }
        }

        @media (max-width: 575.98px) {
            .admin-profile-text { display: none; }
            .form-card { padding: 20px; }
            .button-wrapper { flex-direction: column-reverse; }
            .action-button { width: 100%; }
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
                    {{ app()->getLocale() === 'id' ? 'Tambah Pelabuhan' : 'Add Port' }}
                </h2>
                <small class="text-muted">
                    {{ app()->getLocale() === 'id'
                        ? 'Lengkapi data pelabuhan yang akan ditambahkan ke sistem.'
                        : 'Complete the port data that will be added to the system.'
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
                {{ app()->getLocale() === 'id' ? 'Form Tambah Pelabuhan' : 'Add Port Form' }}
            </h1>
            <p class="text-muted mb-0">
                {{ app()->getLocale() === 'id'
                    ? 'Lengkapi data pelabuhan yang akan ditambahkan ke sistem.'
                    : 'Complete the port data that will be added to the system.'
                }}
            </p>
        </section>

        <section class="form-card">
            <h3 class="form-section-title">
                {{ app()->getLocale() === 'id' ? 'Informasi Pelabuhan' : 'Port Information' }}
            </h3>

            <p class="form-section-description">
                {{ app()->getLocale() === 'id'
                    ? 'Kolom bertanda bintang wajib diisi.'
                    : 'Fields marked with an asterisk are required.'
                }}
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="fw-bold mb-2">
                        {{ app()->getLocale() === 'id'
                            ? 'Data belum dapat disimpan:'
                            : 'The data could not be saved:'
                        }}
                    </div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.ports.store') }}">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="country_id" class="form-label">
                            {{ app()->getLocale() === 'id' ? 'Negara' : 'Country' }}
                            <span class="required-mark">*</span>
                        </label>

                        <select
                            id="country_id"
                            name="country_id"
                            class="form-select @error('country_id') is-invalid @enderror"
                            required
                        >
                            <option value="">
                                {{ app()->getLocale() === 'id' ? 'Pilih negara' : 'Select country' }}
                            </option>

                            @foreach ($countries as $country)
                                <option
                                    value="{{ $country->id }}"
                                    @selected(old('country_id') == $country->id)
                                >
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('country_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label">
                            {{ app()->getLocale() === 'id' ? 'Nama Pelabuhan' : 'Port Name' }}
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="{{ app()->getLocale() === 'id' ? 'Masukkan nama pelabuhan' : 'Enter port name' }}"
                            required
                        >

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="city" class="form-label">
                            {{ app()->getLocale() === 'id' ? 'Kota' : 'City' }}
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            id="city"
                            type="text"
                            name="city"
                            value="{{ old('city') }}"
                            class="form-control @error('city') is-invalid @enderror"
                            placeholder="{{ app()->getLocale() === 'id' ? 'Masukkan nama kota' : 'Enter city name' }}"
                            required
                        >

                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="congestion_level" class="form-label">
                            {{ app()->getLocale() === 'id' ? 'Tingkat Kemacetan' : 'Congestion Level' }}
                            <span class="required-mark">*</span>
                        </label>

                        <select
                            id="congestion_level"
                            name="congestion_level"
                            class="form-select @error('congestion_level') is-invalid @enderror"
                            required
                        >
                            <option value="Low" @selected(old('congestion_level', 'Low') === 'Low')>
                                {{ app()->getLocale() === 'id' ? 'Rendah' : 'Low' }}
                            </option>
                            <option value="Medium" @selected(old('congestion_level', 'Low') === 'Medium')>
                                {{ app()->getLocale() === 'id' ? 'Sedang' : 'Medium' }}
                            </option>
                            <option value="High" @selected(old('congestion_level', 'Low') === 'High')>
                                {{ app()->getLocale() === 'id' ? 'Tinggi' : 'High' }}
                            </option>
                        </select>

                        @error('congestion_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="latitude" class="form-label">
                            Latitude <span class="required-mark">*</span>
                        </label>

                        <input
                            id="latitude"
                            type="number"
                            step="any"
                            min="-90"
                            max="90"
                            name="latitude"
                            value="{{ old('latitude') }}"
                            class="form-control @error('latitude') is-invalid @enderror"
                            placeholder="-6.104000"
                            required
                        >

                        @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="longitude" class="form-label">
                            Longitude <span class="required-mark">*</span>
                        </label>

                        <input
                            id="longitude"
                            type="number"
                            step="any"
                            min="-180"
                            max="180"
                            name="longitude"
                            value="{{ old('longitude') }}"
                            class="form-control @error('longitude') is-invalid @enderror"
                            placeholder="106.886000"
                            required
                        >

                        @error('longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="delay_days" class="form-label">
                            {{ app()->getLocale() === 'id' ? 'Keterlambatan (hari)' : 'Delay (days)' }}
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            id="delay_days"
                            type="number"
                            min="0"
                            max="365"
                            name="delay_days"
                            value="{{ old('delay_days', 0) }}"
                            class="form-control @error('delay_days') is-invalid @enderror"
                            required
                        >

                        @error('delay_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="button-wrapper">
                    <a
                        href="{{ route('admin.ports.index') }}"
                        class="btn btn-outline-secondary action-button d-inline-flex align-items-center justify-content-center"
                    >
                        {{ app()->getLocale() === 'id' ? 'Batal' : 'Cancel' }}
                    </a>

                    <button type="submit" class="btn btn-primary action-button">
                        {{ app()->getLocale() === 'id' ? 'Simpan Pelabuhan' : 'Save Port' }}
                    </button>
                </div>
            </form>
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