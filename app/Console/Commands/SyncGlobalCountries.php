<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Services\OpenMeteoWeatherService;
use App\Services\RestCountriesService;
use Illuminate\Console\Command;
use Throwable;

class SyncGlobalCountries extends Command
{
    protected $signature = 'supplyguard:sync-countries
                            {--weather : Synchronize weather after country import}';

    protected $description =
        'Synchronize all global countries and territories';

    public function handle(
        RestCountriesService $countryService,
        OpenMeteoWeatherService $weatherService
    ): int {
        try {
            $this->info(
                'Sinkronisasi master negara global dimulai.'
            );

            $result = $countryService->syncAll();

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    ['Negara/wilayah baru', $result['created']],
                    ['Negara/wilayah diperbarui', $result['updated']],
                    ['Data dilewati', $result['skipped']],
                    ['Halaman API diproses', $result['pages']],
                    [
                        'Total negara/wilayah di database',
                        $result['total_in_database'],
                    ],
                ]
            );

            if ($this->option('weather')) {
                $countries = Country::query()
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->orderBy('id')
                    ->get();

                $this->newLine();
                $this->info(
                    "Sinkronisasi cuaca dimulai untuk {$countries->count()} negara/wilayah."
                );

                $weatherResult = $weatherService
                    ->syncCountries($countries);

                $this->table(
                    ['Keterangan Cuaca', 'Jumlah'],
                    [
                        [
                            'Berhasil diperbarui',
                            $weatherResult['processed'],
                        ],
                        ['Gagal', $weatherResult['failed']],
                        ['Dilewati', $weatherResult['skipped']],
                    ]
                );
            }

            $this->info(
                'Sinkronisasi global selesai tanpa menghapus data lama.'
            );

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}