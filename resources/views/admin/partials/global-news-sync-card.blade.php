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
                        ? 'Sinkronisasi Berita Global'
                        : 'Global News Synchronization'
                    }}
                </h5>

                <p class="text-muted mb-0">
                    {{ app()->getLocale() === 'id'
                        ? 'Ambil berita rantai pasok, logistik, ekonomi, cuaca, dan geopolitik secara bergilir untuk seluruh negara, lalu analisis sentimennya otomatis.'
                        : 'Retrieve supply-chain, logistics, economic, weather, and geopolitical news in rotation for all countries, then analyze sentiment automatically.'
                    }}
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('admin.global-news.sync') }}"
                onsubmit="return confirm(
                    '{{ app()->getLocale() === 'id'
                        ? 'Jalankan sinkronisasi batch berita sekarang?'
                        : 'Run a news synchronization batch now?'
                    }}'
                )"
            >
                @csrf

                <button
                    type="submit"
                    class="btn btn-dark px-4 py-2 fw-semibold text-nowrap"
                    style="border-radius: 12px;"
                >
                    {{ app()->getLocale() === 'id'
                        ? 'Sinkronkan Berita'
                        : 'Synchronize News'
                    }}
                </button>
            </form>
        </div>
    </div>
</div>