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
                        ? 'Sinkronisasi Cuaca Open-Meteo'
                        : 'Open-Meteo Weather Synchronization'
                    }}
                </h5>

                <p class="text-muted mb-0">
                    {{ app()->getLocale() === 'id'
                        ? 'Perbarui suhu, curah hujan, kecepatan angin, dan kondisi cuaca seluruh negara. Skor risiko akan dihitung ulang setelah sinkronisasi.'
                        : 'Update temperature, precipitation, wind speed, and weather conditions for all countries. Risk scores will be recalculated afterward.'
                    }}
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('admin.weather.sync') }}"
                onsubmit="return confirm(
                    '{{ app()->getLocale() === 'id'
                        ? 'Sinkronkan data cuaca seluruh negara sekarang?'
                        : 'Synchronize weather data for all countries now?'
                    }}'
                )"
            >
                @csrf

                <button
                    type="submit"
                    class="btn btn-info px-4 py-2 fw-semibold text-nowrap"
                    style="border-radius: 12px;"
                >
                    {{ app()->getLocale() === 'id'
                        ? 'Sinkronkan Data Cuaca'
                        : 'Synchronize Weather Data'
                    }}
                </button>
            </form>
        </div>
    </div>
</div>