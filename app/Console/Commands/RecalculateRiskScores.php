<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Services\RiskScoringService;
use Illuminate\Console\Command;
use Throwable;

class RecalculateRiskScores extends Command
{
    /**
     * Nama dan opsi perintah Artisan.
     */
    protected $signature = 'supplyguard:recalculate-risks
                            {--country= : Hitung hanya satu negara berdasarkan ID}';

    /**
     * Deskripsi perintah Artisan.
     */
    protected $description = 'Menghitung ulang dan menyimpan skor risiko negara SupplyGuard';

    public function handle(RiskScoringService $riskScoring): int
    {
        $countryId = $this->option('country');

        if ($countryId !== null) {
            return $this->recalculateSingleCountry(
                $riskScoring,
                (int) $countryId
            );
        }

        return $this->recalculateAllCountries($riskScoring);
    }

    private function recalculateSingleCountry(
        RiskScoringService $riskScoring,
        int $countryId
    ): int {
        $country = Country::query()->find($countryId);

        if (! $country) {
            $this->error(
                "Negara dengan ID {$countryId} tidak ditemukan."
            );

            return self::FAILURE;
        }

        try {
            $result = $riskScoring->calculateAndSave($country);

            $this->newLine();
            $this->info('Perhitungan risiko berhasil disimpan.');

            $this->table(
                ['Negara', 'Skor', 'Kategori', 'Kelengkapan', 'Status Data'],
                [[
                    $country->name,
                    $result['total_score'] ?? '-',
                    $result['risk_label'] ?? '-',
                    isset($result['data_completeness_percent'])
                        ? $result['data_completeness_percent'].'%'
                        : '-',
                    $result['risk_data_status'] ?? '-',
                ]]
            );

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->error(
                "Perhitungan {$country->name} gagal: "
                .$exception->getMessage()
            );

            return self::FAILURE;
        }
    }

    private function recalculateAllCountries(
        RiskScoringService $riskScoring
    ): int {
        $total = Country::query()->count();

        if ($total === 0) {
            $this->warn('Belum ada data negara untuk dihitung.');

            return self::SUCCESS;
        }

        $this->info(
            "Menghitung ulang risiko untuk {$total} negara..."
        );

        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        $processed = 0;
        $saved = 0;
        $failed = 0;

        Country::query()
            ->orderBy('id')
            ->chunkById(25, function ($countries) use (
                $riskScoring,
                $progressBar,
                &$processed,
                &$saved,
                &$failed
            ): void {
                foreach ($countries as $country) {
                    try {
                        $riskScoring->calculateAndSave($country);
                        $saved++;
                    } catch (Throwable $exception) {
                        report($exception);
                        $failed++;
                    }

                    $processed++;
                    $progressBar->advance();
                }
            });

        $progressBar->finish();
        $this->newLine(2);

        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Diproses', $processed],
                ['Berhasil disimpan', $saved],
                ['Gagal', $failed],
            ]
        );

        if ($failed > 0) {
            $this->warn(
                'Sebagian perhitungan gagal. Periksa storage/logs/laravel.log.'
            );

            return self::FAILURE;
        }

        $this->info(
            'Perhitungan ulang seluruh risiko selesai.'
        );

        return self::SUCCESS;
    }
}