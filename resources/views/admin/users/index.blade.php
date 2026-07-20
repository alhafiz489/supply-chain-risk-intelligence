<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">

    <title>
        {{ app()->getLocale() === 'id'
            ? 'Kelola Pengguna - SupplyGuard'
            : 'Manage Users - SupplyGuard'
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
            --dark: #111827;
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

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .page-title {
            margin-bottom: 6px;
            font-size: 30px;
            font-weight: 800;
        }

        .search-card {
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

        .table {
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

        .role-badge,
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .role-admin {
            background: #e0e7ff;
            color: #3730a3;
        }

        .role-user {
            background: #f1f5f9;
            color: #475569;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
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

            .page-header {
                display: block;
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
                        ? 'Kelola Pengguna'
                        : 'Manage Users'
                    }}
                </h2>

                <small class="text-muted">
                    {{ app()->getLocale() === 'id'
                        ? 'Lihat dan cari akun SupplyGuard'
                        : 'View and search SupplyGuard accounts'
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
        <section class="page-header">
            <div>
                <h1 class="page-title">
                    {{ app()->getLocale() === 'id'
                        ? 'Data Pengguna'
                        : 'User Data'
                    }}
                </h1>

                <p class="text-muted mb-0">
                    {{ app()->getLocale() === 'id'
                        ? 'Daftar akun pengguna dan administrator yang terdaftar.'
                        : 'List of registered user and administrator accounts.'
                    }}
                </p>
            </div>
        </section>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <section class="search-card">
            <form
                method="GET"
                action="{{ url('/admin/users') }}"
            >
                <div class="row g-3">
                    <div class="col-md-9 col-lg-10">
                        <label
                            for="search"
                            class="form-label fw-semibold"
                        >
                            {{ app()->getLocale() === 'id'
                                ? 'Pencarian Pengguna'
                                : 'Search Users'
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
                                    ? 'Cari nama, email, peran, atau status...'
                                    : 'Search name, email, role, or status...'
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
                            ? 'Daftar Pengguna'
                            : 'User List'
                        }}
                    </h3>

                    <small class="text-muted">
                        {{ app()->getLocale() === 'id'
                            ? 'Total data ditemukan:'
                            : 'Total records found:'
                        }}

                        {{ $users->total() }}
                    </small>
                </div>

                @if ($search !== '')
                    <a
                        href="{{ url('/admin/users') }}"
                        class="btn btn-outline-secondary btn-sm"
                    >
                        {{ app()->getLocale() === 'id'
                            ? 'Reset Pencarian'
                            : 'Reset Search'
                        }}
                    </a>
                @endif
            </div>

            @if ($users->isEmpty())
                <div class="empty-state">
                    <div class="fs-1 mb-3">♙</div>

                    <h5 class="fw-bold">
                        {{ app()->getLocale() === 'id'
                            ? 'Data pengguna tidak ditemukan'
                            : 'No users found'
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

                                <th>
                                    {{ app()->getLocale() === 'id'
                                        ? 'Tanggal Daftar'
                                        : 'Registered At'
                                    }}
                                </th>

                                <th>
    {{ app()->getLocale() === 'id'
        ? 'Aksi'
        : 'Action'
    }}
</th>

                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        {{ $users->firstItem() + $loop->index }}
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $user->name }}
                                    </td>

                                    <td>
                                        {{ $user->email }}
                                    </td>

                                    <td>
                                        <span
                                            class="role-badge {{
                                                $user->role === 'admin'
                                                    ? 'role-admin'
                                                    : 'role-user'
                                            }}"
                                        >
                                            {{ ucfirst($user->role) }}
                                        </span>
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

                                    <td>
                                        {{ optional($user->created_at)
                                            ->format('d/m/Y H:i') }}
                                    </td>

                                    <td>
    @if ($user->id === auth()->id())
        <span class="text-muted small">
            {{ app()->getLocale() === 'id'
                ? 'Akun saat ini'
                : 'Current account'
            }}
        </span>
    @else
        <form
            method="POST"
            action="{{ route('admin.users.status', $user) }}"
            class="m-0"
        >
            @csrf
            @method('PATCH')

            <input
                type="hidden"
                name="status"
                value="{{ $user->status === 'active'
                    ? 'inactive'
                    : 'active'
                }}"
            >

            @if ($user->status === 'active')
                <button
                    type="submit"
                    class="btn btn-outline-danger btn-sm"
                    onclick="return confirm('Nonaktifkan akun pengguna ini?')"
                >
                    {{ app()->getLocale() === 'id'
                        ? 'Nonaktifkan'
                        : 'Deactivate'
                    }}
                </button>
            @else
                <button
                    type="submit"
                    class="btn btn-outline-success btn-sm"
                    onclick="return confirm('Aktifkan kembali akun pengguna ini?')"
                >
                    {{ app()->getLocale() === 'id'
                        ? 'Aktifkan'
                        : 'Activate'
                    }}
                </button>
            @endif
        </form>
    @endif
</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="pagination-wrapper">
                        {{ $users->links('pagination::bootstrap-5') }}
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