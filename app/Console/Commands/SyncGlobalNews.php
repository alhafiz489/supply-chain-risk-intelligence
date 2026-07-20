<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Services\GNewsGlobalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

class SyncGlobalNews extends Command
{
    protected $signature = 'supplyguard:sync-global-news
                            {--limit= : Number of countries to process}
                            {--offset= : Start offset; automatic rotation when omitted}
                            {--days=7 : Historical search period, maximum 30 days}
                            {--max= : Maximum articles per country}
                            {--reset : Reset automatic country rotation}';

    protected $description =
        'Synchronize global supply-chain news from GNews';

    public function handle(
        GNewsGlobalService $newsService
    ): int {
        try {
            if ($this->option('reset')) {
                $this->resetStoredOffset();

                $this->info(
                    'Rotasi negara berita berhasil direset.'
                );

                return self::SUCCESS;
            }

            $totalCountries = Country::count();

            if ($totalCountries === 0) {
                $this->warn(
                    'Data negara masih kosong.'
                );

                return self::SUCCESS;
            }

            $defaultLimit = max(
                1,
                (int) config(
                    'services.gnews.batch_size',
                    4
                )
            );

            $limit =
                $this->option('limit') !== null
                    ? max(
                        1,
                        (int) $this->option('limit')
                    )
                    : $defaultLimit;

            $days = max(
                1,
                min(
                    30,
                    (int) $this->option('days')
                )
            );

            $maxArticles =
                $this->option('max') !== null
                    ? max(
                        1,
                        (int) $this->option('max')
                    )
                    : max(
                        1,
                        (int) config(
                            'services.gnews.max_articles',
                            10
                        )
                    );

            $offset =
                $this->option('offset') !== null
                    ? max(
                        0,
                        (int) $this->option('offset')
                    )
                    : $this->storedOffset();

            if ($offset >= $totalCountries) {
                $offset = 0;
            }

            $countries = Country::query()
                ->orderBy('id')
                ->get()
                ->slice($offset, $limit)
                ->values();

            if ($countries->isEmpty()) {
                $offset = 0;

                $countries = Country::query()
                    ->orderBy('id')
                    ->limit($limit)
                    ->get();
            }

            $this->info(
                "Sinkronisasi berita dimulai. Offset: {$offset}, negara: {$countries->count()}, periode: {$days} hari."
            );

            $result =
                $newsService->syncCountries(
                    $countries,
                    $days,
                    $maxArticles
                );

            /*
            |--------------------------------------------------------------------------
            | Offset hanya maju berdasarkan negara yang benar-benar berhasil
            |--------------------------------------------------------------------------
            |
            | Bila negara ketiga terkena 429, offset hanya maju dua posisi. Negara
            | yang gagal akan menjadi negara pertama pada batch berikutnya.
            |
            */

            if (
                $result['countries_processed'] > 0
            ) {
                $nextOffset =
                    (
                        $offset
                        + $result[
                            'countries_processed'
                        ]
                    )
                    % $totalCountries;

                $this->storeOffset($nextOffset);
            } else {
                $nextOffset = $offset;
            }

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    [
                        'Negara diminta',
                        $result[
                            'countries_requested'
                        ],
                    ],
                    [
                        'Negara dicoba',
                        $result[
                            'countries_attempted'
                        ],
                    ],
                    [
                        'Negara berhasil diproses',
                        $result[
                            'countries_processed'
                        ],
                    ],
                    [
                        'Negara gagal',
                        $result[
                            'countries_failed'
                        ],
                    ],
                    [
                        'Negara ditunda',
                        $result[
                            'countries_deferred'
                        ],
                    ],
                    [
                        'Artikel diterima',
                        $result[
                            'articles_received'
                        ],
                    ],
                    [
                        'Artikel baru',
                        $result['created'],
                    ],
                    [
                        'Artikel diperbarui',
                        $result['updated'],
                    ],
                    [
                        'Offset berikutnya',
                        $nextOffset,
                    ],
                ]
            );

            foreach (
                $result['failed_countries']
                as $failure
            ) {
                $this->warn(
                    $failure['country']
                    .': '
                    .$failure['message']
                );
            }

            return $result[
                'countries_processed'
            ] > 0
                ? self::SUCCESS
                : self::FAILURE;
        } catch (Throwable $exception) {
            report($exception);

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }
    }

    private function storedOffset(): int
    {
        $path = $this->offsetPath();

        if (! File::exists($path)) {
            return 0;
        }

        $payload = json_decode(
            File::get($path),
            true
        );

        return max(
            0,
            (int) ($payload['offset'] ?? 0)
        );
    }

    private function storeOffset(
        int $offset
    ): void {
        $path = $this->offsetPath();

        File::ensureDirectoryExists(
            dirname($path)
        );

        File::put(
            $path,
            json_encode(
                [
                    'offset' => max(
                        0,
                        $offset
                    ),
                    'updated_at' =>
                        now()->toIso8601String(),
                ],
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
            )
        );
    }

    private function resetStoredOffset(): void
    {
        $path = $this->offsetPath();

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function offsetPath(): string
    {
        return storage_path(
            'app/supplyguard/global-news-offset.json'
        );
    }
}