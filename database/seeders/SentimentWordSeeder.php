<?php

namespace Database\Seeders;

use App\Models\SentimentWord;
use Illuminate\Database\Seeder;

class SentimentWordSeeder extends Seeder
{
    public function run(): void
    {
        $positiveWords = [
            'meningkat' => 2,
            'stabil' => 1,
            'aman' => 2,
            'lancar' => 2,
            'baik' => 1,
            'positif' => 2,
            'tumbuh' => 2,
            'pertumbuhan' => 2,
            'pulih' => 2,
            'pemulihan' => 2,
            'berhasil' => 2,
            'efisien' => 2,
            'membaik' => 2,
            'keuntungan' => 2,
            'peluang' => 1,
            'surplus' => 2,
            'naik' => 1,
            'terkendali' => 2,
            'normal' => 1,
            'optimal' => 2,
        ];

        $negativeWords = [
            'menurun' => 2,
            'turun' => 1,
            'krisis' => 3,
            'macet' => 2,
            'kemacetan' => 2,
            'terlambat' => 2,
            'keterlambatan' => 2,
            'gangguan' => 2,
            'risiko' => 1,
            'bahaya' => 2,
            'buruk' => 2,
            'negatif' => 2,
            'gagal' => 2,
            'kerugian' => 2,
            'konflik' => 3,
            'bencana' => 3,
            'banjir' => 2,
            'badai' => 2,
            'inflasi' => 1,
            'hambatan' => 2,
            'terganggu' => 2,
            'kelangkaan' => 2,
            'defisit' => 2,
            'ancaman' => 2,
            'darurat' => 3,
        ];

        foreach ($positiveWords as $word => $weight) {
            SentimentWord::updateOrCreate(
                [
                    'word' => $word,
                    'type' => 'positive',
                ],
                [
                    'weight' => $weight,
                    'is_active' => true,
                ]
            );
        }

        foreach ($negativeWords as $word => $weight) {
            SentimentWord::updateOrCreate(
                [
                    'word' => $word,
                    'type' => 'negative',
                ],
                [
                    'weight' => $weight,
                    'is_active' => true,
                ]
            );
        }
    }
}