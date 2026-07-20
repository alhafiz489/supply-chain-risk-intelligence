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
                        ? 'Sinkronisasi Pelabuhan Global'
                        : 'Global Port Synchronization'
                    }}
                </h5>

                <p class="text-muted mb-0">
                    {{ app()->getLocale() === 'id'
                        ? 'Perbarui referensi lokasi pelabuhan, UN/LOCODE, fungsi, status referensi, dan koordinat global. Data ini tidak memuat kongesti atau delay realtime.'
                        : 'Update global port locations, UN/LOCODE, function, reference status, and coordinates. This dataset does not provide real-time congestion or delay data.'
                    }}
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('admin.global-ports.sync') }}"
                onsubmit="return confirm(
                    '{{ app()->getLocale() === 'id'
                        ? 'Sinkronkan referensi pelabuhan global sekarang? Proses pertama dapat memerlukan beberapa menit.'
                        : 'Synchronize global port references now? The first run may take several minutes.'
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
                        ? 'Sinkronkan Pelabuhan'
                        : 'Synchronize Ports'
                    }}
                </button>
            </form>
        </div>
    </div>
</div>