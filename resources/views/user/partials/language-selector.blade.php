<div class="dropdown" id="countryLanguageSelector">
    <button
        class="sg-nav-action dropdown-toggle d-inline-flex align-items-center gap-2"
        type="button"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        aria-expanded="false"
        aria-label="Pilih bahasa utama negara"
    >
        @if (session('selected_language_country_flag_url'))
            <img
                src="{{ session('selected_language_country_flag_url') }}"
                alt=""
                width="20"
                height="14"
                class="rounded-1 object-fit-cover"
            >
        @endif

        <span id="selectedCountryLanguageLabel">
            {{ session(
                'selected_language_native_name',
                app()->getLocale() === 'id'
                    ? 'Bahasa Indonesia'
                    : 'English'
            ) }}
        </span>
    </button>

    <div
        class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0 overflow-hidden"
        style="width:min(430px,calc(100vw - 24px));"
    >
        <div class="p-3 border-bottom bg-light">
            <label
                for="countryLanguageSearch"
                class="form-label small fw-bold mb-2"
            >
                {{ app()->getLocale() === 'id'
                    ? 'Pilih negara dan bahasa utama'
                    : 'Choose a country and its primary language'
                }}
            </label>

            <input
                type="search"
                id="countryLanguageSearch"
                class="form-control"
                placeholder="{{ app()->getLocale() === 'id'
                    ? 'Cari negara atau bahasa...'
                    : 'Search country or language...'
                }}"
                autocomplete="off"
            >
        </div>

        <div
            id="countryLanguageList"
            class="list-group list-group-flush overflow-y-auto"
            style="max-height:420px;"
        >
            <div class="p-4 text-center text-muted">
                <span
                    class="spinner-border spinner-border-sm me-2"
                    aria-hidden="true"
                ></span>

                {{ app()->getLocale() === 'id'
                    ? 'Memuat pilihan bahasa...'
                    : 'Loading language choices...'
                }}
            </div>
        </div>

        <div class="px-3 py-2 border-top bg-light small text-muted">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <span>
                    <span id="countryLanguageTotal">0</span>
                    {{ app()->getLocale() === 'id'
                        ? 'negara/wilayah'
                        : 'countries/territories'
                    }}
                </span>
                <span id="translationProviderStatus">
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                </span>
            </div>
        </div>
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selector =
                document.getElementById('countryLanguageSelector');

            const searchInput =
                document.getElementById('countryLanguageSearch');

            const listElement =
                document.getElementById('countryLanguageList');

            const totalElement =
                document.getElementById('countryLanguageTotal');

            const providerStatusElement =
                document.getElementById('translationProviderStatus');

            if (
                !selector
                || !searchInput
                || !listElement
                || !totalElement
                || !providerStatusElement
            ) {
                return;
            }

            let languages = [];
            let hasLoaded = false;

            selector.addEventListener(
                'show.bs.dropdown',
                function () {
                    if (!hasLoaded) {
                        loadCountryLanguages();
                    }

                    window.setTimeout(function () {
                        searchInput.focus();
                    }, 160);
                }
            );

            searchInput.addEventListener(
                'input',
                function () {
                    renderCountryLanguages(
                        searchInput.value
                    );
                }
            );

            async function loadCountryLanguages() {
                try {
                    const response = await fetch(
                        @json(route('user.languages.index')),
                        {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                    const result = await response.json();

                    if (
                        !response.ok
                        || !result.success
                    ) {
                        throw new Error(
                            result.message
                            || 'Bahasa gagal dimuat.'
                        );
                    }

                    languages = Array.isArray(result.data)
                        ? result.data
                        : [];

                    hasLoaded = true;
                    totalElement.textContent =
                        String(languages.length);

                    renderCountryLanguages('');
                    loadProviderStatus();
                } catch (error) {
                    console.error(error);

                    listElement.innerHTML = `
                        <div class="p-4 text-center text-danger">
                            ${escapeHtml(
                                @json(
                                    app()->getLocale() === 'id'
                                        ? 'Pilihan bahasa gagal dimuat.'
                                        : 'Language choices failed to load.'
                                )
                            )}
                        </div>
                    `;
                }
            }

            async function loadProviderStatus() {
                try {
                    const response = await fetch(
                        @json(route('user.translation.status')),
                        { headers: { 'Accept': 'application/json' } }
                    );
                    const result = await response.json();
                    const online = response.ok && result.data?.online;

                    providerStatusElement.innerHTML = online
                        ? `<span class="text-success">● ${escapeHtml(
                            @json(app()->getLocale() === 'id' ? 'Penerjemah aktif' : 'Translator online')
                        )}</span>`
                        : `<span class="text-danger">● ${escapeHtml(
                            @json(app()->getLocale() === 'id' ? 'Penerjemah offline' : 'Translator offline')
                        )}</span>`;
                } catch (error) {
                    providerStatusElement.innerHTML =
                        `<span class="text-danger">● Offline</span>`;
                }
            }

            function renderCountryLanguages(query) {
                const keyword = String(query || '')
                    .trim()
                    .toLocaleLowerCase();

                const filtered = keyword === ''
                    ? languages
                    : languages.filter(function (item) {
                        return [
                            item.country_name,
                            item.iso2,
                            item.language_code,
                            item.language_name,
                            item.language_native_name
                        ]
                            .filter(Boolean)
                            .join(' ')
                            .toLocaleLowerCase()
                            .includes(keyword);
                    });

                totalElement.textContent =
                    String(filtered.length);

                if (filtered.length === 0) {
                    listElement.innerHTML = `
                        <div class="p-4 text-center text-muted">
                            ${escapeHtml(
                                @json(
                                    app()->getLocale() === 'id'
                                        ? 'Negara atau bahasa tidak ditemukan.'
                                        : 'Country or language not found.'
                                )
                            )}
                        </div>
                    `;

                    return;
                }

                listElement.innerHTML = filtered
                    .map(function (item) {
                        const switchUrl =
                            @json(url('/user/language'))
                            + '/'
                            + encodeURIComponent(item.id);

                        const flag = item.flag_url
                            ? `
                                <img
                                    src="${escapeAttribute(item.flag_url)}"
                                    alt=""
                                    width="28"
                                    height="19"
                                    class="rounded-1 object-fit-cover flex-shrink-0"
                                >
                            `
                            : `
                                <span
                                    class="d-inline-flex align-items-center justify-content-center rounded-1 bg-light border flex-shrink-0"
                                    style="width:28px;height:19px;font-size:9px;"
                                >
                                    ${escapeHtml(item.iso2 || '--')}
                                </span>
                            `;

                        return `
                            <a
                                href="${switchUrl}"
                                class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3"
                            >
                                ${flag}

                                <span class="min-w-0 flex-grow-1">
                                    <span class="d-block fw-bold text-dark">
                                        ${escapeHtml(
                                            item.country_name || '-'
                                        )}
                                    </span>

                                    <span class="d-block small text-muted text-truncate">
                                        ${escapeHtml(
                                            item.language_native_name
                                            || item.language_name
                                            || item.language_code
                                            || '-'
                                        )}

                                        <span class="ms-1">
                                            (${escapeHtml(
                                                item.language_code || '-'
                                            )})
                                        </span>
                                    </span>
                                </span>

                                ${
                                    item.text_direction === 'rtl'
                                        ? '<span class="badge text-bg-light">RTL</span>'
                                        : ''
                                }
                            </a>
                        `;
                    })
                    .join('');
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function escapeAttribute(value) {
                return escapeHtml(value);
            }
        });
    </script>
@endonce
