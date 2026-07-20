<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RestCountriesService
{
    private const ENDPOINT =
        'https://api.restcountries.com/countries/v5';

    private const PAGE_SIZE = 100;

    /**
     * Kode bahasa yang menggunakan arah tulisan kanan ke kiri.
     */
    private const RTL_LANGUAGE_CODES = [
        'ar',
        'arc',
        'ckb',
        'dv',
        'fa',
        'he',
        'ku',
        'nqo',
        'ps',
        'sd',
        'ug',
        'ur',
        'yi',
    ];

    public function syncAll(): array
    {
        $apiKey = (string) config(
            'services.rest_countries.key'
        );

        if ($apiKey === '') {
            throw new RuntimeException(
                'REST_COUNTRIES_API_KEY belum diisi pada file .env.'
            );
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $languageFallbacks = 0;
        $offset = 0;
        $pages = 0;

        do {
            $response = Http::acceptJson()
                ->withToken($apiKey)
                ->connectTimeout(15)
                ->timeout(60)
                ->retry(3, 1000, null, false)
                ->get(self::ENDPOINT, [
                    'limit' => self::PAGE_SIZE,
                    'offset' => $offset,
                    'response_fields' => implode(',', [
                        'names.common',
                        'names.official',
                        'codes.alpha_2',
                        'codes.alpha_3',
                        'capitals',
                        'region',
                        'subregion',
                        'currencies',
                        'coordinates',
                        'population',
                        'languages',
                        'classification.sovereign',
                        'classification.un_member',
                        'classification.dependency',
                        'flag.url_png',
                    ]),
                ]);

            $this->ensureSuccessful($response);

            $payload = $response->json();

            $objects = data_get(
                $payload,
                'data.objects',
                []
            );

            $meta = data_get(
                $payload,
                'data.meta',
                []
            );

            if (! is_array($objects)) {
                throw new RuntimeException(
                    'Format data negara dari REST Countries tidak valid.'
                );
            }

            foreach ($objects as $item) {
                if (! is_array($item)) {
                    $skipped++;
                    continue;
                }

                $iso2 = strtoupper(
                    trim(
                        (string) data_get(
                            $item,
                            'codes.alpha_2',
                            ''
                        )
                    )
                );

                if ($iso2 === '') {
                    $skipped++;
                    continue;
                }

                $population = max(
                    0,
                    (int) data_get(
                        $item,
                        'population',
                        0
                    )
                );

                $country = Country::firstOrNew([
                    'iso2' => $iso2,
                ]);

                $wasExisting = $country->exists;

                $primaryLanguage = $this->primaryLanguage(
                    data_get(
                        $item,
                        'languages',
                        []
                    )
                );

                /*
                |--------------------------------------------------------------------------
                | Fallback bahasa
                |--------------------------------------------------------------------------
                |
                | Data bahasa lama dipertahankan jika API tidak mengirim bahasa.
                | Apabila negara belum memiliki bahasa sama sekali, English dipakai
                | sebagai fallback agar semua negara tetap dapat menjadi pilihan.
                |
                */

                if ($primaryLanguage === null) {
                    $languageFallbacks++;

                    $primaryLanguage = [
                        'code' => $this->nullableString(
                            $country->primary_language_code
                        ) ?? 'en',

                        'name' => $this->nullableString(
                            $country->primary_language_name
                        ) ?? 'English',

                        'native_name' => $this->nullableString(
                            $country->primary_language_native_name
                        ) ?? 'English',
                    ];
                }

                $languageCode = $this->normalizeLanguageCode(
                    $primaryLanguage['code']
                );

                $country->fill([
                    'name' => (string) data_get(
                        $item,
                        'names.common',
                        $iso2
                    ),

                    'official_name' => data_get(
                        $item,
                        'names.official'
                    ),

                    'iso3' => $this->nullableUppercase(
                        data_get(
                            $item,
                            'codes.alpha_3'
                        )
                    ),

                    'region' => data_get(
                        $item,
                        'region'
                    ),

                    'subregion' => data_get(
                        $item,
                        'subregion'
                    ),

                    'capital' => $this->capitalName(
                        data_get(
                            $item,
                            'capitals',
                            []
                        )
                    ),

                    'currency_code' => $this->currencyCode(
                        data_get(
                            $item,
                            'currencies',
                            []
                        )
                    ),

                    /*
                    |--------------------------------------------------------------------------
                    | Satu bahasa utama per negara
                    |--------------------------------------------------------------------------
                    */

                    'primary_language_code' =>
                        $languageCode,

                    'primary_language_name' =>
                        $primaryLanguage['name'],

                    'primary_language_native_name' =>
                        $primaryLanguage['native_name'],

                    'text_direction' =>
                        $this->textDirection(
                            $languageCode
                        ),

                    'latitude' => $this->nullableFloat(
                        data_get(
                            $item,
                            'coordinates.lat'
                        )
                    ),

                    'longitude' => $this->nullableFloat(
                        data_get(
                            $item,
                            'coordinates.lng'
                        )
                    ),

                    'population' => $population,

                    'population_million' => round(
                        $population / 1_000_000,
                        2
                    ),

                    'is_sovereign' => (bool) data_get(
                        $item,
                        'classification.sovereign',
                        false
                    ),

                    'is_un_member' => (bool) data_get(
                        $item,
                        'classification.un_member',
                        false
                    ),

                    'is_dependency' => (bool) data_get(
                        $item,
                        'classification.dependency',
                        false
                    ),

                    'flag_url' => data_get(
                        $item,
                        'flag.url_png'
                    ),

                    'data_completeness_percent' => max(
                        (int) $country
                            ->data_completeness_percent,
                        20
                    ),

                    'risk_data_status' =>
                        $country->risk_data_status === 'ready'
                            ? 'ready'
                            : 'partial',

                    'master_synced_at' => now(),
                ]);

                $country->save();

                if ($wasExisting) {
                    $updated++;
                } else {
                    $created++;
                }
            }

            $pages++;

            $more = (bool) data_get(
                $meta,
                'more',
                false
            );

            $count = (int) data_get(
                $meta,
                'count',
                count($objects)
            );

            $offset += $count;

            if ($pages >= 10) {
                throw new RuntimeException(
                    'Sinkronisasi dihentikan karena melebihi batas halaman.'
                );
            }
        } while ($more);

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'language_fallbacks' => $languageFallbacks,
            'total_in_database' => Country::count(),
            'pages' => $pages,
        ];
    }

    private function ensureSuccessful(
        Response $response
    ): void {
        if ($response->successful()) {
            return;
        }

        $message = data_get(
            $response->json(),
            'errors.0.message',
            'REST Countries gagal merespons.'
        );

        throw new RuntimeException(
            "{$message} HTTP status: "
            .$response->status()
            .'.'
        );
    }

    /**
     * Memilih satu bahasa pertama yang valid dari data API.
     *
     * Method ini dibuat fleksibel karena data bahasa dapat memakai
     * nama properti ISO/BCP yang berbeda.
     */
    private function primaryLanguage(
        mixed $languages
    ): ?array {
        if (
            ! is_array($languages)
            || $languages === []
        ) {
            return null;
        }

        $items = array_is_list($languages)
            ? $languages
            : $this->normalizeAssociativeLanguages(
                $languages
            );

        foreach ($items as $language) {
            if (is_string($language)) {
                $name = $this->nullableString(
                    $language
                );

                if ($name === null) {
                    continue;
                }

                return [
                    'code' => 'en',
                    'name' => $name,
                    'native_name' => $name,
                ];
            }

            if (! is_array($language)) {
                continue;
            }

            $code = $this->firstAvailableString([
                data_get(
                    $language,
                    'iso_639_1'
                ),
                data_get(
                    $language,
                    'iso639_1'
                ),
                data_get(
                    $language,
                    'iso_639_2'
                ),
                data_get(
                    $language,
                    'iso639_2'
                ),
                data_get(
                    $language,
                    'iso_639_3'
                ),
                data_get(
                    $language,
                    'iso639_3'
                ),
                data_get(
                    $language,
                    'bcp_47'
                ),
                data_get(
                    $language,
                    'bcp47'
                ),
                data_get(
                    $language,
                    'tag'
                ),
                data_get(
                    $language,
                    'code'
                ),
            ]);

            $name = $this->firstAvailableString([
                data_get(
                    $language,
                    'name'
                ),
                data_get(
                    $language,
                    'english_name'
                ),
                data_get(
                    $language,
                    'english'
                ),
            ]);

            $nativeName =
                $this->firstAvailableString([
                    data_get(
                        $language,
                        'native_name'
                    ),
                    data_get(
                        $language,
                        'nativeName'
                    ),
                    data_get(
                        $language,
                        'native'
                    ),
                ]);

            if (
                $code === null
                && $name === null
                && $nativeName === null
            ) {
                continue;
            }

            return [
                'code' => $code ?? 'en',
                'name' =>
                    $name
                    ?? $nativeName
                    ?? 'English',

                'native_name' =>
                    $nativeName
                    ?? $name
                    ?? 'English',
            ];
        }

        return null;
    }

    /**
     * Mengubah format bahasa associative menjadi daftar bahasa.
     *
     * Contoh yang didukung:
     * [
     *     'eng' => 'English',
     *     'fra' => ['name' => 'French', ...],
     * ]
     */
    private function normalizeAssociativeLanguages(
        array $languages
    ): array {
        $items = [];

        foreach ($languages as $key => $value) {
            if (is_string($value)) {
                $items[] = [
                    'code' => is_string($key)
                        ? $key
                        : null,
                    'name' => $value,
                    'native_name' => $value,
                ];

                continue;
            }

            if (is_array($value)) {
                if (
                    is_string($key)
                    && ! isset($value['code'])
                    && ! isset($value['iso_639_1'])
                    && ! isset($value['iso639_1'])
                    && ! isset($value['iso_639_3'])
                    && ! isset($value['iso639_3'])
                ) {
                    $value['code'] = $key;
                }

                $items[] = $value;
            }
        }

        return $items;
    }

    private function normalizeLanguageCode(
        mixed $value
    ): string {
        $value = strtolower(
            trim((string) $value)
        );

        if ($value === '') {
            return 'en';
        }

        /*
         * BCP 47 seperti pt-BR atau zh_Hans disimpan sebagai
         * bahasa dasarnya agar mudah dipakai sebagai locale.
         */
        $parts = preg_split(
            '/[-_]/',
            $value
        );

        $code = (string) (
            $parts[0]
            ?? 'en'
        );

        return preg_match(
            '/^[a-z]{2,3}$/',
            $code
        ) === 1
            ? $code
            : 'en';
    }

    private function textDirection(
        string $languageCode
    ): string {
        return in_array(
            strtolower($languageCode),
            self::RTL_LANGUAGE_CODES,
            true
        )
            ? 'rtl'
            : 'ltr';
    }

    private function firstAvailableString(
        array $values
    ): ?string {
        foreach ($values as $value) {
            $value = $this->nullableString(
                $value
            );

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    private function capitalName(
        mixed $capitals
    ): ?string {
        if (
            ! is_array($capitals)
            || $capitals === []
        ) {
            return null;
        }

        foreach ($capitals as $capital) {
            if (
                is_array($capital)
                && (bool) data_get(
                    $capital,
                    'attributes.primary',
                    false
                )
            ) {
                return $this->nullableString(
                    data_get(
                        $capital,
                        'name'
                    )
                );
            }
        }

        $first = $capitals[0] ?? null;

        if (is_string($first)) {
            return $this->nullableString(
                $first
            );
        }

        return is_array($first)
            ? $this->nullableString(
                data_get(
                    $first,
                    'name'
                )
            )
            : null;
    }

    private function currencyCode(
        mixed $currencies
    ): ?string {
        if (
            ! is_array($currencies)
            || $currencies === []
        ) {
            return null;
        }

        if (array_is_list($currencies)) {
            $first = $currencies[0] ?? null;

            if (is_string($first)) {
                return $this->nullableUppercase(
                    $first
                );
            }

            if (is_array($first)) {
                return $this->nullableUppercase(
                    data_get(
                        $first,
                        'code'
                    )
                );
            }

            return null;
        }

        $firstKey = array_key_first(
            $currencies
        );

        if (
            is_string($firstKey)
            && preg_match(
                '/^[A-Z]{3}$/i',
                $firstKey
            ) === 1
        ) {
            return strtoupper(
                $firstKey
            );
        }

        $first = reset(
            $currencies
        );

        return is_array($first)
            ? $this->nullableUppercase(
                data_get(
                    $first,
                    'code'
                )
            )
            : null;
    }

    private function nullableString(
        mixed $value
    ): ?string {
        if (
            is_array($value)
            || is_object($value)
        ) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value === ''
            ? null
            : $value;
    }

    private function nullableUppercase(
        mixed $value
    ): ?string {
        $value = $this->nullableString(
            $value
        );

        return $value === null
            ? null
            : strtoupper($value);
    }

    private function nullableFloat(
        mixed $value
    ): ?float {
        return is_numeric($value)
            ? (float) $value
            : null;
    }
}
