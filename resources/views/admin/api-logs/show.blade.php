<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ app()->getLocale() === 'id' ? 'Detail Log API - SupplyGuard' : 'API Log Details - SupplyGuard' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: #f4f7fb;
            color: #1f2937;
        }

        .page-wrapper {
            max-width: 1180px;
            margin: 0 auto;
            padding: 32px 20px;
        }

        .detail-card {
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .detail-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-body {
            padding: 24px;
        }

        .info-box {
            height: 100%;
            padding: 18px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #f8fafc;
        }

        .info-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .info-value {
            margin-top: 6px;
            font-weight: 800;
            word-break: break-word;
        }

        pre {
            max-height: 500px;
            margin: 0;
            padding: 18px;
            overflow: auto;
            border-radius: 14px;
            background: #0f172a;
            color: #e2e8f0;
            font-size: 13px;
            line-height: 1.55;
        }
    </style>
</head>

<body>
<main class="page-wrapper">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                {{ app()->getLocale() === 'id' ? 'Detail Log API' : 'API Log Details' }}
            </h1>
            <p class="text-muted mb-0">
                {{ app()->getLocale() === 'id' ? 'Informasi lengkap permintaan dan respons API.' : 'Complete API request and response information.' }}
            </p>
        </div>

        <a href="{{ route('admin.api-logs.index') }}" class="btn btn-outline-secondary">
            {{ app()->getLocale() === 'id' ? 'Kembali' : 'Back' }}
        </a>
    </div>

    <section class="detail-card">
        <div class="detail-header">
            <h2 class="h5 fw-bold mb-1">{{ $apiLog->method }} {{ $apiLog->endpoint }}</h2>
            <small class="text-muted">{{ optional($apiLog->created_at)->format('d/m/Y H:i:s') }}</small>
        </div>

        <div class="detail-body">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-label">Status Code</div>
                        <div class="info-value">{{ $apiLog->status_code }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-label">{{ app()->getLocale() === 'id' ? 'Waktu Respons' : 'Response Time' }}</div>
                        <div class="info-value">{{ number_format($apiLog->response_time_ms) }} ms</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-label">IP Address</div>
                        <div class="info-value">{{ $apiLog->ip_address ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="info-box">
                        <div class="info-label">Route Name</div>
                        <div class="info-value">{{ $apiLog->route_name ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="info-box">
                        <div class="info-label">User Agent</div>
                        <div class="info-value">{{ $apiLog->user_agent ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h3 class="h6 fw-bold mb-3">{{ app()->getLocale() === 'id' ? 'Payload Permintaan' : 'Request Payload' }}</h3>
                <pre>{{ json_encode($apiLog->request_payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>

            <div>
                <h3 class="h6 fw-bold mb-3">{{ app()->getLocale() === 'id' ? 'Payload Respons' : 'Response Payload' }}</h3>
                <pre>{{ json_encode($apiLog->response_payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>