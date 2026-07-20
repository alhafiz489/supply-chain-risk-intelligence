<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">

    <title>
        {{ app()->getLocale() === 'id'
            ? 'Daftar - SupplyGuard'
            : 'Register - SupplyGuard'
        }}
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
            max-width: 500px;
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
                {{ app()->getLocale() === 'id'
                    ? 'Buat Akun Baru'
                    : 'Create an Account'
                }}
            </h1>

            <p class="text-muted mb-4">
                {{ app()->getLocale() === 'id'
                    ? 'Daftar untuk menyimpan dan memantau negara favorit.'
                    : 'Register to save and monitor favorite countries.'
                }}
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('auth.register') }}"
            >
                @csrf

                <div class="mb-3">
                    <label
                        for="name"
                        class="form-label fw-semibold"
                    >
                        {{ app()->getLocale() === 'id'
                            ? 'Nama Lengkap'
                            : 'Full Name'
                        }}
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Masukkan nama lengkap"
                        autocomplete="name"
                        required
                        autofocus
                    >
                </div>

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
                        placeholder="Minimal 8 karakter, berisi huruf dan angka"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label
                        for="password_confirmation"
                        class="form-label fw-semibold"
                    >
                        {{ app()->getLocale() === 'id'
                            ? 'Konfirmasi Password'
                            : 'Confirm Password'
                        }}
                    </label>

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="Ulangi password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="btn btn-primary submit-button w-100"
                >
                    {{ app()->getLocale() === 'id'
                        ? 'Daftar'
                        : 'Register'
                    }}
                </button>
            </form>

            <p class="text-center text-muted mt-4 mb-0">
                {{ app()->getLocale() === 'id'
                    ? 'Sudah memiliki akun?'
                    : 'Already have an account?'
                }}

                <a
                    href="{{ route('login') }}"
                    class="fw-semibold text-decoration-none"
                >
                    {{ app()->getLocale() === 'id'
                        ? 'Masuk'
                        : 'Sign in'
                    }}
                </a>
            </p>

        </div>
    </div>
</main>

@include('user.partials.automatic-translation')
</body>
</html>
