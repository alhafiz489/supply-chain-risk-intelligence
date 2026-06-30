<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.app_title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            SupplyGuard
        </a>

        <div class="d-flex gap-2 align-items-center">
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    {{ __('messages.choose_language') }}
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('language.switch', ['locale' => 'en']) }}">
                            🇬🇧 {{ __('messages.english') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('language.switch', ['locale' => 'id']) }}">
                            🇮🇩 {{ __('messages.indonesian') }}
                        </a>
                    </li>
                </ul>
            </div>

            <a href="#" class="btn btn-primary">
                {{ __('messages.login') }}
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-5">
            <h1 class="fw-bold mb-3">
                {{ __('messages.app_title') }}
            </h1>

            <p class="text-muted fs-5">
                {{ __('messages.app_subtitle') }}
            </p>

            <hr>

            <div class="row g-3 mt-3">
                <div class="col-md-3">
                    <div class="p-3 bg-white border rounded-3">
                        <div class="text-muted">{{ __('messages.gdp') }}</div>
                        <h3 class="fw-bold">-</h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 bg-white border rounded-3">
                        <div class="text-muted">{{ __('messages.inflation') }}</div>
                        <h3 class="fw-bold">-</h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 bg-white border rounded-3">
                        <div class="text-muted">{{ __('messages.currency') }}</div>
                        <h3 class="fw-bold">-</h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 bg-white border rounded-3">
                        <div class="text-muted">{{ __('messages.risk_score') }}</div>
                        <h3 class="fw-bold">-</h3>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="form-label fw-semibold">
                    {{ __('messages.select_country') }}
                </label>

                <div class="row g-2">
                    <div class="col-md-10">
                        <select class="form-select">
                            <option>{{ __('messages.select_country') }}</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            {{ __('messages.analyze') }}
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>