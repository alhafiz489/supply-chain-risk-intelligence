<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">

    <title>
        {{ app()->getLocale() === 'id'
            ? 'Kelola Berita - SupplyGuard'
            : 'Manage News - SupplyGuard'
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
            background: linear-gradient(
                180deg,
                #111827,
                #172033
            );
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
            background: linear-gradient(
                135deg,
                #3b82f6,
                #1d4ed8
            );
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

        .search-card {
            margin-top: 24px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: white;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }

        .form-control {
            min-height: 48px;
            border-radius: 12px;
        }

        .search-button {
            min-height: 48px;
            border-radius: 12px;
            font-weight: 700;
        }

        .content-card {
            margin-top: 22px;
            border: 1px solid #e5e7eb;
            border-radius: 19px;
            background: white;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
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

        .table-responsive {
            position: relative;
            overflow-x: auto;
            scrollbar-width: thin;
        }

        .table {
            min-width: 1180px;
            margin-bottom: 0;
        }

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

        .news-title {
            min-width: 210px;
            max-width: 290px;
            font-weight: 700;
            line-height: 1.5;
        }

        .news-summary {
            min-width: 220px;
            max-width: 310px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.55;
        }

        .category-badge,
        .sentiment-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .category-badge {
            background: #e0e7ff;
            color: #3730a3;
        }

        .sentiment-positive {
            background: #dcfce7;
            color: #166534;
        }

        .sentiment-neutral {
            background: #f1f5f9;
            color: #475569;
        }

        .sentiment-negative {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-column {
            position: sticky;
            right: 0;
            z-index: 4;
            min-width: 160px;
            background: white;
            box-shadow: -8px 0 14px rgba(15, 23, 42, 0.06);
        }

        thead .action-column {
            z-index: 5;
            background: #f8fafc;
        }

        .action-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 145px;
        }

        .action-wrapper form {
            margin: 0;
        }

        .action-button {
            min-width: 64px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
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
                    {{ app()->getLocale() === 'id'
                        ? 'Kelola Berita'
                        : 'Manage News'
                    }}
                </h2>

                <small class="text-muted">
                    {{ app()->getLocale() === 'id'
                        ? 'Kelola berita dan hasil sentimen negara'
                        : 'Manage news and country sentiment results'
                    }}
                </small>
            </div>
        </div>

        <div class="admin-profile">
            <span class="admin-avatar">
                {{ strtoupper(
                    substr(auth()->user()->name, 0, 1)
                ) }}
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
                    ? 'Data Berita'
                    : 'News Data'
                }}
            </h1>

            <p class="text-muted mb-0">
                {{ app()->getLocale() === 'id'
                    ? 'Daftar berita berdasarkan negara, kategori, tanggal publikasi, dan hasil analisis sentimen.'
                    : 'List of news by country, category, publication date, and sentiment analysis result.'
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

        <section class="search-card">
            <form
                method="GET"
                action="{{ route('admin.news.index') }}"
            >
                <div class="row g-3">
                    <div class="col-md-9 col-lg-10">
                        <label
                            for="search"
                            class="form-label fw-semibold"
                        >
                            {{ app()->getLocale() === 'id'
                                ? 'Pencarian Berita'
                                : 'Search News'
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
                                    ? 'Cari judul, negara, kategori, sentimen, atau ringkasan...'
                                    : 'Search title, country, category, sentiment, or summary...'
                            }}"
                        >
                    </div>

                    <div class="col-md-3 col-lg-2 d-flex align-items-end">
                        <button
                            type="submit"
                            class="btn btn-primary search-button w-100"
                        >
                            {{ app()->getLocale() === 'id'
                                ? 'Cari'
                                : 'Search'
                            }}
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <section class="content-card">
            <div class="content-card-header">
                <div>
                    <h3 class="content-card-title">
                        {{ app()->getLocale() === 'id'
                            ? 'Daftar Berita'
                            : 'News List'
                        }}
                    </h3>

                    <small class="text-muted">
                        {{ app()->getLocale() === 'id'
                            ? 'Total data ditemukan:'
                            : 'Total records found:'
                        }}

                        {{ $newsItems->total() }}
                    </small>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if ($search !== '')
                        <a
                            href="{{ route('admin.news.index') }}"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            {{ app()->getLocale() === 'id'
                                ? 'Reset Pencarian'
                                : 'Reset Search'
                            }}
                        </a>
                    @endif

                    <a
                        href="{{ route('admin.news.create') }}"
                        class="btn btn-primary btn-sm"
                    >
                        <span class="me-1">+</span>

                        {{ app()->getLocale() === 'id'
                            ? 'Tambah Berita'
                            : 'Add News'
                        }}
                    </a>
                </div>
            </div>

            @if ($newsItems->isEmpty())
                <div class="empty-state">
                    <div class="fs-1 mb-3">▤</div>

                    <h5 class="fw-bold">
                        {{ app()->getLocale() === 'id'
                            ? 'Data berita tidak ditemukan'
                            : 'No news found'
                        }}
                    </h5>

                    <p class="mb-0">
                        {{ app()->getLocale() === 'id'
                            ? 'Coba gunakan kata pencarian yang berbeda.'
                            : 'Try using a different search keyword.'
                        }}
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>

                                <th>
                                    {{ app()->getLocale() === 'id'
                                        ? 'Judul Berita'
                                        : 'News Title'
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
                                        ? 'Kategori'
                                        : 'Category'
                                    }}
                                </th>

                                <th>
                                    {{ app()->getLocale() === 'id'
                                        ? 'Sentimen'
                                        : 'Sentiment'
                                    }}
                                </th>

                                <th>
                                    {{ app()->getLocale() === 'id'
                                        ? 'Ringkasan'
                                        : 'Summary'
                                    }}
                                </th>

                                <th>
                                    {{ app()->getLocale() === 'id'
                                        ? 'Tanggal Publikasi'
                                        : 'Publication Date'
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
                            @foreach ($newsItems as $news)
                                @php
                                    $sentiment = strtolower(
                                        (string) $news->sentiment
                                    );

                                    $sentimentClass = match ($sentiment) {
                                        'positive' => 'sentiment-positive',
                                        'negative' => 'sentiment-negative',
                                        default => 'sentiment-neutral',
                                    };

                                    $sentimentLabel = app()->getLocale() === 'id'
                                        ? match ($sentiment) {
                                            'positive' => 'Positif',
                                            'negative' => 'Negatif',
                                            default => 'Netral',
                                        }
                                        : ucfirst($sentiment);
                                @endphp

                                <tr>
                                    <td>
                                        {{ $newsItems->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        <div class="news-title">
                                            {{ $news->title }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $news->country?->name ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="category-badge">
                                            {{ $news->category }}
                                        </span>
                                    </td>

                                    <td>
                                        <span
                                            class="sentiment-badge {{ $sentimentClass }}"
                                        >
                                            {{ $sentimentLabel }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="news-summary">
                                            {{ \Illuminate\Support\Str::limit(
                                                $news->summary ?? '-',
                                                95
                                            ) }}
                                        </div>
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $news->published_date
                                            ? \Carbon\Carbon::parse($news->published_date)->format('d/m/Y')
                                            : '-'
                                        }}
                                    </td>

                                    <td class="action-column">
                                        <div class="action-wrapper">
                                            <a
                                                href="{{ route('admin.news.edit', $news) }}"
                                                class="btn btn-warning btn-sm action-button"
                                            >
                                                {{ app()->getLocale() === 'id'
                                                    ? 'Edit'
                                                    : 'Edit'
                                                }}
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.news.destroy', $news) }}"
                                                onsubmit="return confirm(
                                                    '{{ app()->getLocale() === 'id'
                                                        ? 'Apakah Anda yakin ingin menghapus berita ini?'
                                                        : 'Are you sure you want to delete this news?'
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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($newsItems->hasPages())
                    <div class="pagination-wrapper">
                        {{ $newsItems->links('pagination::bootstrap-5') }}
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