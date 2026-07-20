<?php

namespace App\Services;

use App\Models\SentimentWord;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SentimentAnalysisService
{
    private ?Collection $sentimentWords = null;

    /**
     * Kamus bawaan berfungsi sebagai cadangan ketika kamus database
     * belum lengkap. Kamus database tetap diprioritaskan dan dapat
     * dikelola melalui halaman admin.
     */
    private const DEFAULT_POSITIVE_WORDS = [
        'advance' => 1,
        'agreement' => 2,
        'approve' => 1,
        'approved' => 1,
        'benefit' => 1,
        'boom' => 2,
        'boost' => 2,
        'collaboration' => 1,
        'cooperation' => 1,
        'deal' => 1,
        'efficient' => 2,
        'expand' => 1,
        'expanded' => 1,
        'expansion' => 2,
        'gain' => 1,
        'gains' => 1,
        'growth' => 2,
        'improve' => 1,
        'improved' => 1,
        'improvement' => 2,
        'increase' => 1,
        'increased' => 1,
        'investment' => 1,
        'leading' => 1,
        'opportunity' => 1,
        'partnership' => 2,
        'progress' => 2,
        'protect' => 1,
        'protected' => 1,
        'recovery' => 2,
        'resilient' => 2,
        'resilience' => 2,
        'rise' => 1,
        'rises' => 1,
        'rising' => 1,
        'secure' => 1,
        'stable' => 2,
        'strengthen' => 2,
        'strengthened' => 2,
        'strengthening' => 2,
        'strong' => 2,
        'success' => 2,
        'successful' => 2,
        'surplus' => 2,
        'upgrade' => 1,
        'upgraded' => 1,

        'berhasil' => 2,
        'efisien' => 2,
        'kemajuan' => 2,
        'meningkat' => 1,
        'pemulihan' => 2,
        'pertumbuhan' => 2,
        'stabil' => 2,
        'tangguh' => 2,
    ];

    private const DEFAULT_NEGATIVE_WORDS = [
        'attack' => 3,
        'attacks' => 3,
        'bankrupt' => 3,
        'bankruptcy' => 3,
        'bottleneck' => 2,
        'clash' => 2,
        'conflict' => 3,
        'congestion' => 2,
        'crisis' => 3,
        'decline' => 2,
        'declined' => 2,
        'delay' => 2,
        'delayed' => 2,
        'delays' => 2,
        'deficit' => 2,
        'disaster' => 3,
        'disruption' => 3,
        'disruptions' => 3,
        'drought' => 3,
        'drop' => 2,
        'earthquake' => 3,
        'embargo' => 3,
        'emergency' => 2,
        'extreme' => 1,
        'fail' => 2,
        'failed' => 2,
        'failure' => 2,
        'flood' => 3,
        'flooded' => 3,
        'flooding' => 3,
        'halt' => 3,
        'inflation' => 1,
        'loss' => 2,
        'losses' => 2,
        'money laundering' => 3,
        'outage' => 3,
        'risk' => 1,
        'risks' => 1,
        'sanction' => 3,
        'sanctions' => 3,
        'shortage' => 3,
        'shortages' => 3,
        'shock' => 3,
        'slow' => 1,
        'slowdown' => 2,
        'slump' => 2,
        'storm' => 3,
        'strike' => 2,
        'threat' => 2,
        'threats' => 2,
        'trade deficit' => 3,
        'uncertainty' => 2,
        'war' => 3,
        'warning' => 2,
        'weak' => 2,
        'weaken' => 2,
        'wildfire' => 3,

        'ancaman' => 2,
        'banjir' => 3,
        'bencana' => 3,
        'gagal' => 2,
        'gangguan' => 3,
        'keterlambatan' => 2,
        'konflik' => 3,
        'krisis' => 3,
        'kerugian' => 2,
        'kekurangan' => 3,
        'macet' => 2,
        'menurun' => 2,
        'perang' => 3,
        'risiko' => 1,
    ];

    public function analyze(string $text): array
    {
        $normalizedText = $this->normalize($text);

        $tokens = preg_split(
            '/[^\p{L}\p{N}_]+/u',
            $normalizedText,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $frequencies = array_count_values(
            $tokens ?: []
        );

        $positiveScore = 0;
        $negativeScore = 0;
        $matchedWords = [];

        foreach ($this->dictionary() as $entry) {
            $word = $entry['word'];
            $type = $entry['type'];
            $weight = $entry['weight'];

            $matchCount = str_contains($word, ' ')
                ? substr_count($normalizedText, $word)
                : ($frequencies[$word] ?? 0);

            if ($matchCount < 1) {
                continue;
            }

            $score = $matchCount * $weight;

            if ($type === 'positive') {
                $positiveScore += $score;
            } elseif ($type === 'negative') {
                $negativeScore += $score;
            }

            $matchedWords[] = [
                'word' => $word,
                'type' => $type,
                'count' => $matchCount,
                'score' => $score,
            ];
        }

        $difference = $positiveScore - $negativeScore;

        $sentiment = match (true) {
            $difference >= 1 => 'Positive',
            $difference <= -1 => 'Negative',
            default => 'Neutral',
        };

        return [
            'sentiment' => $sentiment,
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore,
            'matched_words' => $matchedWords,
        ];
    }

    public function refreshDictionary(): void
    {
        $this->sentimentWords = null;
    }

    private function dictionary(): Collection
    {
        $entries = collect();

        foreach (self::DEFAULT_POSITIVE_WORDS as $word => $weight) {
            $entries->push([
                'word' => $word,
                'type' => 'positive',
                'weight' => $weight,
                'source' => 'default',
            ]);
        }

        foreach (self::DEFAULT_NEGATIVE_WORDS as $word => $weight) {
            $entries->push([
                'word' => $word,
                'type' => 'negative',
                'weight' => $weight,
                'source' => 'default',
            ]);
        }

        foreach ($this->databaseWords() as $sentimentWord) {
            $word = $this->normalize(
                (string) $sentimentWord->word
            );

            if ($word === '') {
                continue;
            }

            $entries->push([
                'word' => $word,
                'type' => $sentimentWord->type,
                'weight' => max(
                    1,
                    (int) $sentimentWord->weight
                ),
                'source' => 'database',
            ]);
        }

        /*
         * Entri database mengalahkan kamus bawaan jika kata dan jenis sama.
         */
        return $entries
            ->reverse()
            ->unique(
                fn (array $entry) =>
                    $entry['type'].'|'.$entry['word']
            )
            ->reverse()
            ->values();
    }

    private function databaseWords(): Collection
    {
        if ($this->sentimentWords !== null) {
            return $this->sentimentWords;
        }

        return $this->sentimentWords =
            SentimentWord::query()
                ->where('is_active', true)
                ->get([
                    'word',
                    'type',
                    'weight',
                ]);
    }

    private function normalize(string $text): string
    {
        return trim(
            preg_replace(
                '/\s+/u',
                ' ',
                Str::lower(strip_tags($text))
            ) ?? ''
        );
    }
}