<?php

namespace App\Services;

use App\Models\Country;
use App\Models\NewsCache;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class GNewsGlobalService
{
    private const ENDPOINT =
        'https://gnews.io/api/v4/search';

    private const REQUEST_DELAY_MICROSECONDS =
        2_000_000;

    private const MAX_RATE_LIMIT_RETRIES = 3;

    public function __construct(
        private readonly SentimentAnalysisService
            $sentimentAnalysis
    ) {
    }

    public function syncCountries(
        Collection $countries,
        int $days = 7,
        int $maxArticles = 10
    ): array {
        $days = max(1, min($days, 30));
        $maxArticles = max(1, min($maxArticles, 100));

        $successfulCountries = 0;
        $attemptedCountries = 0;
        $failedCountriesCount = 0;
        $created = 0;
        $updated = 0;
        $articlesReceived = 0;
        $failedCountries = [];

        foreach ($countries as $country) {
            $attemptedCountries++;

            try {
                $articles = $this->fetchCountryArticles(
                    $country,
                    $days,
                    $maxArticles
                );

                $articlesReceived += count($articles);

                foreach ($articles as $article) {
                    $result = $this->storeArticle(
                        $country,
                        $article
                    );

                    if ($result === 'created') {
                        $created++;
                    } elseif ($result === 'updated') {
                        $updated++;
                    }
                }

                $successfulCountries++;

                usleep(
                    self::REQUEST_DELAY_MICROSECONDS
                );
            } catch (Throwable $exception) {
                report($exception);

                $failedCountriesCount++;

                $failedCountries[] = [
                    'country' => $country->name,
                    'message' => $exception->getMessage(),
                ];

                $message = Str::lower(
                    $exception->getMessage()
                );

                /*
                |--------------------------------------------------------------------------
                | Hentikan batch pada masalah autentikasi, kuota, atau rate limit
                |--------------------------------------------------------------------------
                |
                | Negara yang gagal tidak dihitung sebagai berhasil. Karena command
                | hanya memajukan offset berdasarkan jumlah negara yang sukses,
                | negara tersebut otomatis dicoba kembali pada batch berikutnya.
                |
                */

                if (
                    str_contains($message, 'http status: 401')
                    || str_contains($message, 'http status: 403')
                    || str_contains($message, 'http status: 429')
                    || str_contains($message, 'api key')
                    || str_contains($message, 'quota')
                    || str_contains($message, 'too many requests')
                ) {
                    break;
                }
            }
        }

        return [
            'countries_requested' =>
                $countries->count(),

            'countries_attempted' =>
                $attemptedCountries,

            'countries_processed' =>
                $successfulCountries,

            'countries_failed' =>
                $failedCountriesCount,

            'countries_deferred' =>
                max(
                    0,
                    $countries->count()
                    - $attemptedCountries
                ),

            'articles_received' =>
                $articlesReceived,

            'created' => $created,
            'updated' => $updated,
            'failed_countries' => $failedCountries,
        ];
    }

    private function fetchCountryArticles(
        Country $country,
        int $days,
        int $maxArticles
    ): array {
        $apiKey = trim(
            (string) config('services.gnews.key')
        );

        if ($apiKey === '') {
            throw new RuntimeException(
                'GNEWS_API_KEY belum diisi pada file .env.'
            );
        }

        $query = sprintf(
            '"%s" AND ("supply chain" OR logistics OR shipping OR port OR trade OR economy OR inflation OR currency OR weather OR conflict)',
            str_replace('"', '', $country->name)
        );

        $response = $this->sendRequestWithBackoff([
            'q' => $query,
            'lang' => (string) config(
                'services.gnews.language',
                'en'
            ),
            'max' => $maxArticles,
            'sortby' => 'publishedAt',
            'from' => now()
                ->subDays($days)
                ->utc()
                ->format('Y-m-d\TH:i:s\Z'),
            'to' => now()
                ->utc()
                ->format('Y-m-d\TH:i:s\Z'),
            'apikey' => $apiKey,
        ]);

        $this->ensureSuccessful($response);

        $articles = $response->json(
            'articles',
            []
        );

        if (! is_array($articles)) {
            throw new RuntimeException(
                'Format artikel GNews tidak valid.'
            );
        }

        return $articles;
    }

    private function sendRequestWithBackoff(
        array $parameters
    ): Response {
        $response = null;

        for (
            $attempt = 1;
            $attempt <= self::MAX_RATE_LIMIT_RETRIES;
            $attempt++
        ) {
            $response = Http::acceptJson()
                ->connectTimeout(15)
                ->timeout(60)
                ->get(
                    self::ENDPOINT,
                    $parameters
                );

            if ($response->status() !== 429) {
                return $response;
            }

            if (
                $attempt
                >= self::MAX_RATE_LIMIT_RETRIES
            ) {
                break;
            }

            $retryAfter = (int) (
                $response->header('Retry-After')
                ?? 0
            );

            $waitSeconds = $retryAfter > 0
                ? min($retryAfter, 30)
                : min(5 * $attempt, 15);

            sleep($waitSeconds);
        }

        if ($response === null) {
            throw new RuntimeException(
                'GNews tidak memberikan respons.'
            );
        }

        return $response;
    }

    private function storeArticle(
        Country $country,
        array $article
    ): string {
        $url = trim(
            (string) ($article['url'] ?? '')
        );

        $externalId = trim(
            (string) ($article['id'] ?? '')
        );

        if ($externalId === '') {
            $externalId = hash(
                'sha256',
                $url !== ''
                    ? $url
                    : json_encode($article)
            );
        }

        $title = trim(
            (string) ($article['title'] ?? '')
        );

        if ($title === '') {
            return 'skipped';
        }

        $description = trim(
            (string) (
                $article['description']
                ?? ''
            )
        );

        $content = trim(
            (string) ($article['content'] ?? '')
        );

        $analysis =
            $this->sentimentAnalysis->analyze(
                trim(
                    $title
                    .' '
                    .$description
                    .' '
                    .$content
                )
            );

        $publishedAt =
            $this->parsePublishedAt(
                $article['publishedAt']
                ?? null
            );

        $news = NewsCache::firstOrNew([
            'external_id' => $externalId,
        ]);

        $wasExisting = $news->exists;

        $news->fill([
            'country_id' => $country->id,
            'title' => Str::limit(
                $title,
                255,
                ''
            ),
            'category' => $this->detectCategory(
                $title.' '.$description
            ),
            'sentiment' =>
                $analysis['sentiment'],
            'positive_score' =>
                $analysis['positive_score'],
            'negative_score' =>
                $analysis['negative_score'],
            'matched_words' =>
                $analysis['matched_words'],
            'summary' => Str::limit(
                $description !== ''
                    ? $description
                    : $content,
                2000
            ),
            'url' => $url !== ''
                ? $url
                : null,
            'image_url' =>
                $this->nullableString(
                    $article['image'] ?? null
                ),
            'source_name' =>
                $this->nullableString(
                    data_get(
                        $article,
                        'source.name'
                    )
                ),
            'source_url' =>
                $this->nullableString(
                    data_get(
                        $article,
                        'source.url'
                    )
                ),
            'language' => (string) config(
                'services.gnews.language',
                'en'
            ),
            'published_date' =>
                $publishedAt?->toDateString(),
            'published_at' => $publishedAt,
            'synced_at' => now(),
        ]);

        $news->save();

        return $wasExisting
            ? 'updated'
            : 'created';
    }

    private function parsePublishedAt(
        mixed $value
    ): ?Carbon {
        if (
            ! is_string($value)
            || trim($value) === ''
        ) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    private function detectCategory(
        string $text
    ): string {
        $text = Str::lower($text);

        return match (true) {
            Str::contains(
                $text,
                [
                    'port',
                    'harbor',
                    'shipping',
                    'vessel',
                    'freight',
                    'logistics',
                ]
            ) => 'Logistics',

            Str::contains(
                $text,
                [
                    'weather',
                    'storm',
                    'flood',
                    'rain',
                    'drought',
                    'earthquake',
                    'wildfire',
                ]
            ) => 'Weather',

            Str::contains(
                $text,
                [
                    'war',
                    'conflict',
                    'attack',
                    'sanction',
                    'political',
                    'election',
                ]
            ) => 'Geopolitics',

            Str::contains(
                $text,
                [
                    'currency',
                    'exchange rate',
                    'inflation',
                    'interest rate',
                    'central bank',
                ]
            ) => 'Finance',

            default => 'Economy',
        };
    }

    private function nullableString(
        mixed $value
    ): ?string {
        $value = trim((string) $value);

        return $value === ''
            ? null
            : $value;
    }

    private function ensureSuccessful(
        Response $response
    ): void {
        if ($response->successful()) {
            return;
        }

        $message = data_get(
            $response->json(),
            'errors.0',
            data_get(
                $response->json(),
                'message',
                'GNews API gagal merespons.'
            )
        );

        if (is_array($message)) {
            $message = json_encode($message);
        }

        throw new RuntimeException(
            "{$message} HTTP status: {$response->status()}."
        );
    }
}