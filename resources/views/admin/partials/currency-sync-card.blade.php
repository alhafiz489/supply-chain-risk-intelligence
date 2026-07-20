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
                        ? 'Sinkronisasi Kurs Global'
                        : 'Global Currency Synchronization'
                    }}
                </h5>

                <p class="text-muted mb-0">
                    {{ app()->getLocale() === 'id'
                        ? 'Perbarui nilai 1 unit mata uang ke rupiah dan volatilitas harian 35 hari terakhir dari Frankfurter.'
                        : 'Update the value of one currency unit in rupiah and daily volatility over the last 35 days from Frankfurter.'
                    }}
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('admin.currency.sync') }}"
                onsubmit="return confirm(
                    '{{ app()->getLocale() === 'id'
                        ? 'Sinkronkan kurs global sekarang?'
                        : 'Synchronize global exchange rates now?'
                    }}'
                )"
            >
                @csrf

                <button
                    type="submit"
                    class="btn btn-success px-4 py-2 fw-semibold text-nowrap"
                    style="border-radius: 12px;"
                >
                    {{ app()->getLocale() === 'id'
                        ? 'Sinkronkan Data Kurs'
                        : 'Synchronize Currency Data'
                    }}
                </button>
            </form>
        </div>
    </div>
</div>