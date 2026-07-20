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
                        ? 'Sinkronisasi Ekonomi World Bank'
                        : 'World Bank Economic Synchronization'
                    }}
                </h5>

                <p class="text-muted mb-0">
                    {{ app()->getLocale() === 'id'
                        ? 'Perbarui GDP, inflasi, populasi, serta tahun data terbaru seluruh negara. Indikator ekonomi bersifat tahunan sehingga mengikuti pembaruan World Bank.'
                        : 'Update GDP, inflation, population, and the latest data year for all countries. Economic indicators are annual and follow World Bank updates.'
                    }}
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('admin.economy.sync') }}"
                onsubmit="return confirm(
                    '{{ app()->getLocale() === 'id'
                        ? 'Sinkronkan data ekonomi seluruh negara sekarang?'
                        : 'Synchronize economic data for all countries now?'
                    }}'
                )"
            >
                @csrf

                <button
                    type="submit"
                    class="btn btn-primary px-4 py-2 fw-semibold text-nowrap"
                    style="border-radius: 12px;"
                >
                    {{ app()->getLocale() === 'id'
                        ? 'Sinkronkan Data Ekonomi'
                        : 'Synchronize Economic Data'
                    }}
                </button>
            </form>
        </div>
    </div>
</div>