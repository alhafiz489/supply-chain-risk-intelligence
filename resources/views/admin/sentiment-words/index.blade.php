<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ app()->getLocale() === 'id' ? 'Kamus Sentimen - SupplyGuard' : 'Sentiment Dictionary - SupplyGuard' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .card-box {
            margin-top: 22px;
            border: 1px solid #e5e7eb;
            border-radius: 19px;
            background: white;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .card-box-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 22px;
            border-bottom: 1px solid #e5e7eb;
        }

        .content-card-title { margin: 0; font-size: 17px; font-weight: 800; }

        .form-control,
        .form-select {
            min-height: 48px;
            border-radius: 12px;
        }

        .table { margin-bottom: 0; }

        .table th {
            padding: 14px 18px;
            border-bottom-color: #e5e7eb;
            background: #f8fafc;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .table td {
            padding: 15px 18px;
            border-bottom-color: #edf0f4;
            vertical-align: middle;
            font-size: 14px;
        }

        .word-badge,
        .type-badge,
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .word-badge { background: #f1f5f9; color: #334155; }
        .type-positive { background: #dcfce7; color: #166534; }
        .type-negative { background: #fee2e2; color: #991b1b; }
        .status-active { background: #dbeafe; color: #1d4ed8; }
        .status-inactive { background: #e5e7eb; color: #475569; }

        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: var(--muted);
        }

        .pagination-wrapper {
            padding: 18px 22px;
            border-top: 1px solid #e5e7eb;
        }

        .action-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 145px;
        }

        .action-wrapper form { margin: 0; }

        .action-button {
            min-width: 64px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
        }

        .form-card {
            max-width: 900px;
            margin-top: 24px;
            padding: 28px;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }

        .form-label {
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
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

        .form-action-button {
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
            .card-box-header { align-items: flex-start; flex-direction: column; }
            .form-card { padding: 20px; }
            .button-wrapper { flex-direction: column-reverse; }
            .form-action-button { width: 100%; }
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
                {{ app()->getLocale() === 'id' ? 'Kamus Sentimen' : 'Sentiment Dictionary' }}
            </h2>
            <small class="text-muted">
                {{ app()->getLocale() === 'id'
                    ? 'Kelola kata positif dan negatif untuk analisis otomatis'
                    : 'Manage positive and negative words for automatic analysis'
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
                {{ app()->getLocale() === 'id' ? 'Data Kamus Sentimen' : 'Sentiment Dictionary Data' }}
            </h1>
            <p class="text-muted mb-0">
                {{ app()->getLocale() === 'id'
                    ? 'Kata aktif digunakan sistem untuk menentukan sentimen berita secara otomatis.'
                    : 'Active words are used by the system to determine news sentiment automatically.'
                }}
            </p>
        </section>

        @if (session('success'))
            <div class="alert alert-success mt-4">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mt-4">{{ session('error') }}</div>
        @endif

        <section class="card-box">
            <div class="p-4">
                <form method="GET" action="{{ route('admin.sentiment-words.index') }}">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label for="search" class="form-label">
                                {{ app()->getLocale() === 'id' ? 'Cari Kata' : 'Search Word' }}
                            </label>
                            <input
                                id="search"
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                class="form-control"
                                placeholder="{{ app()->getLocale() === 'id' ? 'Contoh: meningkat, krisis...' : 'Example: increase, crisis...' }}"
                            >
                        </div>

                        <div class="col-md-4 col-lg-2">
                            <label for="type" class="form-label">
                                {{ app()->getLocale() === 'id' ? 'Jenis' : 'Type' }}
                            </label>
                            <select id="type" name="type" class="form-select">
                                <option value="">
                                    {{ app()->getLocale() === 'id' ? 'Semua' : 'All' }}
                                </option>
                                <option value="positive" @selected($type === 'positive')>
                                    {{ app()->getLocale() === 'id' ? 'Positif' : 'Positive' }}
                                </option>
                                <option value="negative" @selected($type === 'negative')>
                                    {{ app()->getLocale() === 'id' ? 'Negatif' : 'Negative' }}
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 col-lg-2">
                            <label for="status" class="form-label">
                                Status
                            </label>
                            <select id="status" name="status" class="form-select">
                                <option value="">
                                    {{ app()->getLocale() === 'id' ? 'Semua' : 'All' }}
                                </option>
                                <option value="active" @selected($status === 'active')>
                                    {{ app()->getLocale() === 'id' ? 'Aktif' : 'Active' }}
                                </option>
                                <option value="inactive" @selected($status === 'inactive')>
                                    {{ app()->getLocale() === 'id' ? 'Tidak Aktif' : 'Inactive' }}
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 col-lg-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100" style="min-height:48px;border-radius:12px;font-weight:700;">
                                {{ app()->getLocale() === 'id' ? 'Terapkan' : 'Apply' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <section class="card-box">
            <div class="card-box-header">
                <div>
                    <h3 class="content-card-title">
                        {{ app()->getLocale() === 'id' ? 'Daftar Kata Sentimen' : 'Sentiment Word List' }}
                    </h3>
                    <small class="text-muted">
                        {{ app()->getLocale() === 'id' ? 'Total data:' : 'Total records:' }}
                        {{ $words->total() }}
                    </small>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if ($search !== '' || $type !== '' || $status !== '')
                        <a href="{{ route('admin.sentiment-words.index') }}" class="btn btn-outline-secondary btn-sm">
                            {{ app()->getLocale() === 'id' ? 'Reset Filter' : 'Reset Filters' }}
                        </a>
                    @endif

                    <a href="{{ route('admin.sentiment-words.create') }}" class="btn btn-primary btn-sm">
                        <span class="me-1">+</span>
                        {{ app()->getLocale() === 'id' ? 'Tambah Kata' : 'Add Word' }}
                    </a>
                </div>
            </div>

            @if ($words->isEmpty())
                <div class="empty-state">
                    <div class="fs-1 mb-3">Aa</div>
                    <h5 class="fw-bold">
                        {{ app()->getLocale() === 'id' ? 'Data kata tidak ditemukan' : 'No words found' }}
                    </h5>
                    <p class="mb-0">
                        {{ app()->getLocale() === 'id' ? 'Tambahkan kata baru atau ubah filter pencarian.' : 'Add a new word or change the search filters.' }}
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Kata' : 'Word' }}</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Jenis' : 'Type' }}</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Bobot' : 'Weight' }}</th>
                                <th>Status</th>
                                <th>{{ app()->getLocale() === 'id' ? 'Aksi' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($words as $word)
                                <tr>
                                    <td>{{ $words->firstItem() + $loop->index }}</td>
                                    <td><span class="word-badge">{{ $word->word }}</span></td>
                                    <td>
                                        <span class="type-badge {{ $word->type === 'positive' ? 'type-positive' : 'type-negative' }}">
                                            {{ app()->getLocale() === 'id'
                                                ? ($word->type === 'positive' ? 'Positif' : 'Negatif')
                                                : ucfirst($word->type)
                                            }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold">{{ $word->weight }}</td>
                                    <td>
                                        <span class="status-badge {{ $word->is_active ? 'status-active' : 'status-inactive' }}">
                                            {{ app()->getLocale() === 'id'
                                                ? ($word->is_active ? 'Aktif' : 'Tidak Aktif')
                                                : ($word->is_active ? 'Active' : 'Inactive')
                                            }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-wrapper">
                                            <a
                                                href="{{ route('admin.sentiment-words.edit', $word) }}"
                                                class="btn btn-warning btn-sm action-button"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.sentiment-words.destroy', $word) }}"
                                                onsubmit="return confirm('{{ app()->getLocale() === 'id'
                                                    ? 'Yakin ingin menghapus kata sentimen ini?'
                                                    : 'Are you sure you want to delete this sentiment word?'
                                                }}')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-sm action-button">
                                                    {{ app()->getLocale() === 'id' ? 'Hapus' : 'Delete' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($words->hasPages())
                    <div class="pagination-wrapper">
                        {{ $words->links('pagination::bootstrap-5') }}
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