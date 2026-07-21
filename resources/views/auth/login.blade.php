<!DOCTYPE html>
@php($adminMode = $adminMode ?? false)
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">

    <title>
        {{ $adminMode ? 'Admin Login - SupplyGuard' : 'Login - SupplyGuard' }}
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, 0.13),
                    transparent 34%
                ),
                #f4f7fb;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 16px;
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            border: 0;
            border-radius: 24px;
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.12);
        }

        .brand-mark {
            width: 46px;
            height: 46px;
            display: inline-grid;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(
                135deg,
                #3b82f6,
                #1d4ed8
            );
            color: white;
            font-weight: 800;
        }

        .form-control {
            min-height: 50px;
            border-radius: 12px;
        }

        .submit-button {
            min-height: 50px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(
                135deg,
                #2563eb,
                #1d4ed8
            );
            font-weight: 700;
        }
    </style>
</head>

<body>

<main class="auth-wrapper">
    <div class="card auth-card">
        <div class="card-body p-4 p-md-5">

            <a
                href="{{ route('dashboard') }}"
                class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark mb-4"
            >
                <span class="brand-mark">S</span>
                <span class="fw-bold fs-5">
                    SupplyGuard
                </span>
            </a>

            <h1 class="h3 fw-bold mb-2">
                @if ($adminMode)
                    Administrator Login
                @else
                    {{ app()->getLocale() === 'id' ? 'Masuk ke Akun' : 'Sign In' }}
                @endif
            </h1>

            <p class="text-muted mb-4">
                @if ($adminMode)
                    Masuk menggunakan akun administrator SupplyGuard.
                @else
                    {{ app()->getLocale() === 'id'
                        ? 'Masuk untuk mengelola daftar pemantauan negara.'
                        : 'Sign in to manage your country monitoring list.'
                    }}
                @endif
            </p>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form
                method="POST"
                action="{{ $adminMode ? route('admin.authenticate') : route('auth.login') }}"
            >
                @csrf

                <div class="mb-3">
                    <label
                        for="email"
                        class="form-label fw-semibold"
                    >
                        Email
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="nama@email.com"
                        autocomplete="email"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-3">
                    <label
                        for="password"
                        class="form-label fw-semibold"
                    >
                        Password
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="form-check mb-4">
                    <input
                        id="remember"
                        class="form-check-input"
                        type="checkbox"
                        name="remember"
                        value="1"
                    >

                    <label
                        class="form-check-label"
                        for="remember"
                    >
                        {{ app()->getLocale() === 'id'
                            ? 'Ingat saya'
                            : 'Remember me'
                        }}
                    </label>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary submit-button w-100"
                >
                    {{ app()->getLocale() === 'id'
                        ? 'Masuk'
                        : 'Sign In'
                    }}
                </button>
            </form>

            @if (! $adminMode)
            <p class="text-center text-muted mt-4 mb-0">
                {{ app()->getLocale() === 'id'
                    ? 'Belum memiliki akun?'
                    : 'Do not have an account?'
                }}

                <a
                    href="{{ route('register') }}"
                    class="fw-semibold text-decoration-none"
                >
                    {{ app()->getLocale() === 'id'
                        ? 'Daftar sekarang'
                        : 'Register now'
                    }}
                </a>
            </p>
            @else
            <p class="text-center mt-4 mb-0">
                <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">
                    Kembali ke login pengguna
                </a>
            </p>
            @endif

        </div>
    </div>
</main>

@include('user.partials.automatic-translation')
</body>
</html>
