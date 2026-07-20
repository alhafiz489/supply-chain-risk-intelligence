<?php

namespace App\Services;

use App\Models\TranslationCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class AutomaticTranslationService
{
    /**
     * Menerjemahkan banyak teks dalam sedikit request provider.
     * Hasil selalu memiliki key yang sama dengan input.
     */
    public function translateMany(
        array $texts,
        string $targetLocale,
        string $sourceLocale = 'en'
    ): array {
        $texts = array_values(array_unique(array_filter(
            array_map(fn ($text) => trim((string) $text), $texts),
            fn (string $text) => $text !== ''
        )));

        $results = array_combine($texts, $texts) ?: [];
        $sourceLocale = $this->normalizeLocale($sourceLocale);
        $targetLocale = $this->normalizeTargetLocale($targetLocale);

        if ($texts === [] || $sourceLocale === $targetLocale
            || ! (bool) config('services.translation.enabled', false)) {
            return $results;
        }

        $hashes = array_map(fn (string $text) => hash('sha256', $text), $texts);
        $cached = TranslationCache::query()->successful()
            ->where('source_locale', $sourceLocale)
            ->where('target_locale', $targetLocale)
            ->whereIn('source_hash', $hashes)
            ->get()
            ->keyBy('source_hash');

        $pending = [];

        foreach ($texts as $text) {
            $record = $cached->get(hash('sha256', $text));

            if ($record !== null) {
                $results[$text] = (string) $record->translated_text;
            } else {
                $pending[] = $text;
            }
        }

        try {
            foreach (array_chunk($pending, 50) as $chunk) {
                $translated = $this->translateBatchWithProvider(
                    $chunk,
                    $sourceLocale,
                    $targetLocale
                );

                foreach ($chunk as $index => $text) {
                    $value = trim((string) ($translated[$index] ?? ''));

                    if ($value === '') {
                        continue;
                    }

                    $results[$text] = $value;
                    TranslationCache::query()->updateOrCreate(
                        [
                            'source_locale' => $sourceLocale,
                            'target_locale' => $targetLocale,
                            'source_hash' => hash('sha256', $text),
                        ],
                        [
                            'source_text' => $text,
                            'translated_text' => $value,
                            'provider' => config('services.translation.provider'),
                            'status' => 'success',
                            'error_message' => null,
                            'translated_at' => now(),
                        ]
                    );
                }
            }
        } catch (Throwable $exception) {
            Log::warning('Terjemahan batch SupplyGuard gagal.', [
                'target_locale' => $targetLocale,
                'message' => $exception->getMessage(),
            ]);
        }

        return $results;
    }

    /**
     * Menerjemahkan teks dan menyimpan hasilnya di database.
     *
     * Jika provider gagal atau bahasa belum didukung, teks sumber
     * dikembalikan agar halaman pengguna tetap dapat dibuka.
     */
    public function translate(
        string $text,
        string $targetLocale,
        string $sourceLocale = 'en'
    ): string {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        $sourceLocale = $this->normalizeLocale(
            $sourceLocale
        );

        $targetLocale = $this->normalizeTargetLocale(
            $targetLocale
        );

        if ($sourceLocale === $targetLocale) {
            return $text;
        }

        if (! (bool) config(
            'services.translation.enabled',
            false
        )) {
            return $text;
        }

        $hash = hash('sha256', $text);

        $cached = TranslationCache::query()
            ->successful()
            ->where('source_locale', $sourceLocale)
            ->where('target_locale', $targetLocale)
            ->where('source_hash', $hash)
            ->first();

        if ($cached !== null) {
            return (string) $cached->translated_text;
        }

        $record = TranslationCache::query()
            ->firstOrCreate(
                [
                    'source_locale' => $sourceLocale,
                    'target_locale' => $targetLocale,
                    'source_hash' => $hash,
                ],
                [
                    'source_text' => $text,
                    'status' => 'pending',
                ]
            );

        try {
            $provider = strtolower(
                (string) config(
                    'services.translation.provider',
                    'libretranslate'
                )
            );

            $translatedText = match ($provider) {
                'google' => $this->translateWithGoogle(
                    $text,
                    $sourceLocale,
                    $targetLocale
                ),

                'libretranslate' =>
                    $this->translateWithLibreTranslate(
                        $text,
                        $sourceLocale,
                        $targetLocale
                    ),

                default => throw new RuntimeException(
                    "Provider terjemahan {$provider} tidak didukung."
                ),
            };

            $translatedText = trim($translatedText);

            if ($translatedText === '') {
                throw new RuntimeException(
                    'Provider mengembalikan hasil terjemahan kosong.'
                );
            }

            $record->forceFill([
                'source_text' => $text,
                'translated_text' => $translatedText,
                'provider' => $provider,
                'status' => 'success',
                'error_message' => null,
                'translated_at' => now(),
            ])->save();

            return $translatedText;
        } catch (Throwable $exception) {
            $record->forceFill([
                'source_text' => $text,
                'translated_text' => null,
                'provider' => (string) config(
                    'services.translation.provider',
                    'libretranslate'
                ),
                'status' => 'failed',
                'error_message' => mb_substr(
                    $exception->getMessage(),
                    0,
                    2000
                ),
                'translated_at' => null,
            ])->save();

            Log::warning(
                'Terjemahan otomatis SupplyGuard gagal.',
                [
                    'source_locale' => $sourceLocale,
                    'target_locale' => $targetLocale,
                    'provider' => config(
                        'services.translation.provider'
                    ),
                    'message' => $exception->getMessage(),
                ]
            );

            return $text;
        }
    }

    private function translateWithLibreTranslate(
        string $text,
        string $sourceLocale,
        string $targetLocale
    ): string {
        $baseUrl = rtrim(
            (string) config(
                'services.translation.libretranslate.url'
            ),
            '/'
        );

        if ($baseUrl === '') {
            throw new RuntimeException(
                'LIBRETRANSLATE_URL belum diatur.'
            );
        }

        $payload = [
            'q' => $text,
            'source' => $sourceLocale,
            'target' => $targetLocale,
            'format' => 'text',
        ];

        $apiKey = trim(
            (string) config(
                'services.translation.libretranslate.key'
            )
        );

        if ($apiKey !== '') {
            $payload['api_key'] = $apiKey;
        }

        $response = Http::acceptJson()
            ->asJson()
            ->connectTimeout(10)
            ->timeout(
                (int) config(
                    'services.translation.timeout',
                    30
                )
            )
            ->retry(2, 750, null, false)
            ->post(
                $baseUrl.'/translate',
                $payload
            );

        if (! $response->successful()) {
            throw new RuntimeException(
                'LibreTranslate gagal: HTTP '
                .$response->status()
                .' - '
                .mb_substr(
                    (string) $response->body(),
                    0,
                    600
                )
            );
        }

        $translated = data_get(
            $response->json(),
            'translatedText'
        );

        if (! is_string($translated)) {
            throw new RuntimeException(
                'Format respons LibreTranslate tidak valid.'
            );
        }

        return $translated;
    }

    private function translateBatchWithProvider(
        array $texts,
        string $sourceLocale,
        string $targetLocale
    ): array {
        $provider = strtolower((string) config(
            'services.translation.provider',
            'libretranslate'
        ));

        $endpoint = $provider === 'google'
            ? (string) config('services.translation.google.url')
                .'?key='.urlencode((string) config('services.translation.google.key'))
            : rtrim((string) config('services.translation.libretranslate.url'), '/')
                .'/translate';

        if ($provider === 'google' && trim((string) config('services.translation.google.key')) === '') {
            throw new RuntimeException('GOOGLE_TRANSLATE_API_KEY belum diatur.');
        }

        $payload = [
            'q' => $texts,
            'source' => $sourceLocale,
            'target' => $targetLocale,
            'format' => 'text',
        ];

        if ($provider === 'libretranslate') {
            $key = trim((string) config('services.translation.libretranslate.key'));

            if ($key !== '') {
                $payload['api_key'] = $key;
            }
        }

        $response = Http::acceptJson()->asJson()
            ->connectTimeout(3)
            ->timeout(min(10, (int) config('services.translation.timeout', 10)))
            ->post($endpoint, $payload);

        if (! $response->successful()) {
            throw new RuntimeException(
                "{$provider} batch gagal: HTTP {$response->status()}"
            );
        }

        if ($provider === 'google') {
            return collect(data_get($response->json(), 'data.translations', []))
                ->map(fn ($item) => html_entity_decode(
                    (string) data_get($item, 'translatedText', ''),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                ))
                ->all();
        }

        $translated = data_get($response->json(), 'translatedText', []);
        $translated = is_array($translated) ? array_values($translated) : [$translated];

        if (count($translated) !== count($texts)) {
            throw new RuntimeException('Jumlah hasil batch LibreTranslate tidak sesuai.');
        }

        return $translated;
    }

    private function translateWithGoogle(
        string $text,
        string $sourceLocale,
        string $targetLocale
    ): string {
        $apiKey = trim(
            (string) config(
                'services.translation.google.key'
            )
        );

        if ($apiKey === '') {
            throw new RuntimeException(
                'GOOGLE_TRANSLATE_API_KEY belum diatur.'
            );
        }

        $endpoint = (string) config(
            'services.translation.google.url',
            'https://translation.googleapis.com/language/translate/v2'
        );

        $response = Http::acceptJson()
            ->asForm()
            ->connectTimeout(10)
            ->timeout(
                (int) config(
                    'services.translation.timeout',
                    30
                )
            )
            ->retry(2, 750, null, false)
            ->post(
                $endpoint.'?key='.urlencode($apiKey),
                [
                    'q' => $text,
                    'source' => $sourceLocale,
                    'target' => $targetLocale,
                    'format' => 'text',
                ]
            );

        if (! $response->successful()) {
            throw new RuntimeException(
                'Google Translation gagal: HTTP '
                .$response->status()
                .' - '
                .mb_substr(
                    (string) $response->body(),
                    0,
                    600
                )
            );
        }

        $translated = data_get(
            $response->json(),
            'data.translations.0.translatedText'
        );

        if (! is_string($translated)) {
            throw new RuntimeException(
                'Format respons Google Translation tidak valid.'
            );
        }

        return html_entity_decode(
            $translated,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
    }

    private function normalizeLocale(
        string $locale
    ): string {
        $locale = strtolower(
            trim($locale)
        );

        $parts = preg_split(
            '/[-_]/',
            $locale
        );

        $baseLocale = (string) (
            $parts[0]
            ?? 'en'
        );

        return preg_match(
            '/^[a-z]{2,3}$/',
            $baseLocale
        ) === 1
            ? $baseLocale
            : 'en';
    }

    private function normalizeTargetLocale(string $locale): string
    {
        $normalized = $this->normalizeLocale($locale);

        if (
            $normalized === 'zh'
            && strtolower((string) config('services.translation.provider')) === 'libretranslate'
        ) {
            return 'zh-Hans';
        }

        return $normalized;
    }
}
