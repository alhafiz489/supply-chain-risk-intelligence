<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ app()->getLocale() === 'id' ? 'Edit Kata Sentimen - SupplyGuard' : 'Edit Sentiment Word - SupplyGuard' }}</title>
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
                {{ app()->getLocale() === 'id' ? 'Edit Kata Sentimen' : 'Edit Sentiment Word' }}
            </h1>
            <p class="text-muted mb-0">
                {{ app()->getLocale() === 'id'
                    ? 'Atur kata, jenis sentimen, bobot, dan status penggunaannya.'
                    : 'Set the word, sentiment type, weight, and usage status.'
                }}
            </p>
        </section>

        <section class="form-card">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="fw-bold mb-2">
                        {{ app()->getLocale() === 'id' ? 'Data belum dapat disimpan:' : 'The data could not be saved:' }}
                    </div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.sentiment-words.update', $sentimentWord) }}">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="word" class="form-label">
                            {{ app()->getLocale() === 'id' ? 'Kata atau Frasa' : 'Word or Phrase' }}
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            id="word"
                            type="text"
                            name="word"
                            value="{{ old('word', $sentimentWord->word) }}"
                            class="form-control @error('word') is-invalid @enderror"
                            placeholder="{{ app()->getLocale() === 'id' ? 'Contoh: meningkat' : 'Example: increasing' }}"
                            required
                        >

                        @error('word')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            {{ app()->getLocale() === 'id'
                                ? 'Gunakan huruf kecil. Sistem juga mendukung frasa seperti “berjalan lancar”.'
                                : 'Use lowercase letters. Phrases such as “running smoothly” are also supported.'
                            }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label">
                            {{ app()->getLocale() === 'id' ? 'Jenis Sentimen' : 'Sentiment Type' }}
                            <span class="required-mark">*</span>
                        </label>

                        <select
                            id="type"
                            name="type"
                            class="form-select @error('type') is-invalid @enderror"
                            required
                        >
                            <option value="positive" @selected(old('type', $sentimentWord->type) === 'positive')>
                                {{ app()->getLocale() === 'id' ? 'Positif' : 'Positive' }}
                            </option>
                            <option value="negative" @selected(old('type', $sentimentWord->type) === 'negative')>
                                {{ app()->getLocale() === 'id' ? 'Negatif' : 'Negative' }}
                            </option>
                        </select>

                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="weight" class="form-label">
                            {{ app()->getLocale() === 'id' ? 'Bobot Nilai' : 'Score Weight' }}
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            id="weight"
                            type="number"
                            min="1"
                            max="10"
                            name="weight"
                            value="{{ old('weight', $sentimentWord->weight) }}"
                            class="form-control @error('weight') is-invalid @enderror"
                            required
                        >

                        @error('weight')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            {{ app()->getLocale() === 'id'
                                ? 'Bobot 1 berarti pengaruh rendah, sedangkan 10 berarti sangat kuat.'
                                : 'Weight 1 means low influence, while 10 means very strong.'
                            }}
                        </div>
                    </div>

                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check form-switch mt-4">
                            <input type="hidden" name="is_active" value="0">
                            <input
                                id="is_active"
                                type="checkbox"
                                name="is_active"
                                value="1"
                                class="form-check-input"
                                @checked(old('is_active', $sentimentWord->is_active))
                            >
                            <label for="is_active" class="form-check-label fw-semibold">
                                {{ app()->getLocale() === 'id'
                                    ? 'Aktifkan kata untuk analisis otomatis'
                                    : 'Enable word for automatic analysis'
                                }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="button-wrapper">
                    <a
                        href="{{ route('admin.sentiment-words.index') }}"
                        class="btn btn-outline-secondary form-action-button d-inline-flex align-items-center justify-content-center"
                    >
                        {{ app()->getLocale() === 'id' ? 'Batal' : 'Cancel' }}
                    </a>

                    <button type="submit" class="btn btn-primary form-action-button">
                        {{ app()->getLocale() === 'id' ? 'Perbarui Kata' : 'Update Word' }}
                    </button>
                </div>
            </form>
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