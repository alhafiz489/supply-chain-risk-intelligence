<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">

    <title>
        {{ app()->getLocale() === 'id'
            ? 'Edit Berita - SupplyGuard'
            : 'Edit News - SupplyGuard'
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
            --primary: #2563eb;
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
            top: 0;
            bottom: 0;
            left: 0;
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

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .required-mark {
            color: #dc2626;
        }

        .button-wrapper {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid #e5e7eb;
        }

        .action-button {
            min-width: 130px;
            min-height: 46px;
            border-radius: 12px;
            font-weight: 700;
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

            .form-card {
                padding: 20px;
            }

            .button-wrapper {
                flex-direction: column-reverse;
            }

            .action-button {
                width: 100%;
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
                        ? 'Edit Berita'
                        : 'Edit News'
                    }}
                </h2>

                <small class="text-muted">
                    {{ app()->getLocale() === 'id'
                        ? 'Perbarui informasi berita yang tersimpan'
                        : 'Update the stored news information'
                    }}
                </small>
            </div>
        </div>

        <div class="admin-profile">
            <span class="admin-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>

            <div class="admin-profile-text">
                <div class="fw-bold">
                    {{ auth()->user()->name }}
                </div>

                <small class="text-muted">
                    Administrator
                </small>
            </div>
        </div>
    </header>

    <main class="page-wrapper">
        <section>
            <h1 class="page-title">
                {{ app()->getLocale() === 'id'
                    ? 'Form Edit Berita'
                    : 'Edit News Form'
                }}
            </h1>

            <p class="text-muted mb-0">
                {{ app()->getLocale() === 'id'
                    ? 'Perbarui data berita yang tersimpan di dalam sistem.'
                    : 'Update the news data stored in the system.'
                }}
            </p>
        </section>

        <section class="form-card">
            <h3 class="form-section-title">
                {{ app()->getLocale() === 'id'
                    ? 'Informasi Berita'
                    : 'News Information'
                }}
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

            <form
                method="POST"
                action="{{ route('admin.news.update', $news) }}"
            >
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="country_id" class="form-label">
                            {{ app()->getLocale() === 'id'
                                ? 'Negara'
                                : 'Country'
                            }}

                            <span class="required-mark">*</span>
                        </label>

                        <select
                            id="country_id"
                            name="country_id"
                            class="form-select @error('country_id') is-invalid @enderror"
                            required
                        >
                            <option value="">
                                {{ app()->getLocale() === 'id'
                                    ? 'Pilih negara'
                                    : 'Select country'
                                }}
                            </option>

                            @foreach ($countries as $country)
                                <option
                                    value="{{ $country->id }}"
                                    @selected(old('country_id', $news->country_id) == $country->id)
                                >
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('country_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="published_date" class="form-label">
                            {{ app()->getLocale() === 'id'
                                ? 'Tanggal Publikasi'
                                : 'Publication Date'
                            }}
                        </label>

                        <input
                            id="published_date"
                            type="date"
                            name="published_date"
                            value="{{ old(
                                'published_date',
                                $news->published_date?->format('Y-m-d')
                            ) }}"
                            class="form-control @error('published_date') is-invalid @enderror"
                        >

                        @error('published_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="title" class="form-label">
                            {{ app()->getLocale() === 'id'
                                ? 'Judul Berita'
                                : 'News Title'
                            }}

                            <span class="required-mark">*</span>
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title', $news->title) }}"
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="{{
                                app()->getLocale() === 'id'
                                    ? 'Masukkan judul berita'
                                    : 'Enter the news title'
                            }}"
                            required
                        >

                        @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="form-label">
                            {{ app()->getLocale() === 'id'
                                ? 'Kategori'
                                : 'Category'
                            }}

                            <span class="required-mark">*</span>
                        </label>

                        <select
                            id="category"
                            name="category"
                            class="form-select @error('category') is-invalid @enderror"
                            required
                        >
                            <option value="">
                                {{ app()->getLocale() === 'id'
                                    ? 'Pilih kategori'
                                    : 'Select category'
                                }}
                            </option>

                            <option
                                value="Economy"
                                @selected(old('category', $news->category) === 'Economy')
                            >
                                Economy
                            </option>

                            <option
                                value="Logistics"
                                @selected(old('category', $news->category) === 'Logistics')
                            >
                                Logistics
                            </option>

                            <option
                                value="Trade"
                                @selected(old('category', $news->category) === 'Trade')
                            >
                                Trade
                            </option>

                            <option
                                value="Port"
                                @selected(old('category', $news->category) === 'Port')
                            >
                                Port
                            </option>

                            <option
                                value="Weather"
                                @selected(old('category', $news->category) === 'Weather')
                            >
                                Weather
                            </option>

                            <option
                                value="Politics"
                                @selected(old('category', $news->category) === 'Politics')
                            >
                                Politics
                            </option>

                            <option
                                value="Shipping"
                                @selected(old('category', $news->category) === 'Shipping')
                            >
                                Shipping
                            </option>
                        </select>

                        @error('category')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="sentiment" class="form-label">
                            {{ app()->getLocale() === 'id'
                                ? 'Sentimen'
                                : 'Sentiment'
                            }}

                            <span class="required-mark">*</span>
                        </label>

                        <select
                            id="sentiment"
                            name="sentiment"
                            class="form-select @error('sentiment') is-invalid @enderror"
                            required
                        >
                            <option value="">
                                {{ app()->getLocale() === 'id'
                                    ? 'Pilih sentimen'
                                    : 'Select sentiment'
                                }}
                            </option>

                            <option
                                value="Auto"
                                @selected(old('sentiment') === 'Auto')
                            >
                                {{ app()->getLocale() === 'id'
                                    ? 'Hitung ulang secara otomatis'
                                    : 'Recalculate automatically'
                                }}
                            </option>

                            <option
                                value="Positive"
                                @selected(old('sentiment', $news->sentiment) === 'Positive')
                            >
                                {{ app()->getLocale() === 'id'
                                    ? 'Positif'
                                    : 'Positive'
                                }}
                            </option>

                            <option
                                value="Neutral"
                                @selected(old('sentiment', $news->sentiment) === 'Neutral')
                            >
                                {{ app()->getLocale() === 'id'
                                    ? 'Netral'
                                    : 'Neutral'
                                }}
                            </option>

                            <option
                                value="Negative"
                                @selected(old('sentiment', $news->sentiment) === 'Negative')
                            >
                                {{ app()->getLocale() === 'id'
                                    ? 'Negatif'
                                    : 'Negative'
                                }}
                            </option>
                        </select>

                        @error('sentiment')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            {{ app()->getLocale() === 'id'
                                ? 'Pilih hitung ulang otomatis untuk menganalisis kembali sentimen dari judul dan ringkasan.'
                                : 'Choose automatic recalculation to analyze sentiment again from the title and summary.'
                            }}
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="summary" class="form-label">
                            {{ app()->getLocale() === 'id'
                                ? 'Ringkasan Berita'
                                : 'News Summary'
                            }}
                        </label>

                        <textarea
                            id="summary"
                            name="summary"
                            class="form-control @error('summary') is-invalid @enderror"
                            placeholder="{{
                                app()->getLocale() === 'id'
                                    ? 'Masukkan ringkasan isi berita'
                                    : 'Enter a summary of the news'
                            }}"
                        >{{ old('summary', $news->summary) }}</textarea>

                        @error('summary')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="button-wrapper">
                    <a
                        href="{{ route('admin.news.index') }}"
                        class="btn btn-outline-secondary action-button d-inline-flex align-items-center justify-content-center"
                    >
                        {{ app()->getLocale() === 'id'
                            ? 'Batal'
                            : 'Cancel'
                        }}
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary action-button"
                    >
                        {{ app()->getLocale() === 'id'
                            ? 'Perbarui Berita'
                            : 'Update News'
                        }}
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