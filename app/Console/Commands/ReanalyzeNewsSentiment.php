<?php

namespace App\Console\Commands;

use App\Models\NewsCache;
use App\Services\SentimentAnalysisService;
use Illuminate\Console\Command;
use Throwable;

class ReanalyzeNewsSentiment extends Command
{
    protected $signature = 'supplyguard:reanalyze-news
                            {--all : Include manually created and demo news}
                            {--limit=0 : Maximum number of news records to process}';

    protected $description =
        'Reanalyze existing news sentiment using the latest dictionary';

    public function handle(
        SentimentAnalysisService $sentimentAnalysis
    ): int {
        $limit = max(0, (int) $this->option('limit'));

        $query = NewsCache::query()
            ->orderBy('id');

        if (! $this->option('all')) {
            $query->whereNotNull('external_id');
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $newsItems = $query->get();

        if ($newsItems->isEmpty()) {
            $this->warn(
                'Tidak ada berita yang dapat dianalisis ulang.'
            );

            return self::SUCCESS;
        }

        $updated = 0;
        $failed = 0;
        $positive = 0;
        $neutral = 0;
        $negative = 0;

        $bar = $this->output->createProgressBar(
            $newsItems->count()
        );

        $bar->start();

        foreach ($newsItems as $news) {
            try {
                $analysis = $sentimentAnalysis->analyze(
                    trim(
                        $news->title
                        .' '
                        .($news->summary ?? '')
                    )
                );

                $news->update([
                    'sentiment' => $analysis['sentiment'],
                    'positive_score' =>
                        $analysis['positive_score'],
                    'negative_score' =>
                        $analysis['negative_score'],
                    'matched_words' =>
                        $analysis['matched_words'],
                ]);

                match ($analysis['sentiment']) {
                    'Positive' => $positive++,
                    'Negative' => $negative++,
                    default => $neutral++,
                };

                $updated++;
            } catch (Throwable $exception) {
                report($exception);
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Berita diperbarui', $updated],
                ['Positif', $positive],
                ['Netral', $neutral],
                ['Negatif', $negative],
                ['Gagal', $failed],
            ]
        );

        return $failed === 0
            ? self::SUCCESS
            : self::FAILURE;
    }
}