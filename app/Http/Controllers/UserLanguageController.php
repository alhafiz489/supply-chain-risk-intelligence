<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\AutomaticTranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class UserLanguageController extends Controller
{
    private const LANGUAGE_ALIASES = [
        'ara' => 'ar', 'chi' => 'zh-Hans', 'zho' => 'zh-Hans',
        'zh' => 'zh-Hans', 'jpn' => 'ja',
        'eng' => 'en', 'ind' => 'id', 'fra' => 'fr', 'fre' => 'fr',
        'deu' => 'de', 'ger' => 'de', 'spa' => 'es', 'por' => 'pt',
        'rus' => 'ru', 'kor' => 'ko', 'hin' => 'hi', 'tha' => 'th',
        'vie' => 'vi', 'ita' => 'it', 'nld' => 'nl', 'dut' => 'nl',
        'tur' => 'tr', 'fas' => 'fa', 'per' => 'fa', 'heb' => 'he',
        'urd' => 'ur', 'ben' => 'bn', 'msa' => 'ms', 'may' => 'ms',
    ];

    private const NATIVE_LANGUAGE_NAMES = [
        'ar' => 'العربية', 'zh-Hans' => '中文', 'ja' => '日本語',
        'en' => 'English', 'id' => 'Bahasa Indonesia', 'fr' => 'Français',
        'de' => 'Deutsch', 'es' => 'Español', 'pt' => 'Português',
        'ru' => 'Русский', 'ko' => '한국어', 'hi' => 'हिन्दी',
        'th' => 'ไทย', 'vi' => 'Tiếng Việt', 'it' => 'Italiano',
        'nl' => 'Nederlands', 'tr' => 'Türkçe', 'fa' => 'فارسی',
        'he' => 'עברית', 'ur' => 'اردو', 'bn' => 'বাংলা',
        'ms' => 'Bahasa Melayu',
    ];

    /**
     * Mengirim 250 pilihan negara dan satu bahasa utama masing-masing.
     */
    public function index(): JsonResponse
    {
        $supportedLocales = $this->supportedLocales();

        $countries = Country::query()
            ->whereNotNull('primary_language_code')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'iso2',
                'flag_url',
                'primary_language_code',
                'primary_language_name',
                'primary_language_native_name',
                'text_direction',
            ])
            ->map(function (Country $country): array {
                return [
                    'id' => $country->id,
                    'country_name' => $country->name,
                    'iso2' => $country->iso2,
                    'flag_url' => $country->flag_url,
                    'language_code' => $this->providerLocale(
                        (string) $country->primary_language_code
                    ),
                    'language_name' =>
                        $country->primary_language_name,
                    'language_native_name' => $this->nativeLanguageName(
                        (string) $country->primary_language_code,
                        $country->primary_language_native_name
                            ?: $country->primary_language_name
                    ),
                    'text_direction' =>
                        $country->text_direction,
                ];
            })
            ->when(
                is_array($supportedLocales),
                fn ($items) => $items->filter(
                    fn (array $item) => in_array(
                        $item['language_code'],
                        $supportedLocales,
                        true
                    )
                )
            )
            ->values();

        return response()->json([
            'success' => true,
            'message' =>
                'Daftar bahasa utama negara berhasil diambil.',
            'data' => $countries,
            'meta' => [
                'total' => $countries->count(),
            ],
        ]);
    }

    /**
     * Menyimpan negara dan bahasa pilihan pengguna ke session.
     *
     * Indonesia dan English langsung memakai file bahasa Laravel.
     * Bahasa lain diterjemahkan otomatis dari antarmuka English,
     * sementara locale Laravel tetap English agar key tidak mentah
     * agar tidak menampilkan key mentah seperti messages.app_title.
     */
    public function switch(
        Country $country
    ): RedirectResponse {
        abort_if(
            $country->primary_language_code === null,
            422,
            'Bahasa utama negara belum tersedia.'
        );

        $targetLocale = $this->providerLocale(
            (string) $country->primary_language_code
        );

        if (strtolower((string) config('services.translation.provider')) === 'libretranslate') {
            abort_unless(
                in_array(
                    $targetLocale,
                    $this->supportedLocales() ?? [],
                    true
                ),
                422,
                'Bahasa ini belum didukung oleh provider terjemahan.'
            );
        }

        $manualLocales = [
            'id',
            'en',
        ];

        $applicationLocale = in_array(
            $targetLocale,
            $manualLocales,
            true
        )
            ? $targetLocale
            : 'en';

        session([
            'locale' => $applicationLocale,

            'translation_target_locale' =>
                $targetLocale,

            'translation_mode' => in_array(
                $targetLocale,
                $manualLocales,
                true
            )
                ? 'manual'
                : 'automatic',

            'selected_language_country_id' =>
                $country->id,

            'selected_language_country_name' =>
                $country->name,

            'selected_language_country_iso2' =>
                $country->iso2,

            'selected_language_country_flag_url' =>
                $country->flag_url,

            'selected_language_name' =>
                $country->primary_language_name,

            'selected_language_native_name' => $this->nativeLanguageName(
                (string) $country->primary_language_code,
                $country->primary_language_native_name
                    ?: $country->primary_language_name
            ),

            'selected_text_direction' =>
                $country->text_direction ?? 'ltr',
        ]);

        app()->setLocale($applicationLocale);

        $languageName = $this->nativeLanguageName(
            (string) $country->primary_language_code,
            $country->primary_language_native_name
                ?: $country->primary_language_name
                ?: strtoupper($targetLocale)
        );

        $message = $targetLocale === 'id'
            ? "Bahasa pengguna diubah ke {$languageName}."
            : "Language changed to {$languageName}.";

        return back()->with(
            'language_success',
            $message
        );
    }

    public function translate(
        Request $request,
        AutomaticTranslationService $translation
    ): JsonResponse {
        $validated = $request->validate([
            'texts' => ['required', 'array', 'max:50'],
            'texts.*' => ['required', 'string', 'max:1000'],
        ]);
        $targetLocale = (string) $request->session()->get(
            'translation_target_locale',
            'en'
        );

        if (in_array(strtolower($targetLocale), ['en', 'id'], true)) {
            return response()->json(['data' => array_combine(
                $validated['texts'],
                $validated['texts']
            ) ?: []]);
        }

        return response()->json([
            'data' => $translation->translateMany(
                $validated['texts'],
                $targetLocale,
                'en'
            ),
        ]);
    }

    public function status(): JsonResponse
    {
        $provider = strtolower((string) config('services.translation.provider'));
        $enabled = (bool) config('services.translation.enabled');
        $online = false;
        $languages = [];

        if ($enabled && $provider === 'libretranslate') {
            try {
                $url = rtrim(
                    (string) config('services.translation.libretranslate.url'),
                    '/'
                );
                $response = Http::connectTimeout(2)->timeout(5)->get($url.'/languages');
                $online = $response->successful();

                if ($online) {
                    $languages = collect($response->json())
                        ->pluck('code')
                        ->filter()
                        ->values()
                        ->all();
                }
            } catch (Throwable) {
                $online = false;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'enabled' => $enabled,
                'online' => $online,
                'provider' => $provider,
                'target_locale' => session('translation_target_locale'),
                'languages' => $languages,
            ],
        ]);
    }

    private function providerLocale(string $locale): string
    {
        $locale = strtolower(trim($locale));
        $locale = preg_split('/[-_]/', $locale)[0] ?? 'en';

        return self::LANGUAGE_ALIASES[$locale] ?? $locale;
    }

    private function nativeLanguageName(string $locale, ?string $fallback): string
    {
        $locale = $this->providerLocale($locale);

        return self::NATIVE_LANGUAGE_NAMES[$locale]
            ?? $fallback
            ?? strtoupper($locale);
    }

    private function supportedLocales(): ?array
    {
        if (strtolower((string) config('services.translation.provider')) !== 'libretranslate') {
            return null;
        }

        $fallback = config('services.translation.libretranslate.supported_locales', []);

        try {
            return Cache::remember(
                'libretranslate.supported-targets',
                now()->addMinutes(5),
                function () use ($fallback): array {
                    $url = rtrim(
                        (string) config('services.translation.libretranslate.url'),
                        '/'
                    );
                    $response = Http::connectTimeout(2)
                        ->timeout(5)
                        ->get($url.'/languages');

                    if (! $response->successful()) {
                        return $fallback;
                    }

                    $english = collect($response->json())->firstWhere('code', 'en');
                    $targets = is_array($english) ? ($english['targets'] ?? []) : [];

                    return array_values(array_unique(array_merge(
                        ['en', 'id'],
                        is_array($targets) ? $targets : []
                    )));
                }
            );
        } catch (Throwable) {
            return $fallback;
        }
    }
}
