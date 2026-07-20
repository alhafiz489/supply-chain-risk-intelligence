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

<div
    class="card border-0 shadow-sm mb-4"
    style="border-radius: 18px;"
>
    <div class="card-body p-4">
        <div
            class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3"
        >
            <div>
                <h5 class="fw-bold mb-1">
                    {{ app()->getLocale() === 'id'
                        ? 'Perhitungan Risiko Otomatis'
                        : 'Automatic Risk Calculation'
                    }}
                </h5>

                <p class="text-muted mb-0">
                    {{ app()->getLocale() === 'id'
                        ? 'Hitung ulang seluruh negara atau buka riwayat hasil perhitungan risiko.'
                        : 'Recalculate all countries or open the risk calculation history.'
                    }}
                </p>
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2">
                <a
                    href="{{ route('admin.risks.index') }}"
                    class="btn btn-outline-primary px-4 py-2 fw-semibold text-nowrap"
                    style="border-radius: 12px;"
                >
                    {{ app()->getLocale() === 'id'
                        ? 'Lihat Riwayat Risiko'
                        : 'View Risk History'
                    }}
                </a>

                <form
                    method="POST"
                    action="{{ route('admin.risks.recalculate') }}"
                    onsubmit="return confirm(
                        '{{ app()->getLocale() === 'id'
                            ? 'Hitung ulang risiko untuk seluruh negara sekarang?'
                            : 'Recalculate risk scores for all countries now?'
                        }}'
                    )"
                >
                    @csrf

                    <button
                        type="submit"
                        class="btn btn-primary px-4 py-2 fw-semibold text-nowrap w-100"
                        style="border-radius: 12px;"
                    >
                        {{ app()->getLocale() === 'id'
                            ? 'Hitung Ulang Semua Risiko'
                            : 'Recalculate All Risks'
                        }}
                    </button>
                </form>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-6 col-md">
                <small class="text-muted d-block">Weather</small>
                <strong>27%</strong>
            </div>

            <div class="col-6 col-md">
                <small class="text-muted d-block">Inflation</small>
                <strong>21%</strong>
            </div>

            <div class="col-6 col-md">
                <small class="text-muted d-block">Currency</small>
                <strong>18%</strong>
            </div>

            <div class="col-6 col-md">
                <small class="text-muted d-block">News</small>
                <strong>22%</strong>
            </div>

            <div class="col-6 col-md">
                <small class="text-muted d-block">Port</small>
                <strong>12%</strong>
            </div>
        </div>
    </div>
</div>