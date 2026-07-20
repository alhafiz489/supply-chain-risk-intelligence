<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Services\OpenMeteoWeatherService;
use App\Services\RiskScoringService;
use Illuminate\Console\Command;

class SyncWeatherData extends Command
{
    protected $signature = 'supplyguard:sync-weather
                            {--limit=0 : Maximum countries to process}
                            {--offset=0 : Number of countries to skip}
                            {--recalculate : Recalculate risk scores after synchronization}';

    protected $description =
        'Synchronize country weather data from Open-Meteo';

    public function handle(
        OpenMeteoWeatherService $weatherService,
        RiskScoringService $riskScoring
    ): int {
        $limit = max(0, (int) $this->option('limit'));
        $offset = max(0, (int) $this->option('offset'));

        /*
        |--------------------------------------------------------------------------
        | Ambil data tanpa OFFSET SQL
        |--------------------------------------------------------------------------
        |
        | MariaDB tidak menerima query "OFFSET 0" tanpa LIMIT. Karena jumlah
        | negara tidak terlalu besar, offset dan limit diterapkan pada Collection
        | agar kompatibel dengan MySQL maupun MariaDB.
        |
        */

        $countries = Country::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('id')
            ->get();

        if ($offset > 0) {
            $countries = $countries
                ->slice($offset)
                ->values();
        }

        if ($limit > 0) {
            $countries = $countries
                ->take($limit)
                ->values();
        }

        if ($countries->isEmpty()) {
            $this->warn(
                'Tidak ada negara dengan koordinat yang dapat diproses.'
            );

            return self::SUCCESS;
        }

        $this->info(
            "Sinkronisasi cuaca dimulai untuk {$countries->count()} negara."
        );

        $result = $weatherService->syncCountries($countries);

        $recalculated = 0;

        if (
            $this->option('recalculate')
            && $result['updated_country_ids'] !== []
        ) {
            $updatedCountries = Country::query()
                ->whereIn('id', $result['updated_country_ids'])
                ->orderBy('id')
                ->get();

            $bar = $this->output->createProgressBar(
                $updatedCountries->count()
            );

            $bar->start();

            foreach ($updatedCountries as $country) {
                $riskScoring->calculateAndSave($country);
                $recalculated++;
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
        }

        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Cuaca berhasil diperbarui', $result['processed']],
                ['Gagal', $result['failed']],
                ['Dilewati tanpa koordinat', $result['skipped']],
                ['Risiko dihitung ulang', $recalculated],
            ]
        );

        if ($result['failed_countries'] !== []) {
            $this->warn(
                'Negara gagal: '
                .implode(', ', $result['failed_countries'])
            );
        }

        return $result['processed'] > 0
            ? self::SUCCESS
            : self::FAILURE;
    }
}